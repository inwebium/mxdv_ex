<?
class CCertificates
{
	private static $IBlockId = 9;

	public static function GetList($limit = false)
	{
		$result = [];

		$arOrder = ['SORT' => 'ASC', 'ACTIVE_FROM' => 'DESC'];
		$arFilter = ['IBLOCK_ID' => self::$IBlockId, 'ACTIVE' => 'Y'];
		$arSelect = ['IBLOCK_ID', 'ID', 'ACTIVE', 'ACTIVE_FROM', 'NAME', 'DETAIL_PICTURE'];

		$arNavStartParams = false;

		if ($limit > 0)
		{
			$arNavStartParams = ['nTopCount' => $limit];
		}

		$resElements = CIBlockElement::GetList($arOrder, $arFilter, false, $arNavStartParams, $arSelect);

		while($arElement = $resElements->GetNext())
		{
			$arElement['DETAIL_PICTURE'] = CFile::GetPath($arElement['DETAIL_PICTURE']);
 			$result[$arElement['ID']] = $arElement;
		}

		return $result;
	}
}