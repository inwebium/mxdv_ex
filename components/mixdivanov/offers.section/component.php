<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

/** @global CIntranetToolbar $INTRANET_TOOLBAR */
global $INTRANET_TOOLBAR;

use Bitrix\Main\Context,
	Bitrix\Main\Type\DateTime,
	Bitrix\Main\Loader,
	Bitrix\Iblock;

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if(strlen($arParams["IBLOCK_TYPE"])<=0)
	$arParams["IBLOCK_TYPE"] = "news";
$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);
$arParams["PARENT_SECTION"] = intval($arParams["PARENT_SECTION"]);
$arParams["INCLUDE_SUBSECTIONS"] = $arParams["INCLUDE_SUBSECTIONS"]!="N";
$arParams["SET_LAST_MODIFIED"] = $arParams["SET_LAST_MODIFIED"]==="Y";

$arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);
if(strlen($arParams["SORT_BY1"])<=0)
	$arParams["SORT_BY1"] = "ACTIVE_FROM";
if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER1"]))
	$arParams["SORT_ORDER1"]="DESC";

if(strlen($arParams["SORT_BY2"])<=0)
{
	if (strtoupper($arParams["SORT_BY1"]) == 'SORT')
	{
		$arParams["SORT_BY2"] = "ID";
		$arParams["SORT_ORDER2"] = "DESC";
	}
	else
	{
		$arParams["SORT_BY2"] = "SORT";
	}
}
if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER2"]))
	$arParams["SORT_ORDER2"]="ASC";

if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
{
	$arrFilter = [];
}
else
{
	$arrFilter = $GLOBALS[$arParams["FILTER_NAME"]];
	if(!is_array($arrFilter))
		$arrFilter = [];
}

$arParams["CHECK_DATES"] = $arParams["CHECK_DATES"]!="N";

if(!is_array($arParams["FIELD_CODE"]))
	$arParams["FIELD_CODE"] = [];
foreach($arParams["FIELD_CODE"] as $key=>$val)
	if(!$val)
		unset($arParams["FIELD_CODE"][$key]);

if(!is_array($arParams["PROPERTY_CODE"]))
	$arParams["PROPERTY_CODE"] = [];
foreach($arParams["PROPERTY_CODE"] as $key=>$val)
	if($val==="")
		unset($arParams["PROPERTY_CODE"][$key]);

$arParams["DETAIL_URL"]=trim($arParams["DETAIL_URL"]);

$arParams["NEWS_COUNT"] = intval($arParams["NEWS_COUNT"]);
if($arParams["NEWS_COUNT"]<=0)
	$arParams["NEWS_COUNT"] = 20;

$arParams["CACHE_FILTER"] = $arParams["CACHE_FILTER"]=="Y";
if(!$arParams["CACHE_FILTER"] && count($arrFilter)>0)
	$arParams["CACHE_TIME"] = 0;

$arParams["SET_TITLE"] = $arParams["SET_TITLE"]!="N";
$arParams["SET_BROWSER_TITLE"] = (isset($arParams["SET_BROWSER_TITLE"]) && $arParams["SET_BROWSER_TITLE"] === 'N' ? 'N' : 'Y');
$arParams["SET_META_KEYWORDS"] = (isset($arParams["SET_META_KEYWORDS"]) && $arParams["SET_META_KEYWORDS"] === 'N' ? 'N' : 'Y');
$arParams["SET_META_DESCRIPTION"] = (isset($arParams["SET_META_DESCRIPTION"]) && $arParams["SET_META_DESCRIPTION"] === 'N' ? 'N' : 'Y');
$arParams["ADD_SECTIONS_CHAIN"] = $arParams["ADD_SECTIONS_CHAIN"]!="N"; //Turn on by default
$arParams["INCLUDE_IBLOCK_INTO_CHAIN"] = $arParams["INCLUDE_IBLOCK_INTO_CHAIN"]!="N";
$arParams["STRICT_SECTION_CHECK"] = (isset($arParams["STRICT_SECTION_CHECK"]) && $arParams["STRICT_SECTION_CHECK"] === "Y");
$arParams["ACTIVE_DATE_FORMAT"] = trim($arParams["ACTIVE_DATE_FORMAT"]);
if(strlen($arParams["ACTIVE_DATE_FORMAT"])<=0)
	$arParams["ACTIVE_DATE_FORMAT"] = $DB->DateFormatToPHP(CSite::GetDateFormat("SHORT"));
