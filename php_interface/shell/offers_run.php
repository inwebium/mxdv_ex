<?
ini_set("max_execution_time", 0);
ini_set("display_errors", 1);

$scriptDir = '/local/php_interface/shell';
$arPath = pathinfo(__FILE__);
$absolutepath = str_replace("\\", "/", $arPath['dirname']);
$stdout = fopen('php://stdout', 'w');

if (strpos($absolutepath, $scriptDir) === false)
{
    fwrite($stdout, "\n\e[1;31mERROR: autoinstall script must be in " . $scriptDir . "\e[0m\n\n");
    die();
}

$docRoot = str_replace($scriptDir, '', $absolutepath);
$_SERVER["DOCUMENT_ROOT"] = $docRoot;

define("STOP_STATISTICS", true);
define("NO_KEEP_STATISTIC", 'Y');
define("NO_AGENT_STATISTIC",'Y');
define("NO_AGENT_CHECK", true);
//define("DisableEventsCheck", true);
define("NOT_CHECK_PERMISSIONS", true);
//define("BX_BUFFER_USED", true);

fwrite($stdout, "HELLO\n");

require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_admin_before.php');
global $DB, $USER, $APPLICATION;

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

fwrite($stdout, "Authorizing user with id=1\n");
$isAuthorized = $USER->Authorize(1);

if (!$isAuthorized)
{
    fwrite($stdout, "ERROR: \nFailed to authrize user with id=1");
    die(); 
}
else
{
    fwrite($stdout, "\e[1;32mUser with id=1 authorized\e[0m\n");
}

$allOffersRes = CIBlockElement::GetList(
	['ID' => 'ASC'],
	['IBLOCK_ID' => 2, 'IBLOCK_SECTION_ID' => 16],
	false,
	false,
	['IBLOCK_ID', 'ID']
);

//$allOffersIds = [];
$PRICE_TYPE_ID = 1;

//die();
$arExistingProducts = [];
$failedAddProduct = [];

while ($arOffer = $allOffersRes->GetNext()) {
	$PRODUCT_ID = $arOffer['ID'];

	if (CCatalogProduct::IsExistProduct($PRODUCT_ID)) {
		$arExistingProducts[] = $PRODUCT_ID;

		$arFields = Array(
		    "PRODUCT_ID" => $PRODUCT_ID,
		    "CATALOG_GROUP_ID" => $PRICE_TYPE_ID,
		    "PRICE" => 0.0,
		    "CURRENCY" => "RUB",
		);

		$res = CPrice::GetList(
	        [],
	        [
	            "PRODUCT_ID" => $PRODUCT_ID,
	            "CATALOG_GROUP_ID" => $PRICE_TYPE_ID
	        ]
	    );

		if ($arr = $res->Fetch())
		{
		    CPrice::Update($arr["ID"], $arFields);
		}
		else
		{
		    CPrice::Add($arFields);
		}
	} else {
		$arFields = array(
			"ID" => $PRODUCT_ID, 
			"AVAILABLE" => "Y",
			"CAN_BUY_ZERO" => "Y"
		);

		if(CCatalogProduct::Add($arFields)) {
			$arFields = Array(
			    "PRODUCT_ID" => $PRODUCT_ID,
			    "CATALOG_GROUP_ID" => $PRICE_TYPE_ID,
			    "PRICE" => 0.0,
			    "CURRENCY" => "RUB",
			);

			$res = CPrice::GetList(
		        [],
		        [
		            "PRODUCT_ID" => $PRODUCT_ID,
		            "CATALOG_GROUP_ID" => $PRICE_TYPE_ID
		        ]
		    );

			if ($arr = $res->Fetch())
			{
			    CPrice::Update($arr["ID"], $arFields);
			}
			else
			{
			    CPrice::Add($arFields);
			}
		} else {
		    $failedAddProduct[] = $PRODUCT_ID;
		}
	}

	/**/
	$startPrice = 0;

    $arItem = [];
    $arItem['CATALOG_PRODUCT'] = CCatalogProduct::GetByID($PRODUCT_ID);
    $arItem['BASE_PRICE'] = CPrice::GetBasePrice($PRODUCT_ID);
    $arItem['BASE_PRICE_VALUE'] = intval($arItem['BASE_PRICE']["PRICE"]);

    $priceTableRes = CIBlockElement::GetPropertyValues(2, ['ID' => $PRODUCT_ID], false, ['ID' => 47]);
    
    if ($priceTableArray = $priceTableRes->Fetch()) {
        $arItem['PRICE_TABLE'] = json_decode($priceTableArray[47], true);

        $manufacturerRes = CIBlockElement::GetPropertyValues(2, ['ID' => $PRODUCT_ID], false, ['ID' => 44]);

        if ($manufacturerArray = $manufacturerRes->Fetch()) {
            $startPrice = $arItem['BASE_PRICE_VALUE'] + $arItem['PRICE_TABLE'][0]['groups'][1];

            CManufacturer::ApplyMarkup(
                CManufacturer::Get($manufacturerArray[44]), 
                $startPrice
            );
            CUtils::RoundPrice($startPrice);

            CIBlockElement::SetPropertyValuesEx(
				$PRODUCT_ID,
				2,
				['START_PRICE' => $startPrice]
			);
        }
        
    }
	/**/



	/*$element = new CIBlockElement;

	$element->Update(
		$arOffer['ID'],
		[
			'MODIFIED_BY' => $USER->GetId()
		]
	);*/



	/*CPrice::SetBasePrice($arOffer['ID'], 0);
	//$element = new CIBlockElement;
	$element->Update(
		$arOffer['ID'],
		[
			'MODIFIED_BY' => $USER->GetId()
		]
	);*/
}

v_dump(implode(', ', $arExistingProducts));

v_dump(implode(', ', $failedAddProduct));