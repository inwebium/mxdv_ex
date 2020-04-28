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
?>
<div class="catalog-section-top">
	<? foreach ($arResult['SECTIONS'] as $key => $section): ?>
		<div class="section-block js-section-block js-collapsed" id="section-block-<?=$section['ID'];?>">
			<div class="image">
				<img src="<?=CFile::GetPath($section['PICTURE']);?>" />
			</div>
			<div class="content">
				<div class="name">
					<?=$section['NAME'];?>
				</div>
				<div class="links">
					<ul>
						<? 
						$linksCounter = 0;
						foreach ($section['LINKS'] as $link): ?>
							<li class="links-item<? if ($linksCounter >= 4): ?> js-hidden" style="display:none;<? endif; ?>">
								<a href="<?=$link['HREF'];?>"><?=$link['NAME'];?></a>
							</li>
						<? 
						$linksCounter++;
						endforeach; ?>
					</ul>
				</div>
				<div class="bottom">
					<? if ($linksCounter > 4): ?>
						<a href="#section-block-<?=$section['ID'];?>" class="js-more-links">Показать еще</a>
					<? endif ?>
				</div>
			</div>
		</div>
	<? endforeach; ?>
</div>