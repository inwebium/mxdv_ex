<?
class CReview
{
	private static $IBlockId = 5;
	
	public static function Add($offerIblockId, $offerId, $text, $name, $files)
	{
		$result = false;

		return $result;
	}

	public static function GetCatalogTree($offerIblockId, $offerId)
	{
		$result = false;

		return $result;
	}

	public static function CreateCatalogTree($offerIblockId, $offerId)
	{
		$result = false;

		return $result;
	}

	public static function GetList($offerId = false, $limit = false)
	{
		$result = [];

		$arOrder = ['SORT' => 'ASC', 'ACTIVE_FROM' => 'DESC'];
		$arFilter = ['IBLOCK_ID' => self::$IBlockId, 'ACTIVE' => 'Y', 'PROPERTY_OFFER' => $offerId];
		$arSelect = ['IBLOCK_ID', 'ID', 'ACTIVE', 'ACTIVE_FROM', 'NAME', 'DETAIL_TEXT', 'PROPERTY_NAME', 'PROPERTY_IMAGES', 'PROPERTY_OFFER', 'PROPERTY_RESPONSE'];

		$arNavStartParams = false;

		if ($limit > 0)
		{
			$arNavStartParams = ['nTopCount' => $limit];
		}

		$resElements = CIBlockElement::GetList($arOrder, $arFilter, false, $arNavStartParams, $arSelect);

		while($arElement = $resElements->GetNext())
		{
 			$result[$arElement['ID']] = $arElement;
		}

		return $result;
	}
}