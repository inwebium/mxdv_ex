<?
\Bitrix\Main\Loader::includeModule('highloadblock');
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

class CCharacteristics
{
	private static $characteristicsHLBlockId = 8;

	public static function GetByCode($code, $limit = false)
	{
		$result = false;
		$entity_data_class = CUtils::GetEntityDataClass(self::$characteristicsHLBlockId);
		$getParams = [
			'select' => ['*'],
		   	'order' => ['ID' => 'ASC'],
		   	'filter' => ['UF_XML_ID' => $code]
		];

		if ($limit)
		{
			$getParams['limit'] = $limit;
		}
		
		$rsData = $entity_data_class::getList($getParams);

		$arElements = [];
		while($el = $rsData->fetch())
		{
		    $arElements[] = $el;
		}

		if (count($arElements) == 1)
		{
			$result = $arElements[0];
		}
		else
		{
			$result = $arElements;
		}

		return $result;
	}
}
