<?php

/**
 * @file plugins/generic/addCitation/controllers/grid/AddCitationGridRow.inc.php
 *
 * Copyright (c) 2021 Universitätsbibliothek Freie Universität Berlin
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class AddCitationGridRow
 * @ingroup plugins_generic_addCitation
 *
 * @brief Handle AddCitation grid row requests.
 */

import('lib.pkp.classes.controllers.grid.GridRow');

class AddCitationGridRow extends GridRow {

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
	}

	//
	// Overridden template methods
	//
	/**
	 * @copydoc GridRow::initialize()
	 */
	function initialize($request, $template = null) {
		parent::initialize($request, $template);
		$objectId = $this->getId();
		$submissionId = $request->getUserVar('submissionId');

		if (!empty($objectId)) {
			$router = $request->getRouter();

			// Create the "edit" action
			import('lib.pkp.classes.linkAction.request.AjaxModal');
			$this->addAction(
				new LinkAction(
					'editCitationItem',
					new AjaxModal(
						$router->url($request, null, null, 'editCitation', null, array('objectId' => $objectId, 'submissionId' => $submissionId)),
						__('grid.action.edit'),
						'modal_edit',
						true),
					__('grid.action.edit'),
					'edit'
				)
			);

			// Create the "delete" action
			import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
			$this->addAction(
				new LinkAction(
					'delete',
					new RemoteActionConfirmationModal(
						$request->getSession(),
						__('common.confirmDelete'),
						__('grid.action.delete'),
						$router->url($request, null, null, 'deleteCitation', null, array('objectId' => $objectId, 'submissionId' => $submissionId)), 'modal_delete'
					),
					__('grid.action.delete'),
					'delete'
				)
			);
		}
	}

}

?>
