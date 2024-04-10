<?php

/**
 * @file plugins/generic/addCitation/controllers/grid/AddCitationGridHandler.inc.php
 *
 * Copyright (c) 2021 Universitätsbibliothek Freie Universität Berlin
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class AddCitationGridHandler
 * @ingroup plugins_generic_addCitation
 *
 * @brief Handle AddCitation grid requests.
 */

import('lib.pkp.classes.controllers.grid.GridHandler');
import('plugins.generic.addCitation.controllers.grid.AddCitationGridRow');
import('plugins.generic.addCitation.controllers.grid.AddCitationGridCellProvider');

class AddCitationGridHandler extends GridHandler {
	
	static $plugin;

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
		$this->addRoleAssignment(
			array(ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR, ROLE_ID_ASSISTANT, ROLE_ID_AUTHOR),
			array('fetchGrid', 'fetchRow', 'addCitation', 'editCitation', 'deleteCitation','updateCitation')
		);
	}

	//
	// Getters/Setters
	//
	/**
	 * Set the AddCitation plugin.
	 * @param $plugin AddCitationPlugin
	 */
	static function setPlugin($plugin) {
		self::$plugin = $plugin;
	}

	/**
	 * Get the submission associated with this grid.
	 * @return Submission
	 */
	function getSubmission() {	
		return $this->getAuthorizedContextObject(ASSOC_TYPE_SUBMISSION);
	}

	//
	// Overridden template methods
	//

	/**
	 * @copydoc PKPHandler::authorize()
	 */
	function authorize($request, &$args, $roleAssignments) {		
		import('lib.pkp.classes.security.authorization.SubmissionAccessPolicy');
		$this->addPolicy(new SubmissionAccessPolicy($request, $args, $roleAssignments));
		return parent::authorize($request, $args, $roleAssignments);
	}

	/**
	 * @copydoc Gridhandler::initialize()
	 */
	function initialize($request, $args = null) {		
		parent::initialize($request, $args);

		$submission = $this->getSubmission();
		$submissionId = $submission->getId();

		// Set the grid details.
		$this->setTitle('plugins.generic.addCitation.addCitationTitle');
		$this->setEmptyRowText('plugins.generic.addCitation.noneCreated');
		
		$publicationDao = DAORegistry::getDAO('PublicationDAO');
		$publication = $publicationDao->getById($submission->getData('currentPublicationId'));
		
		// Get the items and add the data to the grid
		$gridData = array();
		$citationsAll = json_decode($publication->getData('citation'));
		$row = 1;
		foreach ($citationsAll as $citation) {
			$gridData[$row] = array('style'=>$citation->style,'citation'=>$citation->citation);
			$row++;
		}		
		$this->setGridDataElements($gridData);

		// Add grid-level actions
		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		$this->addAction(
			new LinkAction(
				'addCitation',
				new AjaxModal(
					$router->url($request, null, null, 'addCitation', null, array('submissionId' => $submissionId)),
					__('plugins.generic.addCitation.addCitation'),
					'modal_add_item'
				),
				__('plugins.generic.addCitation.addCitation'),
				'add_item'
			)
		);

		// Columns
		$cellProvider = new AddCitationGridCellProvider();
		$this->addColumn(new GridColumn(
			'style',
			'plugins.generic.addCitation.itemStyle',
			null,
			'controllers/grid/gridCell.tpl',
			$cellProvider,
			array('html' => true)
		));
		$this->addColumn(new GridColumn(
			'citation',
			'plugins.generic.addCitation.itemCitation',
			null,
			'controllers/grid/gridCell.tpl',
			$cellProvider,
			array('html' => true)			
		));


	}

	//
	// Overridden methods from GridHandler
	//
	/**
	 * @copydoc Gridhandler::getRowInstance()
	 */
	function getRowInstance() {		
		return new AddCitationGridRow();
	}


	/**
	 * @copydoc GridHandler::getJSHandler()
	 */
	public function getJSHandler() {
		return '$.pkp.plugins.generic.addCitation.AddCitationGridHandler';
	}
	
	//
	// Public Grid Actions
	//

	/**
	 * An action to add a new citation item
	 * @param $args array Arguments to the request
	 * @param $request PKPRequest
	 */
	function addCitation($args, $request) {		
		// Calling editCitation with an empty ID will add
		// a new citation item.
		return $this->editCitation($args, $request);
	}

	/**
	 * An action to edit a citation
	 * @param $args array Arguments to the request
	 * @param $request PKPRequest
	 * @return string Serialized JSON object
	 */
	function editCitation($args, $request) {		
		$objectId = $request->getUserVar('objectId');		
		$context = $request->getContext();
		$submission = $this->getSubmission();
		$submissionId = $submission->getId();

		$this->setupTemplate($request);

		// Create and present the edit form
		import('plugins.generic.addCitation.controllers.grid.form.AddCitationForm');		
		$addCitationForm = new AddCitationForm(self::$plugin, $context->getId(), $submissionId, $objectId);
		$addCitationForm->initData();
		$json = new JSONMessage(true, $addCitationForm->fetch($request));
		return $json->getString();
	}

	/**
	 * Update a citation
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string Serialized JSON object
	 */
	function updateCitation($args, $request) {				
		$objectId = $request->getUserVar('objectId');		
		$context = $request->getContext();
		$submission = $this->getSubmission();
		$submissionId = $submission->getId();

		$this->setupTemplate($request);

		// Create and populate the form
		import('plugins.generic.addCitation.controllers.grid.form.AddCitationForm');
		$addCitationForm = new AddCitationForm(self::$plugin, $context->getId(), $submissionId, $objectId);
		$addCitationForm->readInputData();
				
		// Validate
		if ($addCitationForm->validate()) {
			// Save
			$addCitationForm->execute();
 			return DAO::getDataChangedEvent($submissionId);
		} else {
			// Present any errors
			$json = new JSONMessage(true, $addCitationForm->fetch($request));
			return $json->getString();
		}
	}

	/**
	 * Delete a citation
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string Serialized JSON object
	 */
	function deleteCitation($args, $request) {
		$objectId = $request->getUserVar('objectId');
		$submission = $this->getSubmission();
		$submissionId = $submission->getId();
		
		$publicationDao = DAORegistry::getDAO('PublicationDAO');
		$publication = $publicationDao->getById($submission->getData('currentPublicationId'));
		
		$citationsAll = json_decode($publication->getData('citation'),true);
		
		unset($citationsAll[$objectId-1]);
		$citationsAll = array_values($citationsAll);
		
		$publication->setData('citation',json_encode($citationsAll));
		$publicationDao->updateObject($publication);		
		return DAO::getDataChangedEvent($submissionId);
		
	}

}

?>
