<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/scripts/libs/jquery-ui.min.js');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/styles/jquery-ui.min.css');
//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/scripts/libs/jquery.waypoints.min.js');

$APPLICATION->AddViewContent(
	'OG_TITLE', 
	'<meta property="og:title" content="' . $arResult["NAME"] . '" />'
);
$APPLICATION->AddViewContent(
	'OG_DESCRIPTION', 
	'<meta property="og:description" content="' . $arResult['SECTION']['PATH'][count($arResult['SECTION']['PATH'])-1]['DESCRIPTION'] . '" />'
);
$APPLICATION->AddViewContent(
	'OG_URL', 
	'<meta property="og:url" content="https://www.mixdivanov.ru' . $arResult['SECTION']['PATH'][count($arResult['SECTION']['PATH'])-1]['SECTION_PAGE_URL'] . '" />'
);

?>

<?$this->SetViewTarget('MAIN_CLASS');?>main_npb<?$this->EndViewTarget();?>
<?$this->SetViewTarget('CENTER_WRAPPER_CLASS');?>center-wrapper_fluid<?$this->EndViewTarget();?>

<div class="catalog-body">

	<div class="catalog-products-row products-row clr js-catalog-section">
		<?
		if (isset($_REQUEST['AJAX']) && $_REQUEST['AJAX'] == 'Y') {
			$APPLICATION->RestartBuffer();
		}
		?>
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<?
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<div class="products-column" id="<?=$this->GetEditAreaId($arItem['ID']);?>" itemscope="" itemtype="http://schema.org/Product">
				<div class="products-item">
					<div class="products-item-body">
						<a href="<?=$arItem["DETAIL_PAGE_URL"];?>" class="products-item__favorite active"></a>
						<div class="products-item__image">
							<a href="<?=$arItem["DETAIL_PAGE_URL"];?>" itemprop="url"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="image" itemprop="image"></a>
						</div>
						<a href="<?=$arItem["DETAIL_PAGE_URL"];?>" class="products-item__type" itemprop="url"><?=$arResult['MECHANISMS'][$arItem['PROPERTIES']['MECHANISM']['VALUE']]['UF_NAME'];?></a>
						<div class="products-item__title"><a href="<?=$arItem["DETAIL_PAGE_URL"];?>" itemprop="url"><span>Диван</span> <?=$arItem["NAME"];?></a></div>
						
						<? if ($arItem['PROPERTIES']['WITHOUT_CUSTOM_CLOTHES']['VALUE'] != 'Y'): ?>
							<div class="products-item-color">
								<div class="products-item-color__title">Другие цвета</div>
								<div class="products-item-color-row">
									<div class="color" style="background-color: #c2a78e;"></div>
									<div class="color" style="background-color: #9c8e82;"></div>
									<div class="color" style="background-color: #8a6a5b;"></div>
									<div class="color" style="background-color: #5d4b3f;"></div>
									<div class="color" style="background-color: #6d4061;"></div>
								</div>
							</div>
						<? endif ?>

						<div class="products-item-priceblock" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
							<div class="cell">
								<meta itemprop="price" content="<?=$arItem['START_PRICE'];?>">
    							<meta itemprop="priceCurrency" content="RUB">

								<? if (isset($arItem['OLD_PRICE']['VALUE'])): ?>
									<div class="products-item__oldprice"><span><?=$arItem['OLD_PRICE']['VALUE'];?>Р.</span> <strong class="products-item__discount">-<?=$arItem['OLD_PRICE']['PERCENT'];?>%</strong></div>
								<? endif ?>

								<div class="products-item__price"><?=$arItem['START_PRICE'];?>Р.</div>

								<? if (isset($arItem['OLD_PRICE']['PROFIT'])): ?>
									<div class="products-item__profit">Выгода <?=$arItem['OLD_PRICE']['PROFIT'];?>Р</div>
								<? endif ?>
								<link itemprop="availability" href="http://schema.org/InStock">
							</div>
						</div>

						<a href="<?=$arItem['DETAIL_PAGE_URL'];?>" class="products-item__addcart"></a>
						<div class="products-item-sizes">
							<div class="products-item-sizes__title">Размер:</div>
							<? foreach ($arItem['PRICE_TABLE'] as $keyPriceRow => $arPriceRow): ?>
								<div class="products-item-sizes__item"><?=$arPriceRow['size'];?></div>
							<? endforeach ?>
						</div>
					</div>
				</div>
			</div>
		<?endforeach;?>
		<?
		if (isset($_REQUEST['AJAX']) && $_REQUEST['AJAX'] == 'Y') {
			die();
		}
		?>
	</div>
	<div class="js-ajax-more"></div>
	<div class="section_description"><?=$arResult['SECTION']['PATH'][count($arResult['SECTION']['PATH'])-1]['DESCRIPTION'];?></div>
</div>
<script>
	var totalPages = 3;
</script>






<?/*
<div class="news-list">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<?foreach($arResult["ITEMS"] as $arItem):?>
	
	<p class="news-item" >
		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img
						class="preview_picture"
						border="0"
						src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
						width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>"
						height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>"
						alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
						title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"
						style="float:left"
						/></a>
			<?else:?>
				<img
					class="preview_picture"
					border="0"
					src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
					width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>"
					height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>"
					alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
					title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"
					style="float:left"
					/>
			<?endif;?>
		<?endif?>
		<?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
			<span class="news-date-time"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?></span>
		<?endif?>
		<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
				<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><b><?echo $arItem["NAME"]?></b></a><br />
			<?else:?>
				<b><?echo $arItem["NAME"]?></b><br />
			<?endif;?>
		<?endif;?>
		<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
			<?echo $arItem["PREVIEW_TEXT"];?>
		<?endif;?>
		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
			<div style="clear:both"></div>
		<?endif?>
		<?foreach($arItem["FIELDS"] as $code=>$value):?>
			<small>
			<?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?>
			</small><br />
		<?endforeach;?>
		<?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
			<small>
			<?=$arProperty["NAME"]?>:&nbsp;
			<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
				<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
			<?else:?>
				<?=$arProperty["DISPLAY_VALUE"];?>
			<?endif?>
			</small><br />
		<?endforeach;?>
	</p>
<?endforeach;?>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>
*/?>