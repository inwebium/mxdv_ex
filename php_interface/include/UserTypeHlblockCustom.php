<?php

IncludeModuleLangFile(__FILE__);

class UserTypeHlblockCustom extends CUserTypeString
{
	function GetUserTypeDescription()
	{
		return array(
			"USER_TYPE_ID" => "hlblock_custom",
			"CLASS_NAME" => "UserTypeHlblockCustom",
			"DESCRIPTION" => "Справочник с описанием - множественный checkbox",
			"BASE_TYPE" => "string",
		);
	}

	// function GetDBColumnType($arUserField)
	// {
	// 	global $DB;
	// 	switch(strtolower($DB->type))
	// 	{
	// 		case "mysql":
	// 			return "int(18)";
	// 		case "oracle":
	// 			return "number(18)";
	// 		case "mssql":
	// 			return "int";
	// 	}
	// 	return "int";
	// }

	function PrepareSettings($arUserField)
	{
		$height = intval($arUserField["SETTINGS"]["LIST_HEIGHT"]);

		// $disp = $arUserField["SETTINGS"]["DISPLAY"];

		// if($disp!="CHECKBOX" && $disp!="LIST")
		// 	$disp = "LIST";

		$hlblock_id = intval($arUserField["SETTINGS"]["HLBLOCK_ID"]);

		if($hlblock_id <= 0)
			$hlblock_id = "";

		$hlfield_id = intval($arUserField["SETTINGS"]["HLFIELD_ID"]);

		if($hlfield_id < 0)
			$hlfield_id = "";

		$element_id = intval($arUserField["SETTINGS"]["DEFAULT_VALUE"]);

		return array(
			//"DISPLAY" => $disp,
			"LIST_HEIGHT" => ($height < 1? 1: $height),
			"HLBLOCK_ID" => $hlblock_id,
			"HLFIELD_ID" => $hlfield_id,
			"DEFAULT_VALUE" => $element_id,
		);
	}

	function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
	{
		$result = '';

		if($bVarsFromForm)
			$hlblock_id = $GLOBALS[$arHtmlControl["NAME"]]["HLBLOCK_ID"];
		elseif(is_array($arUserField))
			$hlblock_id = $arUserField["SETTINGS"]["HLBLOCK_ID"];
		else
			$hlblock_id = "";

		if($bVarsFromForm)
			$hlfield_id = $GLOBALS[$arHtmlControl["NAME"]]["HLFIELD_ID"];
		elseif(is_array($arUserField))
			$hlfield_id = $arUserField["SETTINGS"]["HLFIELD_ID"];
		else
			$hlfield_id = "";

		if($bVarsFromForm)
			$value = $GLOBALS[$arHtmlControl["NAME"]]["DEFAULT_VALUE"];
		elseif(is_array($arUserField))
			$value = $arUserField["SETTINGS"]["DEFAULT_VALUE"];
		else
			$value = "";

		if(CModule::IncludeModule('highloadblock'))
		{
			$dropDown = static::getDropDownHtml($hlblock_id, $hlfield_id);

			$result .= '
			<tr>
				<td>'.GetMessage("USER_TYPE_HLEL_DISPLAY").':</td>
				<td>
					'.$dropDown.'
				</td>
			</tr>
			';
		}

		if($hlblock_id > 0 && strlen($hlfield_id) && CModule::IncludeModule('highloadblock'))
		{
			$result .= '
			<tr>
				<td>'.GetMessage("USER_TYPE_HLEL_DEFAULT_VALUE").':</td>
				<td>
					<select name="'.$arHtmlControl["NAME"].'[DEFAULT_VALUE]" size="5">
						<option value="">'.GetMessage("IBLOCK_VALUE_ANY").'</option>
			';

			$rows = static::getHlRows(array('SETTINGS' => array('HLBLOCK_ID' => $hlblock_id, 'HLFIELD_ID' => $hlfield_id)));

			foreach ($rows as $row)
			{
				$result .= '<option value="'.$row["ID"].'" '.($row["ID"]==$value? "selected": "").'>'.htmlspecialcharsbx($row['VALUE']).'</option>';
			}

			$result .= '</select>';
		}
		else
		{
			$result .= '
			<tr>
				<td>'.GetMessage("USER_TYPE_HLEL_DEFAULT_VALUE").':</td>
				<td>
					<input type="text" size="8" name="'.$arHtmlControl["NAME"].'[DEFAULT_VALUE]" value="'.htmlspecialcharsbx($value).'">
				</td>
			</tr>
			';
		}

		/*if($bVarsFromForm)
			$value = $GLOBALS[$arHtmlControl["NAME"]]["DISPLAY"];
		elseif(is_array($arUserField))
			$value = $arUserField["SETTINGS"]["DISPLAY"];
		else
			$value = "LIST";
		$result .= '
		<tr>
			<td class="adm-detail-valign-top">'.GetMessage("USER_TYPE_ENUM_DISPLAY").':</td>
			<td>
				<label><input type="radio" name="'.$arHtmlControl["NAME"].'[DISPLAY]" value="LIST" '.("LIST"==$value? 'checked="checked"': '').'>'.GetMessage("USER_TYPE_HLEL_LIST").'</label><br>
				<label><input type="radio" name="'.$arHtmlControl["NAME"].'[DISPLAY]" value="CHECKBOX" '.("CHECKBOX"==$value? 'checked="checked"': '').'>'.GetMessage("USER_TYPE_HLEL_CHECKBOX").'</label><br>
			</td>
		</tr>
		';*/

		if($bVarsFromForm)
			$value = intval($GLOBALS[$arHtmlControl["NAME"]]["LIST_HEIGHT"]);
		elseif(is_array($arUserField))
			$value = intval($arUserField["SETTINGS"]["LIST_HEIGHT"]);
		else
			$value = 5;
		$result .= '
		<tr>
			<td>'.GetMessage("USER_TYPE_HLEL_LIST_HEIGHT").':</td>
			<td>
				<input type="text" name="'.$arHtmlControl["NAME"].'[LIST_HEIGHT]" size="10" value="'.$value.'">
			</td>
		</tr>
		';

		return $result;
	}

