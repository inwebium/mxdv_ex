<?
\Bitrix\Main\Loader::includeModule('highloadblock');
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

class Clothes
{
	private static $GroupsHLBlockId = 7;
	private static $IBlockId = 4;

	public static function GetGroups()
	{
		$result = false;

		$hlBlock = HL\HighloadBlockTable::getById(self::$GroupsHLBlockId)->fetch();

		if (!empty($hlBlock))
		{
			$entity = HL\HighloadBlockTable::compileEntity($hlBlock);
			$entityDataClass = $entity->getDataClass();
			$entityTableName = $hlBlock['TABLE_NAME'];
			$arFilter = [];
			$sTableID = 'tbl_'.$entityTableName;

			$rsData = $entityDataClass::getList(
				[
					"select" => array('*'), //выбираем все поля
					"filter" => $arFilter,
					"order" => array("ID"=>"ASC")
				]
				);

			$rsData = new CDBResult($rsData, $sTableID);

			while($arRes = $rsData->Fetch())
			{
				$result[] = $arRes;
			}
		}

		return $result;
	}

	public static function GetById($id)
	{
		$result = false;

		$resCloth = CIBlockElement::GetByID($id);

		if ($arCloth = $resCloth->GetNext())
		{
			$resCollection = CIBlockSection::GetList(
			    ['SORT' => 'ASC', 'NAME' => 'ASC'],
			    ['IBLOCK_ID' => self::$IBlockId, 'ID' => $arCloth['IBLOCK_SECTION_ID']],
			    false,
			    ['IBLOCK_ID', 'ID', 'NAME', 'CODE', 'UF_CLOTHTYPE']
			);

			$arCollection = $resCollection->GetNext();

			$clothesTypes = CClothesTypes::GetAll();

			$arCollection['CLOTH_TYPE'] = $clothesTypes[$arCollection['UF_CLOTHTYPE']]['UF_NAME'];

			$arCloth['SECTION'] = $arCollection;

			$result = $arCloth;
		}

		return $result;
	}

	public static function GetCollectionsList($group = 1, $type = 0, $getFromAllGroups = false)
	{
		$result = false;

		$arFilter = ['IBLOCK_ID' => self::$IBlockId, 'UF_GROUP' => $group, 'ACTIVE' => 'Y'];
		$arSelect = ['IBLOCK_ID', 'ID', 'NAME', 'CODE', 'PICTURE', 'DESCRIPTION', 'UF_EXAMPLES', 'UF_CLOTHTYPE', 'UF_GROUP'];

		if ($type != 0)
		{
			$arFilter['UF_CLOTHTYPE'] = $type;
		}

		if ($getFromAllGroups)
		{
			unset($arFilter['UF_GROUP']);
		}
		
		$resCollections = CIBlockSection::GetList(
		    ['SORT' => 'ASC', 'NAME' => 'ASC'],
		    $arFilter,
		    false,
		    $arSelect
		);

		$clothesTypes = CClothesTypes::GetAll();

		while ($arCollection = $resCollections->GetNext())
		{
			$arCollection['CLOTH_TYPE'] = $clothesTypes[$arCollection['UF_CLOTHTYPE']]['UF_NAME'];
			$result[$arCollection['ID']] = $arCollection;
		}

		return $result;
	}

	public static function GetList($arSectionsIds = false)
	{
		$result = false;

		$arOrder = ['SORT' => 'ASC'];
		$arFilter = ['IBLOCK_ID' => self::$IBlockId, 'ACTIVE' => 'Y'];
		$arSelect = ['IBLOCK_ID', 'ID', 'NAME', 'CODE', 'DETAIL_PICTURE', 'PREVIEW_PICTURE', 'IBLOCK_SECTION_ID'];

		if ($arSectionsIds)
		{
			$arFilter['SECTION_ID'] = $arSectionsIds;
		}

		$resElements = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);

		while($arElement = $resElements->GetNext())
		{
 			$result[$arElement['IBLOCK_SECTION_ID']][$arElement['ID']] = $arElement;
		}

		return $result;
	}

	public static function GetGroupsByType($type)
	{
		$result = false;

		return $result;
	}	
}