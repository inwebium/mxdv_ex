<?
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
CModule::IncludeModule('highloadblock');

class CUtils
{
	public function GetEntityDataClass($HlBlockId)
	{
	    if (empty($HlBlockId) || $HlBlockId < 1)
	    {
	        return false;
	    }

	    $hlblock = HLBT::getById($HlBlockId)->fetch();   
	    $entity = HLBT::compileEntity($hlblock);
	    $entity_data_class = $entity->getDataClass();

	    return $entity_data_class;
	}

	public static function RoundPrice(&$price)
	{
		$price = ceil($price / 10) * 10;
	}
}