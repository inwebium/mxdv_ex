<?
// элементы
AddEventHandler("iblock", "OnBeforeIBlockElementAdd", ["IblockHandlers", "BeforeElementAdd"]);
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", ["IblockHandlers", "BeforeElementUpdate"]);
// разделы
AddEventHandler("iblock", "OnAfterIBlockSectionAdd", Array("IblockHandlers", "AfterSectionAdd"));

class IblockHandlers
{
    protected static $handlerDisallow = false;

    public static function BeforeElementAdd(&$arFields)
    {
        if ($arFields["IBLOCK_ID"] == CLocals::IBLOCK_ID_OFFERS)
        {
            $startPrice = 0;

            $arItem = [];
            $arItem['CATALOG_PRODUCT'] = CCatalogProduct::GetByID($arFields['ID']);
            $arItem['BASE_PRICE'] = CPrice::GetBasePrice($arFields['ID']);
            $arItem['BASE_PRICE_VALUE'] = intval($arItem['BASE_PRICE']["PRICE"]);

            $priceTableRes = CIBlockElement::GetPropertyValues(2, ['ID' => $arFields['ID']], false, ['ID' => 47]);
            
            if ($priceTableArray = $priceTableRes->Fetch()) {
                $arItem['PRICE_TABLE'] = json_decode($priceTableArray[47], true);

                $manufacturerRes = CIBlockElement::GetPropertyValues(2, ['ID' => $arFields['ID']], false, ['ID' => 44]);

                if ($manufacturerArray = $manufacturerRes->Fetch()) {
                    $startPrice = $arItem['BASE_PRICE_VALUE'] + $arItem['PRICE_TABLE'][0]['groups'][1];

                    CManufacturer::ApplyMarkup(
                        CManufacturer::Get($manufacturerArray[44]), 
                        $startPrice
                    );
                    CUtils::RoundPrice($startPrice);
                }
                
            }

            $arFields["PROPERTY_VALUES"][75] = $startPrice;
        }
    }

    /**
     * Заполняет свойство "Стартовая цена", ну или как оно там (Id 75)
     */
    public static function BeforeElementUpdate(&$arFields)
    {
        if ($arFields["IBLOCK_ID"] == CLocals::IBLOCK_ID_OFFERS)
        {
            $startPrice = 0;

            $arItem = [];
            $arItem['CATALOG_PRODUCT'] = CCatalogProduct::GetByID($arFields['ID']);
            $arItem['BASE_PRICE'] = CPrice::GetBasePrice($arFields['ID']);
            $arItem['BASE_PRICE_VALUE'] = intval($arItem['BASE_PRICE']["PRICE"]);

            $priceTableRes = CIBlockElement::GetPropertyValues(2, ['ID' => $arFields['ID']], false, ['ID' => 47]);
            
            if ($priceTableArray = $priceTableRes->Fetch()) {
                $arItem['PRICE_TABLE'] = json_decode($priceTableArray[47], true);

                $manufacturerRes = CIBlockElement::GetPropertyValues(2, ['ID' => $arFields['ID']], false, ['ID' => 44]);

                if ($manufacturerArray = $manufacturerRes->Fetch()) {
                    $startPrice = $arItem['BASE_PRICE_VALUE'] + $arItem['PRICE_TABLE'][0]['groups'][1];

                    CManufacturer::ApplyMarkup(
                        CManufacturer::Get($manufacturerArray[44]), 
                        $startPrice
                    );
                    CUtils::RoundPrice($startPrice);
                }
                
            }

            $arFields["PROPERTY_VALUES"][75] = $startPrice;
        }
    }

    // Попахивает
    public static function AfterSectionAdd(&$arFields)
    {
        if (self::$handlerDisallow) 
           return;

        if (
            $arFields["ID"] > 0 && 
            $arFields['IBLOCK_ID'] == CLocals::IBLOCK_ID_OFFERS
        ) {
            self::$handlerDisallow = true;
            $createdSection = CIBlockSection::GetByID($arFields["ID"]);
            

            if ($arCreatedSection = $createdSection->GetNext()) {
                if (
                    (
                        $arCreatedSection["IBLOCK_SECTION_ID"] != CLocals::SECTION_ID_SALE && 
                        $arCreatedSection["DEPTH_LEVEL"] == 1
                    ) || 
                    (
                        $arCreatedSection["IBLOCK_SECTION_ID"] == CLocals::SECTION_ID_SALE && 
                        $arCreatedSection["DEPTH_LEVEL"] == 2
                    )
                ) {
                    $mechanisms = CTransformMechanism::GetList();

                    foreach ($mechanisms as $key => $mechanism) {
                        $newSection = new CIBlockSection;
                        
                        $arNewFields = Array(
                            'ACTIVE' => 'Y',
                            'IBLOCK_SECTION_ID' => $arCreatedSection["ID"],
                            'IBLOCK_ID' => CLocals::IBLOCK_ID_OFFERS,
                            'NAME' => $mechanism['UF_NAME'],
                            'CODE' => $arFields['CODE'] . '-' . $mechanism['UF_XML_ID'],
                            'SORT' => 10,
                            'UF_LINKED_MECHANISM' => $mechanism['ID'],
                        );

                        $newSectionResult = $newSection->Add($arNewFields);
                    }
                }
            }

            self::$handlerDisallow = false;
        }
    }
}