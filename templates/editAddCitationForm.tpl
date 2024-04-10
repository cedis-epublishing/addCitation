{**
 * plugins/generic/addCitation/templates/editAddCitationForm.tpl
 *
 * Copyright (c) 2021 Universitätsbibliothek Freie Universität Berlin
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Form for editing a citation item
 *}

	<script>
		$(function() {ldelim}
			// Attach the form handler.
			$('#addCitationForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
		{rdelim});
	</script>

	{capture assign="actionUrl"}{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.addCitation.controllers.grid.AddCitationGridHandler" op="updateCitation" submissionId=$submissionId escape=false}{/capture}
	
	<form class="pkp_form" id="addCitationForm" method="post" action="{$actionUrl}">
		{csrf}
		{if $objectId}
			<input type="hidden" name="objectId" value="{$objectId|escape}" />
		{/if}		
		{fbvFormArea id="addCitationArea" class="border"}
			{fbvFormSection label="plugins.generic.addCitation.itemStyle" for="style"}
				{fbvElement type="text" id="style" value=$style maxlength="255" inline=true multilingual=false size=$fbvStyles.size.MEDIUM}
			{/fbvFormSection}
			{fbvFormSection label="plugins.generic.addCitation.itemCitation" for="citation"}
				{fbvElement type="textarea" multilingual=false name="citation" id="citation" value=$citation rich=true height=$fbvStyles.height.SHORT}
			{/fbvFormSection}
		{/fbvFormArea}

		{fbvFormSection class="formButtons"}
			{assign var=buttonId value="submitFormButton"|concat:"-"|uniqid}
			{fbvElement type="submit" class="submitFormButton" id=$buttonId label="common.save"}
		{/fbvFormSection}
	</form>


