<?
\Bitrix\Main\Loader::includeModule('highloadblock');
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

class CManufacturer
{
	private static $HLBlockId = 3;

	public static function GetList($limit = false)
	{
		$result = false;
		$entity_data_class = CUtils::GetEntityDataClass(self::$HLBlockId);
		$getParams = [
			'select' => ['*'],
		   	'order' => ['ID' => 'ASC']
		];

		if ($limit)
		{
			$getParams['limit'] = $limit;
		}
		
		$rsData = $entity_data_class::getList($getParams);

		$arElements = [];
		while($el = $rsData->fetch())
		{
		    $arElements[$el['UF_XML_ID']] = $el;
		}

		$result = $arElements;

		return $result;
	}

	public static function Get($xmlId)
	{
		$result = false;
		$entity_data_class = CUtils::GetEntityDataClass(self::$HLBlockId);
		$getParams = [
			'select' => ['*'],
			'filter' => ['UF_XML_ID' => $xmlId],
		   	'order' => ['ID' => 'ASC']
		];
		
		$rsData = $entity_data_class::getList($getParams);

		$arElements = [];
		while($el = $rsData->fetch())
		{
		    $arElements = $el;
		}

		$result = $arElements;

		return $result;
	}

	public static function GetMarkup(&$arManufacturer)
	{
		$markup = $arManufacturer['UF_MARKUP'];

		return $markup;
	}

	public static function GetMarkupStep(&$arManufacturer)
	{
		$markup = $arManufacturer['UF_MARKUP_STEP'];

		return $markup;
	}

	public static function GetMarkupValue(&$arManufacturer, &$price)
	{
		$markup = self::GetMarkup($arManufacturer);
		$markupValue = 0;

		if (strpos($markup, '%'))
		{
			$onePercent = $price / 100;
			$markupValue = $onePercent * $markup;
		}
		else
		{
			$markupValue = $markup;
		}

		return $markupValue;		
	}

	public static function GetMarkupStepValue(&$arManufacturer, &$price, $group)
	{
		if ($group < 1) {
			$group = 0;
		}

		$markup = self::GetMarkupStep($arManufacturer);
		$markupValue = 0;

		if (strpos($markup, '%'))
		{
			$onePercent = $price / 100;
			$markupValue = $onePercent * $markup;
		}
		else
		{
			$markupValue = $markup * ($group - 1);
		}

		return $markupValue;		
	}

	public static function ApplyMarkup(&$arManufacturer, &$price)
	{
		$markup = self::GetMarkup($arManufacturer);

		$markupValue = self::GetMarkupValue($arManufacturer, $price);

		$price = $price + $markupValue;
	}

	public static function ApplyMarkupStep(&$arManufacturer, &$price, $group)
	{
		$markupStepValue = self::GetMarkupStepValue($arManufacturer, $price, $group);

		$price = $price + $markupStepValue;
	}

}