$arParams["PREVIEW_TRUNCATE_LEN"] = intval($arParams["PREVIEW_TRUNCATE_LEN"]);
$arParams["HIDE_LINK_WHEN_NO_DETAIL"] = $arParams["HIDE_LINK_WHEN_NO_DETAIL"]=="Y";

$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]=="Y";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]=="Y";
$arParams["CHECK_PERMISSIONS"] = $arParams["CHECK_PERMISSIONS"]!="N";

// Сортировка из request
if (isset($_REQUEST['SORTBY']) && $_REQUEST['SORTBY'] == 'PRICE' && isset($_REQUEST['ORDER']))
{
	//$arParams["SORT_BY1"] = 'catalog_PRICE_1';
	$arParams["SORT_BY1"] = 'PROPERTY_START_PRICE';
	$arParams["SORT_ORDER1"] = $_REQUEST['ORDER'];
	$arParams["SORT_BY2"] = 'SORT';
	$arParams["SORT_ORDER2"] = 'ASC';
}

if($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"])
{
	$arNavParams = [
		"nPageSize" => $arParams["NEWS_COUNT"],
		"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
		"bShowAll" => $arParams["PAGER_SHOW_ALL"],
		'checkOutOfRange' => true,
	];
	$arNavigation = CDBResult::GetNavParams($arNavParams);
	if($arNavigation["PAGEN"]==0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
		$arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];
}
else
{
	$arNavParams = [
		"nTopCount" => $arParams["NEWS_COUNT"],
		"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
	];
	$arNavigation = false;
}

if (empty($arParams["PAGER_PARAMS_NAME"]) || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["PAGER_PARAMS_NAME"]))
{
	$pagerParameters = [];
}
else
{
	$pagerParameters = $GLOBALS[$arParams["PAGER_PARAMS_NAME"]];
	if (!is_array($pagerParameters))
		$pagerParameters = [];
}

$arParams["USE_PERMISSIONS"] = $arParams["USE_PERMISSIONS"]=="Y";
if(!is_array($arParams["GROUP_PERMISSIONS"]))
	$arParams["GROUP_PERMISSIONS"] = [1];

