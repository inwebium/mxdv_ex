<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => "Топ каталога",
	"DESCRIPTION" => "Топ каталога",
	"ICON" => "/images/news_list.gif",
	"SORT" => 19,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "content",
		"CHILD" => array(
			"ID" => "offers",
			"NAME" => "Товары",
			"SORT" => 10,
			/*"CHILD" => array(
				"ID" => "news_cmpx",
			),*/
		),
	),
);

?>