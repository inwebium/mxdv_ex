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
use Bitrix\Main\Loader;

Loader::includeModule("sale");

if($this->startResultCache(false, [CSaleBasket::GetBasketUserID()]))
{
	$delaydBasketItems = CSaleBasket::GetList(
		[],
	    [
	        "FUSER_ID" => CSaleBasket::GetBasketUserID(),
	        "LID" => SITE_ID,
	        "ORDER_ID" => "NULL",
	        "DELAY" => "Y"
	    ],
	  	[]
	);

	$arResult = [];

	$arResult['ITEMS']['COUNT'] = $delaydBasketItems;

	$resultCacheKeys = ['ITEMS'];

	$this->setResultCacheKeys($resultCacheKeys);

	$this->includeComponentTemplate();
}