<?
\Bitrix\Main\Loader::includeModule('highloadblock');
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

class CPopular
{
	private static $entityId = 10;
	
	public static function GetById($id)
	{
		return $id;
	}

	public static function GetList($limit = false)
	{
		$result = false;
		$entity_data_class = CUtils::GetEntityDataClass(self::$entityId);
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
}