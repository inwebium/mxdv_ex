<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => "Счтчик избранных",
	"DESCRIPTION" => "Счтчик избранных",
	"ICON" => "/images/news_detail.gif",
	"SORT" => 30,
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