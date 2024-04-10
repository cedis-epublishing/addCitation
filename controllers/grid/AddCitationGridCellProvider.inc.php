<?php

/**
 * @file plugins/generic/addCitation/controllers/grid/AddCitationGridCellProvider.inc.php
 *
 * Copyright (c) 2021 Universitätsbibliothek Freie Universität Berlin
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class AddCitationGridCellProvider
 * @ingroup plugins_generic_addCitation
 *
 * @brief Class for a cell provider to display information about citation items
 */

import('lib.pkp.classes.controllers.grid.GridCellProvider');

class AddCitationGridCellProvider extends GridCellProvider {

	//
	// Template methods from GridCellProvider
	//

	/**
	 * Extracts variables for a given column from a data element
	 * so that they may be assigned to template before rendering.
	 *
	 * @copydoc GridCellProvider::getTemplateVarsFromRowColumn()
	 */
	function getTemplateVarsFromRowColumn($row, $column) {
		$citationItem = $row->getData();
		switch ($column->getId()) {
			case 'style':
				return array('label' => $citationItem['style']);
			case 'citation':
				return array('label' => $citationItem['citation']);
		}
	}
}

?>
