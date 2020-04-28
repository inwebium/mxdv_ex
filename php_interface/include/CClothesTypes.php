<?
\Bitrix\Main\Loader::includeModule('highloadblock');
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

class CClothesTypes
{
	private static $clothesTypesHLBlockId = 9;
	
	public static function GetById($id)
	{
		return $id;
	}

	public static function GetAll()
	{
		$result = false;
		$entity_data_class = CUtils::GetEntityDataClass(self::$clothesTypesHLBlockId);
		$getParams = [
			'select' => ['*'],
		   	'order' => ['ID' => 'ASC']
		];
		
		$rsData = $entity_data_class::getList($getParams);

		$arElements = [];
		while($el = $rsData->fetch())
		{
		    $arElements[$el['ID']] = $el;
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

	public static function GetByCode($code, $limit = false)
	{
		$result = false;
		$entity_data_class = CUtils::GetEntityDataClass(self::$clothesTypesHLBlockId);
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

	public static function GetDescription($code)
	{
		$elementResult = self::GetByCode($code);
		return $code;
	}
}