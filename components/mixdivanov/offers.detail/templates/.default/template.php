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
$APPLICATION->AddHeadScript('//yastatic.net/es5-shims/0.0.2/es5-shims.min.js');
$APPLICATION->AddHeadScript('//yastatic.net/share2/share.js');

$APPLICATION->AddViewContent(
	'OG_TITLE', 
	'<meta property="og:title" content="' . $arResult["NAME"] . '" />'
);
$APPLICATION->AddViewContent(
	'OG_DESCRIPTION', 
	'<meta property="og:description" content="' . $arResult["DETAIL_TEXT"] . '" />'
);
$APPLICATION->AddViewContent(
	'OG_IMAGE', 
	'<meta property="og:image" content="https://www.mixdivanov.ru' . $arResult["DETAIL_PICTURE"]["SRC"] . '" />'
);
$APPLICATION->AddViewContent(
	'OG_URL', 
	'<meta property="og:url" content="https://www.mixdivanov.ru' . $arResult['DETAIL_PAGE_URL'] . '" />'
);

?>
<div class="product clr" itemscope itemtype="http://schema.org/Product">
	<div class="product-leftside">
		<div class="product-leftside-inner">
			<div class="product-leftside-top table">
				<div class="cell cell_full">
					<div class="breadcrumbs">
						<ul class="breadcrumbs-list clearlist" itemscope itemtype="http://schema.org/BreadcrumbList">
							<? $breadCounter = 1; ?>
							<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
								<a itemprop="item" href="/">Главная</a><meta itemprop="position" content="<?=$breadCounter++;?>">
							</li>
							<?
							foreach ($arResult['SECTION']['PATH'] as $key => $arPath) {
								?>
								<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
									<a itemprop="item" href="<?=$arPath['SECTION_PAGE_URL'];?>"><?=$arPath['NAME'];?></a><meta itemprop="position" content="<?=$breadCounter++;?>">
								</li>
								<?
							}
							?>
							<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
								<span itemprop="item" href="<?=$arResult['DETAIL_PAGE_URL'];?>"><?=$arResult["NAME"];?></span><meta itemprop="position" content="<?=$breadCounter++;?>">
							</li>
						</ul>
					</div>
					<h1 itemprop="name"><?=$arResult["NAME"]?></h1>
					<a href="#" class="product-type js-mechanism-tooltip">
						<i><?=$arResult['MECHANISM']['UF_NAME'];?></i> 
						<span class="tooltip">
							<span class="tooltip__icon">?</span>
							<span class="tooltip__text js-mechanism-tooltip-text" style="display:none;"><img src="<?=CFile::GetPath($arResult['MECHANISM']['UF_IMAGE']);?>" /><?=$arResult['MECHANISM']['UF_DESCRIPTION'];?></span>
						</span>
					</a>
				</div>

				<?
				//Проверяем, есть ли данный товар в отложенных
				/*$curProductId = $arResult['ID'];
				$dbBasketItems = CSaleBasket::GetList(
				    array(
				        "NAME" => "ASC",
				        "ID" => "ASC"
				    ),
				    array(
				        "FUSER_ID" => CSaleBasket::GetBasketUserID(),
				        "LID" => SITE_ID,
				        "PRODUCT_ID" => $curProductId,
				        "ORDER_ID" => "NULL",
				        "DELAY" => "Y"
				    ),
				    false,
				    false,
				    array("PRODUCT_ID")
				);
				while ($arItems = $dbBasketItems->Fetch())
				{
				    $itInDelay = $arItems['PRODUCT_ID'];
				}


				//Добавляем к кнопке class
				if ( (in_array($arResult["ID"], $delaydBasketItems)) || (isset($itInDelay)) ) { echo 'in_wishlist'; }*/
				?>
				
				<div class="cell">
					<div class="product-actions">
						<a href="javascript:void(0)" class="product-favorite wishbtn <? if (in_array($arResult["ID"],$arBasketItems )) echo 'in_wishlist '; ?>" onclick="add2wish(
           					'<?=$arResult["ID"]?>',
           					 '<?=$arResult["CATALOG_PRICE_ID_1"]?>',
           					 '<?=$arResult["CATALOG_PRICE_1"]?>',
           					 '<?=$arResult["NAME"]?>',
           					 '<?=$arResult["DETAIL_PAGE_URL"]?>',
           					 this)">Добавить в избранное</a>
						<div class="product-share">
							<div class="product-share__title">Поделиться в соц.сетях:</div>
							<div class="product-share__vidget"><div class="ya-share2" data-services="collections,vkontakte,facebook,odnoklassniki,twitter,viber,whatsapp,telegram"></div></div>
						</div>
					</div>
				</div>

			</div>
			<div class="product-slider-section">
				<div class="product-slider-wrap">
					<div class="product-slider">
						<div class="product-slider__slide">
							<a href="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" class="product-slider__item fancybox" data-fancybox="offer-gallery">
								<span><img src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" alt="image" itemprop="image"></span>
							</a>
						</div>

						<? foreach ($arResult['DISPLAY_PROPERTIES']['MORE_PHOTO']['RESIZED'] as $key => $photo): ?>
							<div class="product-slider__slide">
								<a href="<?=$photo;?>" class="product-slider__item fancybox" data-fancybox="offer-gallery">
									<span><img src="<?=$photo;?>" alt="image"></span>
								</a>
							</div>
						<? endforeach ?>
					</div>
				</div>
				<div class="product-pager">
					<a href="#" data-slide-index="0" class="product-pager__item"><span><img src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" alt="image"></span></a>
					<? 
					$i = 1;
					foreach ($arResult['DISPLAY_PROPERTIES']['MORE_PHOTO']['VALUE'] as $key => $photoId) 
					{
						?>
						<a href="#" data-slide-index="<?=$i;?>" class="product-pager__item"><span><img src="<?=CFile::GetPath($photoId);?>" alt="image"></span></a>
						<?
						$i++;
					}
					?>
				</div>
				<div class="product-slider-text">
					<div class="product-mobile__price" style="display: none;"><?=$arResult['START_PRICE'];?>Р.</div>
					<div class="product-mobile__delivery" style="display: none;">Доставка 2-3 дня</div>
					<div class="product-slider-text__info">Модель представленная на фото выполнена в <?=$arResult['DISPLAY_PROPERTIES']['PHOTO_CLOTH_GROUP']['VALUE'];?> группе ткани</div>
					<div class="product-slider-text__price">Стоимость - <?=($arResult['BASE_PRICE_VALUE'] + $arResult['PRICE_TABLE'][0]['groups'][$arResult['DISPLAY_PROPERTIES']['PHOTO_CLOTH_GROUP']['VALUE']]);?> р</div>
				</div>
			</div>

			<? if (count($arResult['DESIGNS']) > 0): ?>
				<div class="ready-designs">
					<div class="ready-designs__title">Готовые дизайны</div>
					<div class="ready-designs-carousel-wrap">
						<div class="ready-designs-carousel">
							<? foreach ($arResult['DESIGNS'] as $designId => $arDesign): ?>
								<div class="ready-designs-carousel-slide">
									<a href="/designs/<?=$arDesign['CODE'];?>/" class="ready-designs-item">
										<span class="ready-designs-item__image"><img src="<?=CFile::GetPath($arDesign['DETAIL_PICTURE']);?>" alt="image"></span>
										<span class="ready-designs-item__price"><?=($arResult['BASE_PRICE_VALUE'] + $arResult['PRICE_TABLE'][0]['groups'][$arDesign['PROPERTY_PHOTO_CLOTH_GROUP_VALUE']]);?> Р</span>
									</a>
								</div>
							<? endforeach ?>
						</div>
					</div>
				</div>
			<? endif ?>

			<div class="product-tabs">
				<div class="product-tabs-list">
					<div class="product-tabs__btn active">Описание</div>
					<div class="product-tabs__btn">Характеристики</div>
					<div class="product-tabs__btn">Отзывы <span><?=count($arResult['REVIEWS']);?></span></div>
					<div class="product-tabs__btn">Вопрос-ответ</div>
				</div>
				<div class="product-tabs-body">
					<div class="product-tabs-section active">
						<div class="product-tabs-section__btn">Описание</div>
						<div class="product-tabs-section-inner">
							<div class="product__description" itemprop="description">
								<?=$arResult['DETAIL_TEXT'];?>
							</div>
						</div>
					</div>

					<? /*ВКЛАДКА ХАРАКТЕРИСТИКИ*/ ?>
					<div class="product-tabs-section">
						<div class="product-tabs-section__btn">Характеристики</div>
						<div class="product-tabs-section-inner">
							<div class="product-characteristics">
								<div class="product-characteristics-row">
									<? foreach ($arResult['CHARACTERISTICS'] as $key => $arCharacteristic): ?>
										<div class="product-characteristics-column">
											<div class="product-characteristics-item">
												<div class="product-characteristics-item__icon"><img src="<?=CFile::GetPath($arCharacteristic['UF_ICON']);?>" width="24px" height="24px" alt="image"></div>
												<div class="product-characteristics-item__title"><?=$arCharacteristic['UF_NAME'];?></div>
											</div>
										</div>
									<? endforeach ?>
								</div>
							</div>
						</div>
					</div>

					<div class="product-tabs-section">
						<div class="product-tabs-section__btn">Отзывы <span><?=count($arResult['REVIEWS']);?></span></div>
						<div class="product-tabs-section-inner">
							<div class="product-review">
								<a href="#" class="product-review__add btn btn-addreview" data-offername='<?=$arResult["NAME"];?>' data-offerid='<?=$arResult["ID"];?>'>Добавить отзыв</a>
								<div class="product-review-list">

									<? foreach ($arResult['REVIEWS'] as $reviewId => $arReview): ?>
										<div class="product-review-item">
											<div class="product-review-item-top">
												<div class="product-review-item__author"><?=$arReview['PROPERTY_NAME_VALUE'];?></div>
												<div class="product-review-item__date"><?=FormatDateFromDB($arReview['ACTIVE_FROM'], 'DD MMMM YYYY');?></div>
											</div>
											<div class="product-review-item__text">
												<?=$arReview['DETAIL_TEXT'];?>
											</div>
											<?/*<div class="product-review-item-images">
												<div class="product-review-item__image"><img src="images/content/product/product-review-image1.jpg" alt="image"></div>
												<div class="product-review-item__image"><img src="images/content/product/product-review-image2.jpg" alt="image"></div>
												<div class="product-review-item__image"><img src="images/content/product/product-review-image3.jpg" alt="image"></div>
											</div>*/?>
										</div>
									<? endforeach; ?>

								</div>
								<? if (count($arResult['REVIEWS']) > 2): ?>
									<a href="#" class="product-review__showall js-offer-all_reviews">Показать все</a>
								<? endif; ?>
							</div>
						</div>
					</div>
					<div class="product-tabs-section">
						<div class="product-tabs-section__btn">Вопрос-ответ</div>
						<div class="product-tabs-section-inner">
							<div class="product-faq">
								<div class="product-faq-list">
									<div class="product-faq-item">
										<div class="product-faq-item__title">Какие обивочные ткани легко чистятся?</div>
										<div class="product-faq-item__text">Обивка – верхний и легкоранимый слой дивана. Именно она подвержена самому сильному воздействию на протяжении всего срока службы мебели. Как выбрать ткань, чтобы ее было легко чистить?</div>
									</div>
									<div class="product-faq-item">
										<div class="product-faq-item__title">8 признаков качественного дивана</div>
										<div class="product-faq-item__text">Обивка – верхний и легкоранимый слой дивана. Именно она подвержена самому сильному воздействию на протяжении всего срока службы мебели. Как выбрать ткань, чтобы ее было легко чистить?</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="product-rightside">
		<div class="product-rightside-body">
			<div class="product-price table" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
				<div class="cell cell_full">
					<?/*<div class="product-price__oldprice"><span>9 900Р.</span> <strong class="product-price__discount">-34%</strong></div>*/?>
					<?/*<div class="product-price__profit">Выгода 13 909Р</div>*/?>
				</div>
				<div class="cell">
					<meta itemprop="price" content="<?=$arResult['START_PRICE'];?>">
    				<meta itemprop="priceCurrency" content="RUB">
					<div class="product-price__number js-text_price-top">от <?=$arResult['START_PRICE'];?>Р.</div>
					<link itemprop="availability" href="http://schema.org/InStock">
				</div>
			</div>

			<div class="product-info">
				<? if (!$arResult['IS_DESIGN']): ?>
					<div class="product-info-block">
						<div class="product-cloth">
							<div class="product-cloth-top clr">
								<div class="product-cloth__title">Выберите ткань из каталога</div>
								<a href="#" class="product-cloth__change btn-modal-cloth">Изменить выбор</a>
							</div>
							<div class="product-cloth-row">
								<div class="product-cloth-item js-cloth_target" data-target="main">
									<div class="product-cloth-item__image">
										<div class="product-cloth-item__delete js-cloth_target-clear"></div>
										<span class="js-cloth_target-image"></span>
									</div>
									<div class="product-cloth-item__title">Основная</div>
								</div>
								<div class="product-cloth-item js-cloth_target" data-target="second">
									<div class="product-cloth-item__image">
										<div class="product-cloth-item__delete js-cloth_target-clear"></div>
										<span class="js-cloth_target-image"></span>
									</div>
									<div class="product-cloth-item__title">Ткань-компаньон</div>
								</div>
								<?
								if (isset($arResult['OPTIONS']['third_cloth'])) {
									?>
									<div class="product-cloth-item js-cloth_target" data-target="third">
										<div class="product-cloth-item__image">
											<div class="product-cloth-item__delete js-cloth_target-clear"></div>
											<span class="js-cloth_target-image"></span>
										</div>
										<div class="product-cloth-item__title">3я ткань<?if($arResult['OPTIONS']['third_cloth']['UF_COST']):?> (+<?=$arResult['OPTIONS']['third_cloth']['UF_COST'];?> руб.)<?endif?></div>
									</div>
									<?
								}
								if (isset($arResult['OPTIONS']['fourth_cloth'])) {
									?>
									<div class="product-cloth-item js-cloth_target" data-target="fourth">
										<div class="product-cloth-item__image">
											<div class="product-cloth-item__delete js-cloth_target-clear"></div>
											<span class="js-cloth_target-image"></span>
										</div>
										<div class="product-cloth-item__title">4я ткань<?if($arResult['OPTIONS']['fourth_cloth']['UF_COST']):?> (+<?=$arResult['OPTIONS']['fourth_cloth']['UF_COST'];?> руб.)<?endif?></div>
									</div>
									<?
								}
								?>
							</div>
							<a href="#" class="product-cloth__instruction js-cloth_instruction">
								<i>Как выбрать ткань?</i> 
								<span class="tooltip">
									<span class="tooltip__icon">?</span>
									<?/*<span class="tooltip__text">Тут будет текст подсказки</span>*/?>
								</span>
							</a>
						</div>
					</div>
					<div class="product-info-block">
						<div class="product-photo-order checkbox">
							<input class="js-order-photo" type="checkbox" id="product-photo-order1" value="product-photo-order1">
							<label for="product-photo-order1">Заказать, как на фото</label>
						</div>
					</div>

				<? endif ?>

				<? if (isset($arResult['ARMRESTS_COLORS']) && count($arResult['ARMRESTS_COLORS']) > 0): ?>
					<div class="product-info-block">
						<div class="product-info-block__title">Выбрать цвет подлокотника</div>
						<div class="product-color-select select js-armrest-color">
							<div class="select-title">
								<div class="select-title__arrow"></div>
								<? foreach ($arResult['ARMRESTS_COLORS'] as $key => $arArmrestColor): ?>
									<? if ($arArmrestColor['UF_DEFAULT'] == 1): ?>
										<div class="select-title__value"><i class="product-select__color" style="background: url('<?=CFile::GetPath($arArmrestColor['UF_IMAGE']);?>');"></i><?=$arArmrestColor['UF_NAME'];?><? if ($arArmrestColor['UF_MARKUP'] > 0): ?> (+<?=$arArmrestColor['UF_MARKUP'];?>Р.)<? endif ?></div>
										<? break; ?>
									<? endif ?>
								<? endforeach ?>
							</div>
							<div class="select-options">
								<div class="select-options-inside">
									<? foreach ($arResult['ARMRESTS_COLORS'] as $key => $arArmrestColor): ?>
										<? if ($arArmrestColor['UF_DEFAULT'] != 1): ?>
											<div class="select-options__value" data-value="<?=$arArmrestColor['UF_NAME'];?>" data-price="<?=$arArmrestColor['UF_MARKUP'];?>"><i class="product-select__color" style="background: url('<?=CFile::GetPath($arArmrestColor['UF_IMAGE']);?>');"></i><span><?=$arArmrestColor['UF_NAME'];?><? if ($arArmrestColor['UF_MARKUP'] > 0): ?> (+<?=$arArmrestColor['UF_MARKUP'];?>Р.)<? endif ?></span></div>
										<? else: ?>
											<div class="select-options__value active" data-value="<?=$arArmrestColor['UF_NAME'];?>" data-price="<?=$arArmrestColor['UF_MARKUP'];?>"><i class="product-select__color" style="background: url('<?=CFile::GetPath($arArmrestColor['UF_IMAGE']);?>');"></i><span><?=$arArmrestColor['UF_NAME'];?><? if ($arArmrestColor['UF_MARKUP'] > 0): ?> (+<?=$arArmrestColor['UF_MARKUP'];?>Р.)<? endif ?></span></div>
										<? endif ?>
									<? endforeach ?>
								</div>
								<? foreach ($arResult['ARMRESTS_COLORS'] as $key => $arArmrestColor): ?>
									<? if ($arArmrestColor['UF_DEFAULT'] == 1): ?>
										<input type="hidden" name="ARMRESTS_COLOR" value="<?=$arArmrestColor['UF_NAME'];?>" />
									<? endif ?>
								<? endforeach ?>
							</div>
						</div>
					</div>
				<? endif ?>

				<? if (isset($arResult['PROPERTIES']['CORNER_CHOOSE']['VALUE']) && $arResult['PROPERTIES']['CORNER_CHOOSE']['VALUE'] == 'Y'): ?>
					<div class="product-info-block">
						<div class="product-info-block__title">Выбор угла</div>
						<div class="product-corner">
							<div class="product-corner__item radio">
								<input name="CORNER_POSITION" type="radio" id="product-corner-left" value="Левый">
								<label for="product-corner-left">Левый</label>
								<div class="product-corner__image"><img src="<?=SITE_TEMPLATE_PATH;?>/images/icon-corner-left.svg" width="39px" height="28px" alt="image"></div>
							</div>
							<div class="product-corner__item radio">
								<input name="CORNER_POSITION" type="radio" id="product-corner-right" value="Правый" checked>
								<label for="product-corner-right">Правый</label>
								<div class="product-corner__image"><img src="<?=SITE_TEMPLATE_PATH;?>/images/icon-corner-right.svg" width="39px" height="28px" alt="image"></div>
							</div>
						</div>
					</div>
				<? endif ?>
				<? /*РАЗМЕРЫ*/ ?>
				<div class="product-info-block">
					<div class="product-info-block__title">Габаритные размеры (спальные):</div>
					<div class="product-sizes">
						<? 
						$counterSizes = 0;
						foreach ($arResult['PRICE_TABLE'] as $key => $arPriceRow): ?>
							<div class="product-sizes__item radio">
								<input name="SIZE" type="radio" id="product-sizes<?=$key;?>" value="<?=$key;?>" <? if ($counterSizes==0): ?>checked<? endif ?>>
								<label for="product-sizes<?=$key;?>"><?=$arPriceRow['size'];?></label>
							</div>
							<? $counterSizes++; ?>
						<? endforeach ?>
						
					</div>
				</div>

				<div class="product-info-block">
					<div class="product-options">
						<?
						foreach ($arResult['OPTIONS'] as $key => $arOption)
						{
							if (in_array($arOption['UF_XML_ID'], ['third_cloth', 'fourth_cloth'])) {
								?>
								<div class="product-options__item checkbox" style="display: none;">
									<input 
										type="checkbox" 
										class="js-options-checkbox" 
										id="product-option<?=$arOption['ID'];?>" 
										value="<?=$arOption['UF_NAME'];?>" 
										data-id="<?=$arOption['ID'];?>" 
										data-xmlid="<?=$arOption['UF_XML_ID'];?>"
										data-price="<?if($arOption['UF_COST']):?><?=$arOption['UF_COST'];?><?else:?>0<?endif?>">
									<label for="product-option<?=$arOption['ID'];?>"><?=$arOption['UF_NAME'];?><?if($arOption['UF_COST']):?> (+<?=$arOption['UF_COST'];?> руб.)<?endif?></label>
								</div>
								<?
							} else {
								?>
								<div class="product-options__item checkbox">
									<input 
										type="checkbox" 
										class="js-options-checkbox" 
										id="product-option<?=$arOption['ID'];?>" 
										value="<?=$arOption['UF_NAME'];?>" 
										data-id="<?=$arOption['ID'];?>" 
										data-xmlid="<?=$arOption['UF_XML_ID'];?>"
										data-price="<?if($arOption['UF_COST']):?><?=$arOption['UF_COST'];?><?else:?>0<?endif?>">
									<label for="product-option<?=$arOption['ID'];?>"><?=$arOption['UF_NAME'];?><?if($arOption['UF_COST']):?> (+<?=$arOption['UF_COST'];?> руб.)<?endif?></label>
								</div>
								<?
							}
						}
						?>
					</div>
				</div>
			</div>
			<div class="product-total clr">
				<div class="product-total-info">
					<?/*<div class="product-total__oldprice"><span>9 900Р.</span> <strong class="product-total__discount">-34%</strong></div>*/?>
					<div class="product-total__price"><span class="js-final_price"><?=$arResult['START_PRICE'];?></span><span class="js-final_price-currency">Р.</span></div>
					<?/*<div class="product-total__profit">Выгда 13 909Р</div>*/?>
					<div class="product-total__clothprice">(Цена в <span class="js-price_block-cloth_group">1</span>-ой группе тканей)</div>
					<div class="product-total__delivery">Доставка: 2-3 дня</div>
				</div>
				<a href="#" class="product-total__addcart btn js-add_to_basket">В корзину</a>
			</div>
		</div>

		<? if (!empty($arResult['CERTIFICATES'])): ?>
			<div class="product-certificate">
				<div class="product-certificate-top clr">
					<div class="product-certificate__title">Сертификаты</div>
					<div class="product-certificate-carousel-actions">
						<div class="product-certificate-carousel__info"></div>
						<div class="product-certificate-carousel-arrows">
							<div class="product-certificate__arrow arrow-prev"></div>
							<div class="product-certificate__arrow arrow-next"></div>
						</div>
					</div>
				</div>
				<div class="product-certificate-carousel">
					<? foreach ($arResult['CERTIFICATES'] as $key => $arCertificate): ?>
						<div class="product-certificate-slide">
							<div class="product-certificate__item"><img src="<?=$arCertificate['DETAIL_PICTURE'];?>" alt="image"></div>
						</div>
					<? endforeach ?>
				</div>
			</div>
		<? endif; ?>

		<!--googleoff: all-->
		<noindex>
			<div class="product-tabs product-tabs-m robots-nocontent">
				<div class="product-tabs-list">
					<div class="product-tabs__btn active">Описание</div>
					<div class="product-tabs__btn">Характеристики</div>
					<div class="product-tabs__btn">Отзывы <span><?=count($arResult['REVIEWS']);?></span></div>
					<div class="product-tabs__btn">Вопрос-ответ</div>
				</div>
				<div class="product-tabs-body">
					<div class="product-tabs-section active">
						<div class="product-tabs-section__btn">Описание</div>
						<div class="product-tabs-section-inner">
							<div class="product__description" itemprop="description">
								<?=$arResult['DETAIL_TEXT'];?>
							</div>
						</div>
					</div>

					<? /*ВКЛАДКА ХАРАКТЕРИСТИКИ*/ ?>
					<div class="product-tabs-section">
						<div class="product-tabs-section__btn">Характеристики</div>
						<div class="product-tabs-section-inner">
							<div class="product-characteristics">
								<div class="product-characteristics-row">
									<? foreach ($arResult['CHARACTERISTICS'] as $key => $arCharacteristic): ?>
										<div class="product-characteristics-column">
											<div class="product-characteristics-item">
												<div class="product-characteristics-item__icon"><img src="<?=CFile::GetPath($arCharacteristic['UF_ICON']);?>" width="24px" height="24px" alt="image"></div>
												<div class="product-characteristics-item__title"><?=$arCharacteristic['UF_NAME'];?></div>
											</div>
										</div>
									<? endforeach ?>
								</div>
							</div>
						</div>
					</div>

					<div class="product-tabs-section">
						<div class="product-tabs-section__btn">Отзывы <span><?=count($arResult['REVIEWS']);?></span></div>
						<div class="product-tabs-section-inner">
							<div class="product-review">
								<a href="#" class="product-review__add btn btn-addreview" data-offername='<?=$arResult["NAME"];?>' data-offerid='<?=$arResult["ID"];?>'>Добавить отзыв</a>
								<div class="product-review-list">

									<? foreach ($arResult['REVIEWS'] as $reviewId => $arReview): ?>
										<div class="product-review-item">
											<div class="product-review-item-top">
												<div class="product-review-item__author"><?=$arReview['PROPERTY_NAME_VALUE'];?></div>
												<div class="product-review-item__date"><?=FormatDateFromDB($arReview['ACTIVE_FROM'], 'DD MMMM YYYY');?></div>
											</div>
											<div class="product-review-item__text">
												<?=$arReview['DETAIL_TEXT'];?>
											</div>
											<?/*<div class="product-review-item-images">
												<div class="product-review-item__image"><img src="images/content/product/product-review-image1.jpg" alt="image"></div>
												<div class="product-review-item__image"><img src="images/content/product/product-review-image2.jpg" alt="image"></div>
												<div class="product-review-item__image"><img src="images/content/product/product-review-image3.jpg" alt="image"></div>
											</div>*/?>
										</div>
									<? endforeach; ?>

								</div>
								<? if (count($arResult['REVIEWS']) > 2): ?>
									<a href="#" class="product-review__showall js-offer-all_reviews">Показать все</a>
								<? endif; ?>
							</div>
						</div>
					</div>
					<div class="product-tabs-section">
						<div class="product-tabs-section__btn">Вопрос-ответ</div>
						<div class="product-tabs-section-inner">
							<div class="product-faq">
								<div class="product-faq-list">
									<div class="product-faq-item">
										<div class="product-faq-item__title">Какие обивочные ткани легко чистятся?</div>
										<div class="product-faq-item__text">Обивка – верхний и легкоранимый слой дивана. Именно она подвержена самому сильному воздействию на протяжении всего срока службы мебели. Как выбрать ткань, чтобы ее было легко чистить?</div>
									</div>
									<div class="product-faq-item">
										<div class="product-faq-item__title">8 признаков качественного дивана</div>
										<div class="product-faq-item__text">Обивка – верхний и легкоранимый слой дивана. Именно она подвержена самому сильному воздействию на протяжении всего срока службы мебели. Как выбрать ткань, чтобы ее было легко чистить?</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</noindex>
		<!--googleon: all-->
	</div>
