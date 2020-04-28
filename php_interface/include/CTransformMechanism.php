<?
\Bitrix\Main\Loader::includeModule('highloadblock');
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

class CTransformMechanism
{
	private static $mechanismsHLBlockId = 4;
	
	public static function GetById($id)
	{
		return $id;
	}

	public static function GetList($limit = false)
	{
		$result = false;
		$entity_data_class = CUtils::GetEntityDataClass(self::$mechanismsHLBlockId);
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

	public static function GetByCode($code, $limit = false)
	{
		$result = false;
		$entity_data_class = CUtils::GetEntityDataClass(self::$mechanismsHLBlockId);
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

	public static function GetSectionSubmenu($sectionId, $iblockId = 2, $notEmpty = true, $depthLevel = 2, $sectionCode = false)
	{
		$result = false;

		$arFilter = [
			'IBLOCK_ID' => $iblockId,
			'GLOBAL_ACTIVE' => 'Y',
			'IBLOCK_ACTIVE' => 'Y',
			'DEPTH_LEVEL' => $depthLevel,
			'CNT_ACTIVE' => 'Y',
			'SECTION_ID' => $sectionId,
		];

		$arOrder = [
			"SORT" => "asc",
			"element_cnt" => "desc",
		];

		$rsSections = CIBlockSection::GetList($arOrder, $arFilter, true, [
			"IBLOCK_ID",
			"ID",
			"DEPTH_LEVEL",
			"NAME",
			"SECTION_PAGE_URL",
			"UF_ICON",
		]);

		while($arSection = $rsSections->GetNext())
		{
			if ($arSection['ELEMENT_CNT'] > 0)
			{
				$result[] = $arSection;
			}
			else
			{
				continue;
			}
		}

		/*$arMechanisms = self::GetList();

		echo "<pre>";
		var_dump($arMechanisms);
		echo "</pre>";

		$arOrder = ["ID" => "ASC"];
		$arSelect = Array("IBLOCK_ID", "ID", "PROPERTY_MECHANISM");
		$arFilter = Array("IBLOCK_ID" => $iblockId, "ACTIVE"=>"Y", "SECTION_ID" => $sectionId);

		$resElements = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
		
		while($arElement = $resElements->GetNext())
		{

		}*/

		return $result;
	}
}