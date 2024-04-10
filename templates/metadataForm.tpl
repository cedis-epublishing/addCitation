{**
 * plugins/generic/addCitation/templates/metadataForm.tpl
 *
 * Copyright (c) 2021 Universitätsbibliothek Freie Universität Berlin
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * The included template that is hooked into Templates::Submission::SubmissionMetadataForm::AdditionalMetadata.
 *}

{if array_intersect(array(ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR, ROLE_ID_ASSISTANT, ROLE_ID_AUTHOR), (array)$userRoles)}
<div id="addCitation">
	{capture assign="addCitationGridUrl"}{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.addCitation.controllers.grid.AddCitationGridHandler" op="fetchGrid" submissionId=$submissionId escape=false}{/capture}
	{load_url_in_div id="addCitationGridContainer"|uniqid url=$addCitationGridUrl}
</div>
{/if}
