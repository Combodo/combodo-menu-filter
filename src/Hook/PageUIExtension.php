<?php
/**
 * Copyright (C) 2013-2020 Combodo SARL
 *
 * This file is part of iTop.
 *
 * iTop is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * iTop is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 */

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
	 * @inheritDoc
	 * @throws \Exception
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
			
			// Bind events
			// - Input focus in
			$('#$sPrefix-input').on('focusin', function(){
				{$sPrefix}SetActive();
			});
			// - Input focus out
			$('#$sPrefix-input').on('focusout', function(){
				if({$sPrefix}GetFilterValue() === '')
				{
					{$sPrefix}SetInactive();
				}
			});
			// - Key strokes on input
			$('#$sPrefix-input').on('keyup', function(oEvent){
				var oInputElem = $(this);
				
				//filter mnenus listener
				var sFilterInputValue = {$sPrefix}GetFilterValue();
				
				if ((sFilterInputValue === "") || (oEvent.key === "Escape")) 
				{
					{$sPrefix}ClearFiltering();
					{$sPrefix}UpdateEmptyLabel();
				} 
				else 
				{
					// Reset throttle timeout on key stroke
					clearTimeout(r{$sPrefix}KeyTimeout);
					r{$sPrefix}KeyTimeout = setTimeout(function(){
						{$sPrefix}FilterMenus(sFilterInputValue);
						{$sPrefix}UpdateEmptyLabel();
					}, 200);
				}
			});
			// - Clear icon
			$('#$sPrefix-clear-icon').on('click', function(){
				{$sPrefix}ClearFiltering();
				{$sPrefix}UpdateEmptyLabel();
				$('#$sPrefix-input').trigger('focus');
			});
			
			function {$sPrefix}SetActive()
			{
				oMenuElem.find('#$sPrefix-filter-textbox').addClass('active');
			}
			function {$sPrefix}SetInactive()
			{
				oMenuElem.find('#$sPrefix-filter-textbox').removeClass('active');
			}
			function {$sPrefix}GetFilterValue()
			{
				return $('#$sPrefix-input').val();
			}
			function {$sPrefix}ClearFiltering()
			{
				// Empty text box
				$('#$sPrefix-input').val('');
				{$sPrefix}DisplayActiveMenu();
				{$sPrefix}UpdateEmptyLabel();
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
				
				{$sPrefix}UpdateEmptyLabel();
			}
			function {$sPrefix}DisplayActiveMenu()
			{
				//reapply default display (ie active menus/submenus)
				oMenuElem.find('.ui-accordion-content').hide();
				oMenuElem.find('.navigation-menu-group').show();
				oMenuElem.find('.ui-accordion-header-active, .ui-accordion-content-active, .ui-accordion-content-active .navigation-menu-item').show();
			}
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
JS


		);
	}

	/**
	 * @inheritDoc
	 */
	public function GetSouthPaneHtml(iTopWebPage $oPage)
	{
		// TODO: Implement GetSouthPaneHtml() method.
	}

	/**
	 * @inheritDoc
	 */
	public function GetBannerHtml(iTopWebPage $oPage)
	{
		// TODO: Implement GetBannerHtml() method.
	}
}
