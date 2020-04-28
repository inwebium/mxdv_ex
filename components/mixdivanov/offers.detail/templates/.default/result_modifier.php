<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?

/*$arWatermark = [
    [
        "name" => "watermark",
        "position" => "bottomright",
        "type" => "image",
        "size" => "real",
        "file" => $_SERVER["DOCUMENT_ROOT"].'/upload/watermark.png',
        "fill" => "exact",
    ]
];

echo "<pre>";
var_dump($arResult["DETAIL_PICTURE"]);
echo "</pre>";


$arFileTmp = CFile::ResizeImageGet(
    $arResult['DETAIL_PICTURE']['ID'],
    ["width" => 800, "height" => 600],
    BX_RESIZE_IMAGE_PROPORTIONAL,
    true,
    $arWaterMark
);

echo "<pre>";
var_dump($arFileTmp);
echo "</pre>";*/

$arResult["DETAIL_PICTURE"]["SRC"] = getResizedImgOrPlaceholder($arResult["DETAIL_PICTURE"]["ID"], 800);

foreach ($arResult['DISPLAY_PROPERTIES']['MORE_PHOTO']['VALUE'] as $key => $photoId) {
	$arResult['DISPLAY_PROPERTIES']['MORE_PHOTO']['RESIZED'][] = getResizedImgOrPlaceholder($photoId, 800);;
}
?>