$bUSER_HAVE_ACCESS = !$arParams["USE_PERMISSIONS"];
if($arParams["USE_PERMISSIONS"] && isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
{
	$arUserGroupArray = $USER->GetUserGroupArray();
	foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
	{
		if(in_array($PERM, $arUserGroupArray))
		{
			$bUSER_HAVE_ACCESS = true;
			break;
		}
	}
}

/*v_dump($_REQUEST['SECTION_CODE']);
v_dump($_REQUEST['SECTION_PART']);
v_dump($arParams["PARENT_SECTION_CODE"]);*/
// "Характеристики фильтра" или "фильтруемые характеристики"...
//if (strlen($_REQUEST['SECTION_PART']) > 2)
//{
	// Кусок с подразделом (в URLе вида раздел(-|_)подраздел)
	$sectionPart = substr($_REQUEST['SECTION_PART'], 1);

	$arFilterables = CFilterCharacteristics::GetAll();
	$arFilterablePostfixes = [];

	foreach ($arFilterables as $filterableId => $arFilterable) {
		$arFilterablePostfixes[$arFilterable['UF_POSTFIX']] = $arFilterable;
	}

	foreach (array_keys($arFilterablePostfixes) as $key => $filterablePostfix) {
		if (strpos($arParams["PARENT_SECTION_CODE"], $filterablePostfix) !== false) {
			//v_dump("\n\n\n\n\nFILTERD PAGE!");
			//v_dump($filterablePostfix);
			//v_dump($arParams["PARENT_SECTION_CODE"]);
			//v_dump($arParams["PARENT_SECTION"]);

			$arrFilter['PROPERTY_FILTER_CHARACTERISTICS'] = $arFilterablePostfixes[$filterablePostfix]['UF_XML_ID'];
			$arParams["PARENT_SECTION_CODE"] = str_replace('_' . $filterablePostfix, '', $arParams["PARENT_SECTION_CODE"]);

			//v_dump($arParams["PARENT_SECTION_CODE"]);
			//v_dump($arParams["PARENT_SECTION"]);
		}
	}
	/*if (in_array($sectionPart, array_keys($arFilterablePostfixes)) || strpos($arParams["PARENT_SECTION_CODE"], $sectionPart))
	{
		//v_dump("\n\n\n\n\nFILTERD PAGE!");
		//v_dump($sectionPart);
		//v_dump($arFilterablePostfixes[$sectionPart]);
		//v_dump($arParams["PARENT_SECTION_CODE"]);
		//v_dump($arParams["PARENT_SECTION"]);

		$arrFilter['PROPERTY_FILTER_CHARACTERISTICS'] = $arFilterablePostfixes[$sectionPart]['UF_XML_ID'];
		$arParams["PARENT_SECTION_CODE"] = str_replace('_' . $sectionPart, '', $arParams["PARENT_SECTION_CODE"]);

		//v_dump($arParams["PARENT_SECTION_CODE"]);
		//v_dump($arParams["PARENT_SECTION"]);
	}*/
//}

// Добавление в фильтр элементов фильтрации по цене 
// при установленных в запросе минимальной и максимальной цене
if (isset($_REQUEST['MIN_PRICE']) && isset($_REQUEST['MAX_PRICE'])) {
	$arrFilter['>=PROPERTY_START_PRICE'] = intval($_REQUEST['MIN_PRICE']);
	$arrFilter['<=PROPERTY_START_PRICE'] = intval($_REQUEST['MAX_PRICE']);
}


if($this->startResultCache(
	false, 
	[
		($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), 
		$bUSER_HAVE_ACCESS, 
		$arNavigation, 
		$arrFilter, 
		$pagerParameters
	]
)) {
	if(!Loader::includeModule("iblock"))
	{
		$this->abortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	if(is_numeric($arParams["IBLOCK_ID"]))
	{
		$rsIBlock = CIBlock::GetList([], [
			"ACTIVE" => "Y",
			"ID" => $arParams["IBLOCK_ID"],
		]);
	}
	else
	{
		$rsIBlock = CIBlock::GetList([], [
			"ACTIVE" => "Y",
			"CODE" => $arParams["IBLOCK_ID"],
			"SITE_ID" => SITE_ID,
		]);
	}

	$arResult = $rsIBlock->GetNext();
	if (!$arResult)
	{
		$this->abortResultCache();
		Iblock\Component\Tools::process404(
			trim($arParams["MESSAGE_404"]) ?: GetMessage("T_NEWS_NEWS_NA")
			,true
			,$arParams["SET_STATUS_404"] === "Y"
			,$arParams["SHOW_404"] === "Y"
			,$arParams["FILE_404"]
		);
		return;
	}

	$arResult["USER_HAVE_ACCESS"] = $bUSER_HAVE_ACCESS;
	//SELECT
	$arSelect = array_merge($arParams["FIELD_CODE"], [
		"ID",
		"IBLOCK_ID",
		"IBLOCK_SECTION_ID",
		"NAME",
		"ACTIVE_FROM",
		"TIMESTAMP_X",
		"DETAIL_PAGE_URL",
		"LIST_PAGE_URL",
		"DETAIL_TEXT",
		"DETAIL_TEXT_TYPE",
		"PREVIEW_TEXT",
		"PREVIEW_TEXT_TYPE",
		"PREVIEW_PICTURE",
	]);
	$bGetProperty = count($arParams["PROPERTY_CODE"])>0;
	if($bGetProperty)
		$arSelect[]="PROPERTY_*";
	//WHERE
	$arFilter = array (
		"IBLOCK_ID" => $arResult["ID"],
		"IBLOCK_LID" => SITE_ID,
		"ACTIVE" => "Y",
		"CHECK_PERMISSIONS" => $arParams['CHECK_PERMISSIONS'] ? "Y" : "N",
	);

	if($arParams["CHECK_DATES"])
		$arFilter["ACTIVE_DATE"] = "Y";

	$PARENT_SECTION = CIBlockFindTools::GetSectionID(
		$arParams["PARENT_SECTION"],
		$arParams["PARENT_SECTION_CODE"],
		[
			"GLOBAL_ACTIVE" => "Y",
			"IBLOCK_ID" => $arResult["ID"],
		]
	);

	if (
		$arParams["STRICT_SECTION_CHECK"]
		&& (
			$arParams["PARENT_SECTION"] > 0
			|| strlen($arParams["PARENT_SECTION_CODE"]) > 0
		)
	) {
		if ($PARENT_SECTION <= 0)
		{
			$this->abortResultCache();
			Iblock\Component\Tools::process404(
				trim($arParams["MESSAGE_404"]) ?: GetMessage("T_NEWS_NEWS_NA")
				,true
				,$arParams["SET_STATUS_404"] === "Y"
				,$arParams["SHOW_404"] === "Y"
				,$arParams["FILE_404"]
			);
			return;
		}
	}

	$arParams["PARENT_SECTION"] = $PARENT_SECTION;

	if ($arParams["PARENT_SECTION"] > 0) {
		$arFilter["SECTION_ID"] = $arParams["PARENT_SECTION"];

		if ($arParams["INCLUDE_SUBSECTIONS"]) {
			$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
		}

		$arResult['SECTION'] = [];
		/****/
		/*$resSection = CIBlockSection::GetList(
			['ID' => 'DESC'], 
			['IBLOCK_ID' => $arParams["IBLOCK_ID"], 'ID' => $arResult["ID"]],
			false,
			['IBLOCK_ID', 'ID', 'CODE', 'NAME', 'DESCRIPTION'],
			false
		);

		if ($arSection = $resSection->GetNext()) {
			$arResult['SECTION'] = $arSection;
		}*/
		/****/

		$arResult["SECTION"]['PATH'] = [];
		//$arResult["DESCRIPTION"] = ;
		$rsPath = CIBlockSection::GetNavChain($arResult["ID"], $arParams["PARENT_SECTION"]);
		$rsPath->SetUrlTemplates("", $arParams["SECTION_URL"], $arParams["IBLOCK_URL"]);
		
		while ($arPath = $rsPath->GetNext()) {
			$ipropValues = new Iblock\InheritedProperty\SectionValues($arParams["IBLOCK_ID"], $arPath["ID"]);
			$arPath["IPROPERTY_VALUES"] = $ipropValues->getValues();
			$arResult["SECTION"]["PATH"][] = $arPath;
		}

		$ipropValues = new Iblock\InheritedProperty\SectionValues($arResult["ID"], $arParams["PARENT_SECTION"]);
		$arResult["IPROPERTY_VALUES"] = $ipropValues->getValues();
	} else {
		$arResult["SECTION"] = false;
	}
	//ORDER BY
	$arSort = [
		$arParams["SORT_BY1"]=>$arParams["SORT_ORDER1"],
		$arParams["SORT_BY2"]=>$arParams["SORT_ORDER2"],
	];

	if(!array_key_exists("ID", $arSort)) {
		$arSort["ID"] = "DESC";
	}

	$obParser = new CTextParser;
	$arResult['ITEMS'] = [];
	$arResult['ELEMENTS'] = [];
	$arResult['MECHANISMS'] = CTransformMechanism::GetList();

	// Популярные ссылки
	$arResult['POPULAR'] = CPopular::GetList();

	// Производители
	$arResult['MANUFACTURERS'] = CManufacturer::GetList();

	$arResult['OFFERS_PREFIX'] = OffersNaming::getPrefixFromSectionPath($arResult['SECTION']['PATH']);

	// Разделы для фильтра
	$sectionIndexForSubmenu = 0;
	// Нужно как-то обработать исключительный случай для распродажи
	// там цепочка вида Распродажа -> Раздел -> Механизм
	if ($arResult['SECTION']['PATH'][0]['ID'] == 614) {
		$sectionIndexForSubmenu = 1;
	}

	if ($arResult['SECTION']['PATH'][0]['ID'] == 614) {
		$arResult['FILTER']['SECTIONS'] = CTransformMechanism::GetSectionSubmenu($arResult['SECTION']['PATH'][$sectionIndexForSubmenu]['ID'], $arResult['ID'], true, 3);
	} else {
		$arResult['FILTER']['SECTIONS'] = CTransformMechanism::GetSectionSubmenu($arResult['SECTION']['PATH'][$sectionIndexForSubmenu]['ID'], $arResult['ID']);	
	}
	

	// Фильтруемые характеристики
	$arResult['FILTER']['CHARACTERISTICS'] = CFilterCharacteristics::GetAll();
	$arResult['FILTER']['CURRENT_URL'] = $arParams['PARENT_SECTION_CODE'];
	$arResult['FILTER']['CURRENT_CHARACTERISTIC'] = $arrFilter['PROPERTY_FILTER_CHARACTERISTICS'];

	$sectionPathCount = count($arResult['SECTION']['PATH']);

	$arResult['FILTER']['UNFILTERD_URL'] = $arResult['SECTION']['PATH'][($sectionPathCount-1)]['CODE'];

	// Минимальная и максимальная цена для фильтра
	$resMinElement = CIBlockElement::GetList(['PROPERTY_START_PRICE' => 'ASC'], array_merge($arFilter , $arrFilter), false, ['nTopCount' => 1], ['IBLOCK_ID', 'ID', 'PROPERTY_START_PRICE']);
	
	if ($arMinElement = $resMinElement->GetNext()) {
		$arResult['FILTER']['MIN_PRICE'] = $arMinElement['PROPERTY_START_PRICE_VALUE'];
	} else {
		$arResult['FILTER']['MIN_PRICE'] = 0;
	}
	
	$resMaxElement = CIBlockElement::GetList(
		['PROPERTY_START_PRICE' => 'DESC'], 
		array_merge($arFilter , $arrFilter), 
		false, 
		['nTopCount' => 1], 
		['IBLOCK_ID', 'ID', 'PROPERTY_START_PRICE']
	);
	
	if ($arMaxElement = $resMaxElement->GetNext()) {
		$arResult['FILTER']['MAX_PRICE'] = $arMaxElement['PROPERTY_START_PRICE_VALUE'];
	} else {
		$arResult['FILTER']['MAX_PRICE'] = 100000;
	}

	$rsElement = CIBlockElement::GetList(
		$arSort, 
		array_merge($arFilter , $arrFilter), 
		false, 
		$arNavParams, 
		$arSelect
	);
	$rsElement->SetUrlTemplates($arParams["DETAIL_URL"], "", $arParams["IBLOCK_URL"]);

	while($obElement = $rsElement->GetNextElement()) {
		$arItem = $obElement->GetFields();

		$arButtons = CIBlock::GetPanelButtons(
			$arItem["IBLOCK_ID"],
			$arItem["ID"],
			0,
			["SECTION_BUTTONS"=>false, "SESSID"=>false]
		);
		$arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
		$arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

		if($arParams["PREVIEW_TRUNCATE_LEN"] > 0) {
			$arItem["PREVIEW_TEXT"] = $obParser->html_cut($arItem["PREVIEW_TEXT"], $arParams["PREVIEW_TRUNCATE_LEN"]);
		}

		if(strlen($arItem["ACTIVE_FROM"])>0) {
			$arItem["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
		} else {
			$arItem['DISPLAY_ACTIVE_FROM'] = "";
		}

		Iblock\InheritedProperty\ElementValues::queue($arItem['IBLOCK_ID'], $arItem['ID']);

		$arItem['FIELDS'] = [];

		if($bGetProperty) {
			$arItem['PROPERTIES'] = $obElement->GetProperties();
		}



		$arItem['CATALOG_PRODUCT'] = CCatalogProduct::GetByID($arItem['ID']);
		$arItem['BASE_PRICE'] = CPrice::GetBasePrice($arItem['ID']);
		$arItem['BASE_PRICE_VALUE'] = intval($arItem['BASE_PRICE']["PRICE"]);

		$arItem['PRICE_TABLE'] = json_decode($arItem['PROPERTIES']['PRICE_TABLE']['~VALUE'], true);

		$arItem['START_PRICE'] = $arItem['BASE_PRICE_VALUE'] + $arItem['PRICE_TABLE'][0]['groups'][1];

		CManufacturer::ApplyMarkup($arResult['MANUFACTURERS'][$arItem['PROPERTIES']['MANUFACTURER']['~VALUE']], $arItem['START_PRICE']);
		CUtils::RoundPrice($arItem['START_PRICE']);

		if (!empty($arItem['PROPERTIES']['OLD_PRICE']['VALUE']) && $arItem['PROPERTIES']['OLD_PRICE']['VALUE'] > 0)
		{
			$arItem['OLD_PRICE']['VALUE'] = $arItem['PROPERTIES']['OLD_PRICE']['VALUE'];
			CManufacturer::ApplyMarkup($arResult['MANUFACTURERS'][$arItem['PROPERTIES']['MANUFACTURER']['~VALUE']], $arItem['OLD_PRICE']['VALUE']);
			CUtils::RoundPrice($arItem['OLD_PRICE']['VALUE']);
			$arItem['OLD_PRICE']['PROFIT'] = $arItem['OLD_PRICE']['VALUE'] - $arItem['START_PRICE'];
			$arItem['OLD_PRICE']['PERCENT'] = $arItem['OLD_PRICE']['PROFIT'] / ($arItem['OLD_PRICE']['VALUE'] / 100);
			$arItem['OLD_PRICE']['PERCENT'] = round($arItem['OLD_PRICE']['PERCENT'], 1);
		}

		$arItem['DISPLAY_PROPERTIES'] = [];

		foreach($arParams['PROPERTY_CODE'] as $pid)
		{
			$prop = &$arItem['PROPERTIES'][$pid];
			if(
				(is_array($prop['VALUE']) && count($prop['VALUE'])>0)
				|| (!is_array($prop['VALUE']) && strlen($prop['VALUE'])>0)
			)
			{
				$arItem['DISPLAY_PROPERTIES'][$pid] = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, 'news_out');
			}
		}

		if ($arParams['SET_LAST_MODIFIED'])
		{
			$time = DateTime::createFromUserTime($arItem['TIMESTAMP_X']);
			if (
				!isset($arResult['ITEMS_TIMESTAMP_X'])
				|| $time->getTimestamp() > $arResult['ITEMS_TIMESTAMP_X']->getTimestamp()
			)
				$arResult['ITEMS_TIMESTAMP_X'] = $time;
		}



		$arResult['ITEMS'][] = $arItem;
		$arResult['ELEMENTS'][] = $arItem['ID'];
	}

	foreach ($arResult["ITEMS"] as &$arItem) {
		$ipropValues = new Iblock\InheritedProperty\ElementValues($arItem["IBLOCK_ID"], $arItem["ID"]);
		$arItem["IPROPERTY_VALUES"] = $ipropValues->getValues();
		Iblock\Component\Tools::getFieldImageData(
			$arItem,
			['PREVIEW_PICTURE', 'DETAIL_PICTURE'],
			Iblock\Component\Tools::IPROPERTY_ENTITY_ELEMENT,
			'IPROPERTY_VALUES'
		);

		foreach($arParams["FIELD_CODE"] as $code)
			if(array_key_exists($code, $arItem))
				$arItem["FIELDS"][$code] = $arItem[$code];
	}

	unset($arItem);

	$navComponentParameters = [];
	
	if ($arParams["PAGER_BASE_LINK_ENABLE"] === "Y") {
		$pagerBaseLink = trim($arParams["PAGER_BASE_LINK"]);
		
		if ($pagerBaseLink === "") {
			if (
				$arResult["SECTION"]
				&& $arResult["SECTION"]["PATH"]
				&& $arResult["SECTION"]["PATH"][0]
				&& $arResult["SECTION"]["PATH"][0]["~SECTION_PAGE_URL"]
			) {
				$pagerBaseLink = $arResult["SECTION"]["PATH"][0]["~SECTION_PAGE_URL"];
			} elseif (
				isset($arItem) && isset($arItem["~LIST_PAGE_URL"])
			) {
				$pagerBaseLink = $arItem["~LIST_PAGE_URL"];
			}
		}

		if ($pagerParameters && isset($pagerParameters["BASE_LINK"])) {
			$pagerBaseLink = $pagerParameters["BASE_LINK"];
			unset($pagerParameters["BASE_LINK"]);
		}

		$navComponentParameters["BASE_LINK"] = CHTTP::urlAddParams($pagerBaseLink, $pagerParameters, ["encode"=>true]);
	}

	$arResult["NAV_STRING"] = $rsElement->GetPageNavStringEx(
		$navComponentObject,
		$arParams["PAGER_TITLE"],
		$arParams["PAGER_TEMPLATE"],
		$arParams["PAGER_SHOW_ALWAYS"],
		$this,
		$navComponentParameters
	);
	$arResult["NAV_CACHED_DATA"] = null;
	$arResult["NAV_RESULT"] = $rsElement;
	$arResult["NAV_PARAM"] = $navComponentParameters;

	// Какие ключи arResult кешировать
	$this->setResultCacheKeys([
		"ID",
		"IBLOCK_TYPE_ID",
		"LIST_PAGE_URL",
		"NAV_CACHED_DATA",
		"NAME",
		"SECTION",
		"ELEMENTS",
		"IPROPERTY_VALUES",
		"ITEMS_TIMESTAMP_X",
		"POPULAR",
		"FILTER",
		'MECHANISMS',
		'POPULAR',
		'MANUFACTURERS',
		'OFFERS_PREFIX',
	]);
	$this->includeComponentTemplate();
}

if(isset($arResult["ID"])) {
	$arTitleOptions = null;

	if($USER->IsAuthorized()) {
		if(
			$APPLICATION->GetShowIncludeAreas()
			|| (is_object($GLOBALS["INTRANET_TOOLBAR"]) && $arParams["INTRANET_TOOLBAR"]!=="N")
			|| $arParams["SET_TITLE"]
		) {
			if(Loader::includeModule("iblock")) {
				$arButtons = CIBlock::GetPanelButtons(
					$arResult["ID"],
					0,
					$arParams["PARENT_SECTION"],
					["SECTION_BUTTONS"=>false]
				);

				if($APPLICATION->GetShowIncludeAreas())
					$this->addIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));

				if(
					is_array($arButtons["intranet"])
					&& is_object($INTRANET_TOOLBAR)
					&& $arParams["INTRANET_TOOLBAR"]!=="N"
				) {
					$APPLICATION->AddHeadScript('/bitrix/js/main/utils.js');
					foreach($arButtons["intranet"] as $arButton)
						$INTRANET_TOOLBAR->AddButton($arButton);
				}

				if($arParams["SET_TITLE"]) {
					$arTitleOptions = [
						'ADMIN_EDIT_LINK' => $arButtons["submenu"]["edit_iblock"]["ACTION"],
						'PUBLIC_EDIT_LINK' => "",
						'COMPONENT_NAME' => $this->getName(),
					];
				}
			}
		}
	}

	$this->setTemplateCachedData($arResult["NAV_CACHED_DATA"]);

	if ($arParams["SET_TITLE"]) {
		if ($arResult["IPROPERTY_VALUES"] && $arResult["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] != "")
			$APPLICATION->SetTitle($arResult["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"], $arTitleOptions);
		elseif(isset($arResult["NAME"]))
			$APPLICATION->SetTitle($arResult["NAME"], $arTitleOptions);
	}

	if ($arResult["IPROPERTY_VALUES"]) {
		if ($arParams["SET_BROWSER_TITLE"] === 'Y' && $arResult["IPROPERTY_VALUES"]["SECTION_META_TITLE"] != "")
			$APPLICATION->SetPageProperty("title", $arResult["IPROPERTY_VALUES"]["SECTION_META_TITLE"], $arTitleOptions);

		if ($arParams["SET_META_KEYWORDS"] === 'Y' && $arResult["IPROPERTY_VALUES"]["SECTION_META_KEYWORDS"] != "")
			$APPLICATION->SetPageProperty("keywords", $arResult["IPROPERTY_VALUES"]["SECTION_META_KEYWORDS"], $arTitleOptions);

		if ($arParams["SET_META_DESCRIPTION"] === 'Y' && $arResult["IPROPERTY_VALUES"]["SECTION_META_DESCRIPTION"] != "")
			$APPLICATION->SetPageProperty("description", $arResult["IPROPERTY_VALUES"]["SECTION_META_DESCRIPTION"], $arTitleOptions);
	}

	if($arParams["INCLUDE_IBLOCK_INTO_CHAIN"] && isset($arResult["NAME"])) {
		if($arParams["ADD_SECTIONS_CHAIN"] && is_array($arResult["SECTION"]))
			$APPLICATION->AddChainItem(
				$arResult["NAME"]
				,strlen($arParams["IBLOCK_URL"]) > 0? $arParams["IBLOCK_URL"]: $arResult["LIST_PAGE_URL"]
			);
		else
			$APPLICATION->AddChainItem($arResult["NAME"]);
	}

	if($arParams["ADD_SECTIONS_CHAIN"] && is_array($arResult["SECTION"])) {
		foreach($arResult["SECTION"]["PATH"] as $arPath) {
			if ($arPath["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] != "")
				$APPLICATION->AddChainItem($arPath["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"], $arPath["~SECTION_PAGE_URL"]);
			else
				$APPLICATION->AddChainItem($arPath["NAME"], $arPath["~SECTION_PAGE_URL"]);
		}
	}

	if ($arParams["SET_LAST_MODIFIED"] && $arResult["ITEMS_TIMESTAMP_X"])
	{
		Context::getCurrent()->getResponse()->setLastModified($arResult["ITEMS_TIMESTAMP_X"]);
	}

	return $arResult["ELEMENTS"];
}