</div><!-- end product -->
<input type="hidden" name="DEFAULT_CLOTH_GROUP" value="<?=intval($arResult['DEFAULT_CLOTH_GROUP']);?>">
<script type="text/javascript">
	var dataName = '<?=$arResult["NAME"];?>';
	var dataDetailPageUrl = '<?=$arResult["DETAIL_PAGE_URL"];?>';
	var dataId = '<?=$arResult["ID"];?>';
	var dataPrice = <?=$arResult['START_PRICE'];?>;
	var dataCount = 1;

	var basePriceValue = <?=$arResult['BASE_PRICE_VALUE'];?>;
	var startPrice = <?=$arResult['START_PRICE'];?>;

	var priceTable = JSON.parse('<?=$arResult['PROPERTIES']['PRICE_TABLE']['~VALUE'];?>');

	var photoClothGroup = '<?=$arResult['PROPERTIES']['PHOTO_CLOTH_GROUP']['VALUE'];?>';
	<?
	if (!empty($arResult['PROPERTIES']['PHOTO_CLOTH_FIRST']['VALUE'])) {
		?>
		var photoClothFirst = '<?=$arResult['PROPERTIES']['PHOTO_CLOTH_FIRST']['VALUE'];?>';
		<?
	} else {
		?>
		var photoClothFirst = 0;
		<?
	}

	if (!empty($arResult['PROPERTIES']['PHOTO_CLOTH_SECOND']['VALUE'])) {
		?>
		var photoClothSecond = '<?=$arResult['PROPERTIES']['PHOTO_CLOTH_SECOND']['VALUE'];?>';
		<?
	} else {
		?>
		var photoClothSecond = 0;
		<?
	}
	?>
	//var offerClothGroup = <?=intval($arResult['DEFAULT_CLOTH_GROUP']);?>;
