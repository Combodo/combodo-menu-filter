<?php

namespace Combodo\iTop\MenuFilter;

use Dict;
use iPageUIExtension;
use iTopWebPage;
use utils;

class PageUIExtension implements iPageUIExtension
{
	const MODULE_CODE = "combodo-menu-filter";
	const HTML_PREFIX = "cmf";

	/**
	 * Add content to the North pane
	 *
	 * @param iTopWebPage $oPage The page to insert stuff into.
	 *
	 * @return string The HTML content to add into the page
	 */
	public function GetNorthPaneHtml(iTopWebPage $oPage)
	{
		$oPage->add_saas("env-" . utils::GetCurrentEnvironment() . '/' . self::MODULE_CODE . '/asset/css/style.scss');

		$sFilterInputLabel = Dict::S(self::MODULE_CODE.":UI:Menus:InputLabel");
		$sFilterInputTooltip = Dict::S(self::MODULE_CODE.":UI:Menus:InputTooltip");
		$sEmptyLabel = Dict::S(self::MODULE_CODE.":UI:Menus:EmptyLabel");
		$sPrefix = self::HTML_PREFIX;
		$sFilterTextbox = <<<HTML
			<div id="$sPrefix-filter-textbox">
				<div id="$sPrefix-input-wrapper">
					<input id="$sPrefix-input" name="$sPrefix-input" type="text" placeholder="$sFilterInputLabel" title="$sFilterInputTooltip"/>
					<span id="$sPrefix-filter-icon" class="fa fa-filter"/>
					<span id="$sPrefix-clear-icon" class="fa fa-times"/>
				</div>
				<div id="$sPrefix-empty-label">
					$sEmptyLabel
				</div>
			</div>
HTML;
		$sFilterTextboxJson = json_encode($sFilterTextbox);

		$oPage->add_ready_script(<<<JS
			var r{$sPrefix}KeyTimeout;
			var oMenuElem = $('#inner_menu');
			
			// Add textbox to menu
			oMenuElem.prepend($sFilterTextboxJson);
			// TODO: Put tooltip nicely. oMenuElem.find('#$sPrefix-input').qtip();
			
			// Bind key strokes on input
			$('#$sPrefix-input').on('keyup', function(oEvent){
				var oInputElem = $(this);
				
					//filter mnenus listener
					var sFilterInputValue = oInputElem.val();
					
					if ((sFilterInputValue === "") || (oEvent.key === "Escape")) 
					{
						{$sPrefix}ClearFiltering();		
						{$sPrefix}UpdateEmptyLabel();				
					} 
					else 
					{
						oMenuElem.find('#$sPrefix-input-wrapper').addClass('filtered');
						// Reset throttle timeout on key stroke
						clearTimeout(r{$sPrefix}KeyTimeout);
						r{$sPrefix}KeyTimeout = setTimeout(function(){
							{$sPrefix}FilterMenus(sFilterInputValue);
							{$sPrefix}UpdateEmptyLabel();
						}, 200);
					}
			});
			
			$('#$sPrefix-clear-icon').on('click', function(){
				{$sPrefix}ClearFiltering();
			});
			
			function {$sPrefix}UpdateEmptyLabel()
			{
				if (oMenuElem.find('.navigation-menu-group:visible').length === 0)
				{
					oMenuElem.find('#$sPrefix-empty-label').addClass('displayed');	
				}
				else 
				{
					oMenuElem.find('#$sPrefix-empty-label').removeClass('displayed');
				}
			}
			
			function {$sPrefix}ClearFiltering()
			{
				oMenuElem.find('#$sPrefix-input-wrapper').removeClass('filtered');
				{$sPrefix}DisplayActiveMenu();	
				
				// Empty text box
				$('#$sPrefix-input').val('');
			}
			
			function {$sPrefix}FilterMenus(sFilterInputValue)
			{
				var sFormattedInputValue = sFilterInputValue.toLowerCase().latinise();
				
				// Expand all menu groups
				oMenuElem.find('.navigation-menu-group').show();
				oMenuElem.find('.ui-accordion-content').show();
				
				// Show only matching links
				oMenuElem.find('.navigation-menu-item').hide();
				//oMenuElem.find('.navigation-menu-item:contains("'+sFilterInputValue+'")').show();
				oMenuElem.find('.navigation-menu-item').each(function(){
					if ($(this).text().toLowerCase().latinise().indexOf(sFormattedInputValue) !== -1)
					{
							$(this).show();
					}
				});
								
				// Hide empty menu groups
				oMenuElem.find('.ui-accordion-content').each(function(){
					if($(this).find('.navigation-menu-item:visible').length === 0)
					{
						// Hide subitems
						$(this).hide();
						// Hide header
						$(this).parent().children('.navigation-menu-group[aria-controls="'+$(this).attr('id')+'"]').hide();
					}
				});			
				
			}
			
			function {$sPrefix}DisplayActiveMenu()
			{
				//reapply default display (ie active menus/submenus)
				oMenuElem.find('.ui-accordion-content').hide();
				oMenuElem.find('.navigation-menu-group').show();
				oMenuElem.find('.ui-accordion-header-active, .ui-accordion-content-active, .ui-accordion-content-active .navigation-menu-item').show();
			}
JS


		);
	}

	/**
	 * Add content to the South pane
	 *
	 * @param iTopWebPage $oPage The page to insert stuff into.
	 *
	 * @return string The HTML content to add into the page
	 */
	public function GetSouthPaneHtml(iTopWebPage $oPage)
	{
		// TODO: Implement GetSouthPaneHtml() method.
	}

	/**
	 * Add content to the "admin banner"
	 *
	 * @param iTopWebPage $oPage The page to insert stuff into.
	 *
	 * @return string The HTML content to add into the page
	 */
	public function GetBannerHtml(iTopWebPage $oPage)
	{
		// TODO: Implement GetBannerHtml() method.
	}
}
