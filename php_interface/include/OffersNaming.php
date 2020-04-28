<?
\Bitrix\Main\Loader::includeModule('highloadblock');
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

class OffersNaming
{
	public static function getSectionPrefix($sectionId)
	{
		$sectionResult = CIBlockSection::GetList(
			['ID' => 'DESC'], 
			['IBLOCK_ID' => CLocals::IBLOCK_ID_OFFERS, 'ID' => $sectionId], 
			false,
			['IBLOCK_ID', 'ID', 'NAME', 'UF_OFFER_PREFIX']
		);

		return $sectionResult->GetNext()['UF_OFFER_PREFIX'];
	}

	public static function getTransformationPrefix($transformationId, &$transformationArray = null)
	{

	}

	public static function getPrefixFromSectionPath($sectionPath)
	{
		$result = '';
		$sectionsIds = [];

		foreach ($sectionPath as $key => $arSection) {
			$sectionsIds[] = $arSection['ID'];
		}

		$sectionsIds = array_reverse($sectionsIds);

		$sectionResult = CIBlockSection::GetList(
			['ID' => 'DESC'], 
			['IBLOCK_ID' => CLocals::IBLOCK_ID_OFFERS, 'ID' => $sectionsIds], 
			false,
			['IBLOCK_ID', 'ID', 'NAME', 'UF_OFFER_PREFIX']
		);

		$prefixPropsValues = [];

		while ($arSection = $sectionResult->GetNext()) {
			$prefixPropsValues[$arSection['ID']] = $arSection['UF_OFFER_PREFIX'];
		}

		foreach ($sectionsIds as $sectionId) {
			if (!empty($prefixPropsValues[$sectionId]) && strlen($prefixPropsValues[$sectionId]) > 0) {
				$result = $prefixPropsValues[$sectionId];
				break;
			}
		}

		return $result;
	}
}