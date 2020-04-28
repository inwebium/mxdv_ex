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

if(!isset($arParams["CACHE_TIME"])) {
	$arParams["CACHE_TIME"] = 36000000;
}

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);

if(strlen($arParams["IBLOCK_TYPE"])<=0) {
	$arParams["IBLOCK_TYPE"] = "news";
}

$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);
$arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);

if(strlen($arParams["SORT_BY1"])<=0) {
	$arParams["SORT_BY1"] = "ACTIVE_FROM";
}

if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER1"])) {
	$arParams["SORT_ORDER1"]="DESC";
}

if(strlen($arParams["SORT_BY2"])<=0) {
	if (strtoupper($arParams["SORT_BY1"]) == 'SORT') {
		$arParams["SORT_BY2"] = "ID";
		$arParams["SORT_ORDER2"] = "DESC";
	} else {
		$arParams["SORT_BY2"] = "SORT";
	}
}

if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER2"])) {
	$arParams["SORT_ORDER2"]="ASC";
}


if($this->startResultCache(false, [])) {
	if(!Loader::includeModule("iblock")) {
		$this->abortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}

	if(is_numeric($arParams["IBLOCK_ID"])) {
		$rsIBlock = CIBlock::GetList([], [
			"ACTIVE" => "Y",
			"ID" => $arParams["IBLOCK_ID"],
		]);
	} else {
		$rsIBlock = CIBlock::GetList([], [
			"ACTIVE" => "Y",
			"CODE" => $arParams["IBLOCK_ID"],
			"SITE_ID" => SITE_ID,
		]);
	}

	$arResult = $rsIBlock->GetNext();
	
	if (!$arResult) {
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

	$arSort = [
		$arParams["SORT_BY1"]=>$arParams["SORT_ORDER1"],
		$arParams["SORT_BY2"]=>$arParams["SORT_ORDER2"],
	];


	$arFilter = array(
		"IBLOCK_ID"=>$arParams["IBLOCK_ID"],
		"GLOBAL_ACTIVE"=>"Y",
		"IBLOCK_ACTIVE"=>"Y",
	);

	$rsSections = CIBlockSection::GetList(['SORT' => 'ASC', 'NAME' => 'ASC'], $arFilter, true, array(
		"IBLOCK_ID",
		"ID",
		"DEPTH_LEVEL",
		'PICTURE',
		"NAME",
	));

	$sectionsIds = [];

	while($arSection = $rsSections->GetNext()) {
		$sectionsIds[] = $arSection["ID"];
		$arResult["SECTIONS"][$arSection["ID"]] = [
			"ID" => $arSection["ID"],
			"NAME" => $arSection["~NAME"],
			"PICTURE" => $arSection["PICTURE"],
		];
	}

	$arFilter = [
		'IBLOCK_ID' => $arParams['IBLOCK_ID'],
		'SECTION_ID' => $sectionsIds
	];

	$rsElements = CIBlockElement::GetList(
		$arSort, 
		$arFilter, 
		false, 
		false, 
		['IBLOCK_ID', 'ID', 'NAME', 'CODE', 'IBLOCK_SECTION_ID']
	);

	while($arElement = $rsElements->GetNext()) {
		$arResult["SECTIONS"][$arElement["IBLOCK_SECTION_ID"]]['LINKS'][$arElement["ID"]] = [
			"ID" => $arElement["ID"],
			"NAME" => $arElement["NAME"],
			"HREF" => $arElement["CODE"],
		];
	}


	// Какие ключи arResult кешировать
	$this->setResultCacheKeys([
		"ID",
		"IBLOCK_TYPE_ID",
		"LIST_PAGE_URL",
		"NAV_CACHED_DATA",
		"NAME",
		"SECTIONS",
		"ITEMS_TIMESTAMP_X",
	]);
	$this->includeComponentTemplate();
}