</script>
















<?/*
<div class="news-detail">
	<?if($arResult["NAV_RESULT"]):?>
		<?if($arParams["DISPLAY_TOP_PAGER"]):?><?=$arResult["NAV_STRING"]?><br /><?endif;?>
		<?echo $arResult["NAV_TEXT"];?>
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?><br /><?=$arResult["NAV_STRING"]?><?endif;?>
	<?elseif(strlen($arResult["DETAIL_TEXT"])>0):?>
		<?echo $arResult["DETAIL_TEXT"];?>
	<?else:?>
		<?echo $arResult["PREVIEW_TEXT"];?>
	<?endif?>
	<div style="clear:both"></div>
	<br />
	<?foreach($arResult["FIELDS"] as $code=>$value):
		if ('PREVIEW_PICTURE' == $code || 'DETAIL_PICTURE' == $code)
		{
			?><?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?
			if (!empty($value) && is_array($value))
			{
				?><img border="0" src="<?=$value["SRC"]?>" width="<?=$value["WIDTH"]?>" height="<?=$value["HEIGHT"]?>"><?
			}
		}
		else
		{
			?><?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?><?
		}
		?><br />
	<?endforeach;
	foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>

		<?=$arProperty["NAME"]?>:&nbsp;
		<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
			<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
		<?else:?>
			<?=$arProperty["DISPLAY_VALUE"];?>
		<?endif?>
		<br />
	<?endforeach;
	if(array_key_exists("USE_SHARE", $arParams) && $arParams["USE_SHARE"] == "Y")
	{
		?>
		<div class="news-detail-share">
			<noindex>
			<?
			$APPLICATION->IncludeComponent("bitrix:main.share", "", array(
					"HANDLERS" => $arParams["SHARE_HANDLERS"],
					"PAGE_URL" => $arResult["~DETAIL_PAGE_URL"],
					"PAGE_TITLE" => $arResult["~NAME"],
					"SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
					"SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
					"HIDE" => $arParams["SHARE_HIDE"],
				),
				$component,
				array("HIDE_ICONS" => "Y")
			);
			?>
			</noindex>
		</div>
		<?
	}
	?>
</div>
*/?>

	<div class="modal modal_full" style="display: none;">
	    <div class="box-modal" id="modal-cloth">
	        <div class="box-modal_close arcticmodal-close"></div>
			<div class="box-modal-body">
				<div class="modal-cloth__title">Выбор ткани</div>
				<div class="cloth">
					<div class="cloth-chosen">
						<div class="cloth-chosen-list">
							<div class="cloth-chosen-item js-cloth_target active" data-target="main">
								<div class="cell">
									<div class="cloth-chosen-item__image js-cloth_target-image"></div>
									<div class="cloth-chosen-item__delete js-cloth_target-clear"></div>
								</div>	
								<div class="cell cell_full">
									<div class="cloth-chosen-item__type">Основная</div>
									<div class="cloth-chosen-item__title js-cloth_target-name"></div>
								</div>	
							</div>
							<div class="cloth-chosen-item js-cloth_target" data-target="second">
								<div class="cell">
									<div class="cloth-chosen-item__image js-cloth_target-image"></div>
									<div class="cloth-chosen-item__delete js-cloth_target-clear"></div>
								</div>	
								<div class="cell cell_full">
									<div class="cloth-chosen-item__type">Ткань-компаньон</div>
									<div class="cloth-chosen-item__title js-cloth_target-name"></div>
								</div>	
							</div>
							<?
							if (isset($arResult['OPTIONS']['third_cloth'])) {
								?>
								<div class="cloth-chosen-item js-cloth_target" data-target="third">
									<div class="cell">
										<div class="cloth-chosen-item__image js-cloth_target-image"></div>
										<div class="cloth-chosen-item__delete js-cloth_target-clear"></div>
									</div>	
									<div class="cell cell_full">
										<div class="cloth-chosen-item__type">3я ткань<?if($arResult['OPTIONS']['third_cloth']['UF_COST']):?> (+<?=$arResult['OPTIONS']['third_cloth']['UF_COST'];?> руб.)<?endif?></div>
										<div class="cloth-chosen-item__title js-cloth_target-name"></div>
									</div>	
								</div>
							<?
							}
							if (isset($arResult['OPTIONS']['fourth_cloth'])) {
								?>
								<div class="cloth-chosen-item js-cloth_target" data-target="fourth">
									<div class="cell">
										<div class="cloth-chosen-item__image js-cloth_target-image"></div>
										<div class="cloth-chosen-item__delete js-cloth_target-clear"></div>
									</div>	
									<div class="cell cell_full">
										<div class="cloth-chosen-item__type">4я ткань<?if($arResult['OPTIONS']['fourth_cloth']['UF_COST']):?> (+<?=$arResult['OPTIONS']['fourth_cloth']['UF_COST'];?> руб.)<?endif?></div>
										<div class="cloth-chosen-item__title js-cloth_target-name"></div>
									</div>	
								</div>
							<?
							}
							?>
							<div class="cloth-chosen-bottom">
								<div class="cloth-chosen__price"><span class="js-cloth-choosed_price"><?=$arResult["START_PRICE"];?></span>Р.</div>
								<button type="submit" class="cloth-chosen__btn btn js-submit_clothes">Подтвердить выбор</button> 
							</div>
						</div>
					</div>
					<div class="cloth-body">
						<div class="cloth-filter">
							<div class="cloth-filter-block">
								<div class="cloth-filter-block__title">Ценовая группа ткани:</div>
								<div class="cloth-filter-price-row js-cloth_group-container">
									<? foreach ($arResult['CLOTHES']['GROUPS'] as $key => $arGroup): ?>
										<div class="cloth-filter-price-item js-cloth_group" data-group="<?=($key+1);?>" data-id="<?=$arGroup['ID'];?>" data-xmlid="<?=$arGroup['UF_XML_ID'];?>">
											<div class="cloth-filter-price-item__number js-cloth_group-name"><?=$arGroup['UF_NAME'];?></div>
											<div class="cloth-filter-price-item__price js-cloth_group-price"><?=$arResult['BASE_PRICE_VALUE'] + $arResult['PRICE_TABLE'][0]['groups'][$key+1];?></div>
										</div>
									<? endforeach; ?>
								</div>
							</div>
							<div class="cloth-filter-block">
								<div class="cloth-filter-block__title">Тип тканей:</div>
								<div class="cloth-filter-type-row">
									<div class="cloth-filter-type-column">
										<a href="#" class="cloth-filter-type__item js-cloth_type" data-typeid="0" data-xmlid="0">Все типы</a>
									</div>
									<? foreach ($arResult['CLOTHES']['TYPES'] as $key => $arType): ?>
										<div class="cloth-filter-type-column">
											<a href="#" class="cloth-filter-type__item js-cloth_type" data-typeid="<?=$arType['ID'];?>" data-xmlid="<?=$arType['UF_XML_ID'];?>"><?=$arType['UF_NAME'];?></a>
										</div>
									<? endforeach ?>
								</div>							
							</div>
							<div class="cloth-filter-block">
								<div class="cloth-filter-block__title">Колекции:</div>
								<div class="cloth-filter-type-row js-cloth_collection-container">
									<div class="cloth-filter-type-column">
										<?/*<a href="#" class="cloth-filter-type__item js-cloth_collection" data-typeid="0" data-xmlid="0">Коллекция 1</a>*/?>
									</div>
								</div>
							</div>
						</div>
						<div class="cloth-content js-clothes_list">
						</div>
					</div>
				</div>
			</div>
	    </div><!-- end box-modal -->
	</div><!-- end modal-cloth -->

	<div class="modal" style="display: none;">
	    <div class="box-modal" id="modal-cloth-pick">
	        <div class="box-modal_close arcticmodal-close"></div>
			<div class="box-modal-body">
				<div class="modal-cloth-pick__title modal__title js-pick-collection_name">Название коллекции</div>
				<div class="collection-pick">
					<div class="collection-pick__image"><img class="js-pick-cloth_image" src="<?=SITE_TEMPLATE_PATH;?>/images/content/product/pick-cloth.jpg" alt="image"></div>
					<div class="collection-pick__name js-pick-cloth_name">Название ткани</div>
					<div class="collection-pick-variant clr">
						<div class="collection-pick-variant-column">
							<div class="collection-pick-variant__item js-choose_cloth" data-target="main">Основная ткань</div>
						</div>
						<div class="collection-pick-variant-column">
							<div class="collection-pick-variant__item js-choose_cloth" data-target="second">Ткань-компаньон</div>
						</div>
						<div class="collection-pick-variant-column">
							<div class="collection-pick-variant__item <? /*active*/ ?> js-choose_cloth" data-target="third">Третья ткань</div>
						</div>
						<div class="collection-pick-variant-column">
							<div class="collection-pick-variant__item js-choose_cloth" data-target="fourth">Четвертая ткань</div>
						</div>
					</div>
				</div>
			</div>
	    </div><!-- end box-modal -->
	</div><!-- end modal-cloth-pick -->

	<div class="modal" style="display: none;">
	    <div class="box-modal" id="modal-cloth_instructions">
	        <div class="box-modal_close arcticmodal-close"></div>
			<div class="box-modal-body">
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
				tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
				quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
				consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
				cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
				proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</div>
		</div><!-- end box-modal -->
	</div><!-- end modal -->