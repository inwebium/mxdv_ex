<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?

//v_dump($arResult['SECTION']['PATH']);


//$arResult['MECHANISMS'] = CTransformMechanism::GetList();

/*foreach($arResult["ITEMS"] as $key => $arItem)
{
	$arResult["ITEMS"][$key]['CATALOG_PRODUCT'] = CCatalogProduct::GetByID($arItem['ID']);
	$arResult["ITEMS"][$key]['BASE_PRICE'] = CPrice::GetBasePrice($arItem['ID']);
	$arResult["ITEMS"][$key]['BASE_PRICE_VALUE'] = intval($arResult["ITEMS"][$key]['BASE_PRICE']["PRICE"]);

	$arResult["ITEMS"][$key]['PRICE_TABLE'] = json_decode($arItem['PROPERTIES']['PRICE_TABLE']['~VALUE'], true);

	$arResult["ITEMS"][$key]['START_PRICE'] = $arResult["ITEMS"][$key]['BASE_PRICE_VALUE'] + $arResult["ITEMS"][$key]['PRICE_TABLE'][0]['groups'][1];
}*/

//v_dump($arParams);
//v_dump("\n\n\n\n\n\n" . $arResult['FILTER']['CHARACTERISTICS']);
?>