	function CheckFields($arUserField, $value)
	{
		$aMsg = array();
		return $aMsg;
	}

	function GetList($arUserField)
	{
		$rs = false;

		if(CModule::IncludeModule('highloadblock'))
		{
			$rows = static::getHlRows($arUserField, true);

			$rs = new CDBResult();
			$rs->InitFromArray($rows);

		}

		return $rs;
	}

	function getEntityReferences($userfield, \Bitrix\Main\Entity\ScalarField $entityField)
	{
		if ($userfield['SETTINGS']['HLBLOCK_ID'])
		{
			$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($userfield['SETTINGS']['HLBLOCK_ID'])->fetch();

			if ($hlblock)
			{
				if (class_exists($hlblock['NAME'].'Table'))
				{
					$hlentity = \Bitrix\Main\Entity\Base::getInstance($hlblock['NAME']);
				}
				else
				{
					$hlentity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
				}

				return array(
					new \Bitrix\Main\Entity\ReferenceField(
						$entityField->getName().'_REF',
						$hlentity,
						array('=this.'.$entityField->getName() => 'ref.ID')
					)
				);
			}
		}

		return array();
	}

	public static function getHlRows($userfield, $clearValues = false)
	{
		global $USER_FIELD_MANAGER;

		$rows = array();

		$hlblock_id = $userfield['SETTINGS']['HLBLOCK_ID'];
		$hlfield_id = $userfield['SETTINGS']['HLFIELD_ID'];

		if (!empty($hlblock_id))
		{
			$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlblock_id)->fetch();
		}

		if (!empty($hlblock))
		{
			$userfield = null;

			if ($hlfield_id == 0)
			{
				$userfield = array('FIELD_NAME' => 'ID');
			}
			else
			{
				$userfields = $USER_FIELD_MANAGER->GetUserFields('HLBLOCK_'.$hlblock['ID'], 0, LANGUAGE_ID);

				foreach ($userfields as $_userfield)
				{
					if ($_userfield['ID'] == $hlfield_id)
					{
						$userfield = $_userfield;
						break;
					}
				}
			}

			if ($userfield)
			{
				// validated successfully. get data
				$hlDataClass = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock)->getDataClass();
				$rows = $hlDataClass::getList(array(
					'select' => array('ID', $userfield['FIELD_NAME']),
					'order' => 'ID'
				))->fetchAll();

				foreach ($rows as &$row)
				{
					if ($userfield['FIELD_NAME'] == 'ID')
					{
						$row['VALUE'] = $row['ID'];
					}
					else
					{
						//see #0088117
						if ($userfield['USER_TYPE_ID'] != 'enumeration' && $clearValues)
						{
							$row['VALUE'] = $row[$userfield['FIELD_NAME']];
						}
						else
						{
							$row['VALUE'] = $USER_FIELD_MANAGER->getListView($userfield, $row[$userfield['FIELD_NAME']]);
						}
						$row['VALUE'] .= ' ['.$row['ID'].']';
					}
				}
			}
		}

		return $rows;
	}

	function GetAdminListViewHTML($arUserField, $arHtmlControl)
	{
		static $cache = array();
		$empty_caption = '&nbsp;';

		$cacheKey = $arUserField['SETTINGS']['HLBLOCK_ID'].'_v'.$arHtmlControl["VALUE"];

		if(!array_key_exists($cacheKey, $cache) && !empty($arHtmlControl["VALUE"]))
		{
			$rsEnum = call_user_func_array(
				[
					$arUserField["USER_TYPE"]["CLASS_NAME"], 
					"getlist"
				],
				[
					$arUserField,
				]
			);
			if(!$rsEnum)
				return $empty_caption;
			while($arEnum = $rsEnum->GetNext())
				$cache[$arUserField['SETTINGS']['HLBLOCK_ID'].'_v'.$arEnum["ID"]] = $arEnum["VALUE"];
		}
		if(!array_key_exists($cacheKey, $cache))
			$cache[$cacheKey] = $empty_caption;

		return $cache[$cacheKey];
	}

	public static function getDropDownData()
	{
		global $USER_FIELD_MANAGER;

		$hlblocks = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('order' => 'NAME'))->fetchAll();

		$list = array();

		foreach ($hlblocks as $hlblock)
		{
			// add hlblock itself
			$list[$hlblock['ID']] = array(
				'name' => $hlblock['NAME'],
				'fields' => array(
					0 => 'ID'
				)
			);

			$userfields = $USER_FIELD_MANAGER->GetUserFields('HLBLOCK_'.$hlblock['ID'], 0, LANGUAGE_ID);

			foreach ($userfields as $userfield)
			{
				$fieldTitle = strlen($userfield['LIST_COLUMN_LABEL']) ? $userfield['LIST_COLUMN_LABEL'] : $userfield['FIELD_NAME'];
				$list[$hlblock['ID']]['fields'][(int)$userfield['ID']] = $fieldTitle;
			}
		}

		return $list;
	}

	public static function getDropDownHtml($hlblockId = null, $hlfieldId = null)
	{

		$list = static::getDropDownData();

		// hlblock selector
		$html = '<select name="SETTINGS[HLBLOCK_ID]" onchange="hlChangeFieldOnHlblockChanged(this)">';
		$html .= '<option value="">'.htmlspecialcharsbx(GetMessage('USER_TYPE_HLEL_SEL_HLBLOCK')).'</option>';

		foreach ($list as $_hlblockId => $hlblockData)
		{
			$html .= '<option value="'.$_hlblockId.'" '.($_hlblockId == $hlblockId?'selected':'').'>'.htmlspecialcharsbx($hlblockData['name']).'</option>';
		}

		$html .= '</select> &nbsp; ';

		// field selector
		$html .= '<select name="SETTINGS[HLFIELD_ID]" id="hl_ufsett_field_selector">';
		$html .= '<option value="">'.htmlspecialcharsbx(GetMessage('USER_TYPE_HLEL_SEL_HLBLOCK_FIELD')).'</option>';

		if ($hlblockId)
		{
			if (strlen($hlfieldId))
			{
				$hlfieldId = (int) $hlfieldId;
			}

			foreach ($list[$hlblockId]['fields'] as $fieldId => $fieldName)
			{
				$html .= '<option value="'.$fieldId.'" '.($fieldId === $hlfieldId?'selected':'').'>'.htmlspecialcharsbx($fieldName).'</option>';
			}
		}

		$html .= '</select>';

		// js: changing field selector
		$html .= '
			<script type="text/javascript">
				function hlChangeFieldOnHlblockChanged(hlSelect)
				{
					var list = '.CUtil::PhpToJSObject($list).';
					var fieldSelect = BX("hl_ufsett_field_selector");

					for(var i=fieldSelect.length-1; i >= 0; i--)
						fieldSelect.remove(i);

					var newOption = new Option(\''.GetMessageJS('USER_TYPE_HLEL_SEL_HLBLOCK_FIELD').'\', "", false, false);
					fieldSelect.options.add(newOption);

					if (list[hlSelect.value])
					{
						for(var j in list[hlSelect.value]["fields"])
						{
							var newOption = new Option(list[hlSelect.value]["fields"][j], j, false, false);
							fieldSelect.options.add(newOption);
						}
					}
				}
			</script>
		';

		return $html;
	}

	function getParsedValue($value)
	{
		$result = [];

		foreach ($value as $key => $valueStringItem) {
			$valueItem = explode(';;', $valueStringItem);

			$result[$valueItem[0]] = [
				'LOOKUP_ID' => $valueItem[0],
				'IS_CHECKED' => boolval($valueItem[1]),
				'DESCRIPTION' => $valueItem[2],
				'HEADING' => $valueItem[3],
				'META_TITLE' => $valueItem[4],
				'META_DESCRIPTION' => $valueItem[5],
				'META_KEYWORDS' => $valueItem[6],
				'STRINGIFIED' => $valueStringItem,
			];
		}

		return $result;
	}

	function GetEditFormHTMLMulty($arUserField, $arHtmlControl)
	{
		if(($arUserField["ENTITY_VALUE_ID"]<1) && strlen($arUserField["SETTINGS"]["DEFAULT_VALUE"])>0)
			$arHtmlControl["VALUE"] = array(intval($arUserField["SETTINGS"]["DEFAULT_VALUE"]));
		elseif(!is_array($arHtmlControl["VALUE"]))
			$arHtmlControl["VALUE"] = array();

		$rsEnum = call_user_func_array(
			array($arUserField["USER_TYPE"]["CLASS_NAME"], "getlist"),
			array(
				$arUserField,
			)
		);
		if(!$rsEnum)
			return '';

		$result = '';
		$result .= '</td></tr>';

		$result .= '<input type="hidden" value="" name="'.$arHtmlControl["NAME"].'">';
		$bWasSelect = false;

		// v_dump($arHtmlControl);
		// v_dump(self::getParsedValue($arHtmlControl['VALUE']));
		// v_dump($arUserField);

		$parsedValue = self::getParsedValue($arHtmlControl['VALUE']);
		$counter = 0;

		while($arEnum = $rsEnum->GetNext()) {
			$bSelected = (
				(!empty($parsedValue[$arEnum["ID"]]) && $parsedValue[$arEnum["ID"]]['IS_CHECKED']) ||
				($arUserField["ENTITY_VALUE_ID"]<=0 && $arEnum["DEF"]=="Y")
			);
			$bWasSelect = $bWasSelect || $bSelected;

			$additionalStyle = '';

			if ($counter % 2 == 0) {
				$additionalStyle = 'background: #d2dddd;';
			}

			$result .= '<tr><td style="width:30%;border-right:1px solid #9dacae;border-bottom:1px solid #9dacae;' . $additionalStyle . '">';

			$result .= '<input class="hlblock_lookup_input" id="checkbox_' . $arEnum["ID"] . '_input" type="hidden" value="' . $parsedValue[$arEnum["ID"]]['STRINGIFIED'] . '" name="'.$arHtmlControl["NAME"] . '">';

			// checkbox
			$result .= 
			'<label>' . 
				'<input' . 
					' class="hlblock_lookup_checkbox"' .
					' id="checkbox_' . $arEnum["ID"] . '_checkbox"' .
					' type="checkbox"' . 
					' value="'.$arEnum["ID"] . '"' . 
					//' name="'.$arHtmlControl["NAME"] . '"' . 
					($bSelected? ' checked': '') . 
					($arUserField["EDIT_IN_LIST"] != "Y" ? ' disabled="disabled" ': '') . 
					'>' . 
						$arEnum["VALUE"] . 
			'</label><br>';

			$result .= '</td><td style="border-bottom:1px solid #9dacae;' . $additionalStyle . '"><table>';

			// HEADING input
			$result .= self::getHeadingHtml($arEnum, $parsedValue);

			// META_TITLE input
			$result .= self::getMetaTitleHtml($arEnum, $parsedValue);

			// META_DESCRIPTION input
			$result .= self::getMetaDescriptionHtml($arEnum, $parsedValue);

			// META_KEYWORDS input
			$result .= self::getMetaKeywordsHtml($arEnum, $parsedValue);

			// DESCRIPTION textarea
			$result .= self::getDescriptionHtml($arEnum, $parsedValue);

			$result .= '</table></td></tr>';
			$counter++;
		}

		ob_start();
		?>
   		<script src="/local/templates/main/scripts/libs/jquery-3.3.1.min.js"></script>
   		<script>
   			function setCheckboxInputValue(checkboxVal)
   			{
   				var checkboxChecked = $('#checkbox_' + checkboxVal + '_checkbox').is(':checked');
   				
   				var descriptionVal = $('textarea[name="checkbox_' + checkboxVal + '_description"]').val();
   				var headingVal = $('input[name="checkbox_' + checkboxVal + '_heading"]').val();
   				var metaTitleVal = $('input[name="checkbox_' + checkboxVal + '_meta_title"]').val();
   				var metaDescriptionVal = $('textarea[name="checkbox_' + checkboxVal + '_meta_description"]').val();
   				var metaKeywordsVal = $('textarea[name="checkbox_' + checkboxVal + '_meta_keywords"]').val();
   				
   				var inputVal = checkboxVal + ';;' + checkboxChecked + ';;' + descriptionVal + ';;' + headingVal + ';;' + metaTitleVal + ';;' + metaDescriptionVal + ';;' + metaKeywordsVal;
   				
   				$('#checkbox_' + checkboxVal + '_input').val(inputVal);
   			}

   			$(document).on('change', '.hlblock_lookup_checkbox', function(event) {
   				setCheckboxInputValue($(this).val());
   			});

   			$(document).on('input', '.hlblock_lookup_heading', function() {
   				setCheckboxInputValue($(this).attr('data-checkboxval'));
   			});

   			$(document).on('input', '.hlblock_lookup_meta_title', function() {
   				setCheckboxInputValue($(this).attr('data-checkboxval'));
   			});

   			$(document).on('input', '.hlblock_lookup_meta_description', function() {
   				setCheckboxInputValue($(this).attr('data-checkboxval'));
   			});

   			$(document).on('input', '.hlblock_lookup_meta_keywords', function() {
   				setCheckboxInputValue($(this).attr('data-checkboxval'));
   			});

   			$(document).on('input', '.hlblock_lookup_description', function() {
   				setCheckboxInputValue($(this).attr('data-checkboxval'));
   			});
		</script>
   		<?
   		$return = ob_get_contents();
   		ob_end_clean();

		$result .= $return;
		return $result;
	}

	private static function getHeadingHtml(&$arEnum, &$parsedValue)
	{
		return '<tr><td style="width: 30%;"><label>Заголовок:</label></td><td><input type="text" class="hlblock_lookup_heading" name="checkbox_' . $arEnum["ID"] . '_heading" data-checkboxval="' . $arEnum["ID"] . '" value="' . $parsedValue[$arEnum["ID"]]['HEADING'] . '" style="width: 100%;" /></td></tr>';
	}

	private static function getDescriptionHtml(&$arEnum, &$parsedValue)
	{
		return '<tr><td style="width: 30%;"><label>Описание:</label></td><td><textarea class="typearea hlblock_lookup_description" style="width:100%;height:200px;" name="checkbox_' . $arEnum["ID"] . '_description" data-checkboxval="' . $arEnum["ID"] . '">' . $parsedValue[$arEnum["ID"]]['DESCRIPTION'] . '</textarea></td></tr>';
	}

	private static function getMetaTitleHtml(&$arEnum, &$parsedValue)
	{
		return '<tr><td style="width: 30%;"><label>META TITLE:</label></td><td><input type="text" class="hlblock_lookup_meta_title" name="checkbox_' . $arEnum["ID"] . '_meta_title" data-checkboxval="' . $arEnum["ID"] . '" value="' . $parsedValue[$arEnum["ID"]]['META_TITLE'] . '" style="width: 100%;" /></td></tr>';
	}

	private static function getMetaDescriptionHtml(&$arEnum, &$parsedValue)
	{
		return '<tr><td style="width: 30%;"><label>META DESCRIPTION:</label></td><td><textarea class="typearea hlblock_lookup_meta_description" style="width:100%;height:100px;" name="checkbox_' . $arEnum["ID"] . '_meta_description" data-checkboxval="' . $arEnum["ID"] . '">' . $parsedValue[$arEnum["ID"]]['META_DESCRIPTION'] . '</textarea></td></tr>';
	}

	private static function getMetaKeywordsHtml(&$arEnum, &$parsedValue)
	{
		return '<tr><td style="width: 30%;"><label>META KEYWORDS:</label></td><td><textarea class="typearea hlblock_lookup_meta_keywords" style="width:100%;height:100px;" name="checkbox_' . $arEnum["ID"] . '_meta_keywords" data-checkboxval="' . $arEnum["ID"] . '">' . $parsedValue[$arEnum["ID"]]['META_KEYWORDS'] . '</textarea></label></td></tr>';
	}

}
?>
