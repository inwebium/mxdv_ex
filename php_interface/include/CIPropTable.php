<?
/* 
 * Пояснения: 
 * (*)  - Мы принимаем массив array('VALUE' => , 'DESCRIPTION' => ) и должны его же вернуть. Если поле с описанием - оно будет содержаться в соответствующем ключе.
 */ 

class CIPropTable extends CIBlockProperty
{
    /** 
     * Возвращает описание типа свойства. 
     * @return array 
     */ 
    public static function GetUserTypeDescription() 
    { 
        return array( 
            'DESCRIPTION' => 'Таблица размеров/цен', 
            'PROPERTY_TYPE' => 'S', 
            'USER_TYPE' => 'TableSizePrice', 
            'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
            "MULTIPLE" => "Y",
            /*'ConvertToDB' => array(__CLASS__, 'ConvertToDB'), 
            'ConvertFromDB' => array(__CLASS__, 'ConvertFromDB'), */
        ); 
    } 

    /** 
     * Вызывается перед сохранением в БД. 
     * @param array $arProperty массив характеристик св-ва 
     * @param array $arValue массив значения и описания св-ва (*) 
     * @return array 
     */ 
    /*function ConvertToDB($arProperty, $value)
    {
	    $return = false;

   		if (is_array($value) && array_key_exists("VALUE", $value))
   		{
      		$return = array("VALUE" => serialize($value["VALUE"]));

      		if (strlen(trim($value["DESCRIPTION"])) > 0) 
  			{
  				$return["DESCRIPTION"] = trim($value["DESCRIPTION"]);
  			}
   		}

        return $return; 
    }*/

    /** 
     * Форматирование значение из БД. Не применять без явной надобности. 
     * @param array $arProperty массив характеристик св-ва 
     * @param array $arValue массив значения и описания св-ва (*) 
     * @return array 
     */
    /*function ConvertFromDB($arProperty, $value)
    {
   		$return = false;

   		if (!is_array($value["VALUE"]))
   		{
      		$return = array("VALUE" => unserialize($value["VALUE"]));

      		if ($value["DESCRIPTION"])
  			{
  				$return["DESCRIPTION"] = trim($value["DESCRIPTION"]);
  			}
   		}

   		return $return;
    }*/

    function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        return;
    }

    //отображение формы редактирования в админке и в режиме правки
    function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    { 
        $arGroups = Clothes::GetGroups();

        $baseValue = $value['VALUE'];

        $arTable = [];

        $jsTable = '';

        $lastRowN = 0;

        if (strlen($baseValue) > 1) {
        	$arTable = json_decode($baseValue, true);
        }

        foreach ($arTable as $idRow => $arRow)
        {
            if (empty($arRow) || empty($arRow['size']) || count($arRow) < 2)
            {
                unset($arTable[$idRow]);
            }
        }

        if (!empty($arTable) && count($arTable) >= 1)
        {
            $jsTable = json_encode($arTable);
            $lastRowN = count($arTable);
        }

   		ob_start();
        /*echo "<pre>";
        var_dump(json_last_error());
        echo "</pre>";
        echo "<pre>";
        var_dump($baseValue);
        echo "</pre>";
        echo "<pre>";
        var_dump($arTable);
        echo "</pre>";*/
   		?>
   		<script src="/local/templates/main/scripts/libs/jquery-3.3.1.min.js"></script>
   		<script type="text/javascript">
   			var groupsCount = <?=count($arGroups);?>;
            var pricePropertyId = <?=$arProperty['ID'];?>;
            var property<?=$arProperty['ID'];?>LastN = 0;

            <? if ($lastRowN > 0): ?>
                property<?=$arProperty['ID'];?>LastN = <?=$lastRowN;?>;
            <? endif ?>

            var prop<?=$arProperty['ID'];?>Value = {};

            <? if ($jsTable != ''): ?>
                prop<?=$arProperty['ID'];?>Value = JSON.parse('<?=$jsTable;?>');
            <? endif ?>

   			function AddPriceRow()
   			{
                var priceNewN = GetNewN();
                var sizeInputHtml = '<input class="pricelist-size pricelist-input" data-pricen="' + priceNewN + '" size="25" type="text" name="ROW_SIZE_VALUE_' + priceNewN + '" value="" placeholder="Укажите размер" />';
   				var newRowHtml = '<tr id="price_row_new"><td>' + sizeInputHtml + GetHiddenStringInput(priceNewN) + '</td>';

   				prop<?=$arProperty['ID'];?>Value[priceNewN] = {};

   				for (var i = 0; i < groupsCount; i++)
   				{
   					newRowHtml += '<td><input class="pricelist-group pricelist-input" data-pricen="' + priceNewN + '" data-group="' + (i+1) + '" size="3" type="text" name="ROW_PRICE_VALUE_' + priceNewN + '_' + (i+1) + '" value=""></td>';
   				}

   				newRowHtml += '<td style="text-align: center; vertical-align:middle;"></td></tr>';
   				$('#table_price_0 tbody').append(newRowHtml);



   				return false;
   			}

            function GetHiddenStringInput(priceNewN)
            {
            	var strHtml = '';
                //strHtml = '<input class="pricelist-rowvalue" data-pricestring="priceval_' + priceNewN + '" data-pricen="' + priceNewN + '" type="hidden" name="PROP[' + pricePropertyId + '][n' + priceNewN + ']">';

                return strHtml;
            }

            function GetNewN()
            {
                var result = 0;

                if (property<?=$arProperty['ID'];?>LastN == 0) {
                    result = 0;
                    property<?=$arProperty['ID'];?>LastN++;
                } else {
                    //property<?=$arProperty['ID'];?>LastN++;
                    result = property<?=$arProperty['ID'];?>LastN;
                    property<?=$arProperty['ID'];?>LastN++;
                }

                return result;
            }

            function DeleteRow(priceRowN)
            {
                $("input[data-pricestring='priceval_" + priceRowN + "']").val('');
            }

            function GetRowStringValue()
            {

            }



            $(document).on('input', '.pricelist-input', function(e) {
                var thisValue = $(this).val();
                var priceRowNumber = $(this).attr('data-pricen');
                var rowSize = $('.pricelist-size[data-pricen="' + priceRowNumber + '"]').val();

                prop<?=$arProperty['ID'];?>Value[priceRowNumber]['size'] = rowSize;

                if (prop<?=$arProperty['ID'];?>Value[priceRowNumber]['groups'] === undefined) {
                	prop<?=$arProperty['ID'];?>Value[priceRowNumber]['groups'] = {};
                }
                //var resultString = rowSize + ";";

                var groupsPrices = $('.pricelist-group[data-pricen="' + priceRowNumber + '"]');

                console.log("priceRowNumber: ", priceRowNumber);

                $.each(groupsPrices, function(key, element) {
                	var priceGroupNumber = $(element).attr("data-group");
                	var elementValue = $(element).val();

                	//console.log("priceGroupNumber: ", priceGroupNumber);
                	//console.log("elementValue: ", elementValue);

                	if (elementValue === undefined) {
                		prop<?=$arProperty['ID'];?>Value[priceRowNumber]['groups'][priceGroupNumber] = '';
                	} else {
						prop<?=$arProperty['ID'];?>Value[priceRowNumber]['groups'][priceGroupNumber] = elementValue;
                	}

                	
                    //resultString += $(element).val() + ';';
                });

                //console.log(resultString);

                $('input[name="PROP[<?=$arProperty['ID'];?>][n0]"]').val(JSON.stringify(prop<?=$arProperty['ID'];?>Value));
                console.log($('input[name="PROP[<?=$arProperty['ID'];?>][n0]"]').val());
            });
   		</script>
   		<div class="admin_edit-table_wrapper">
   			<input type='hidden' name='PROP[<?=$arProperty['ID'];?>][n0]' value='<?=$jsTable;?>'>
   			<table class="internal admin_edit-table_price" id="table_price_0">
   				<thead>
   					<tr class="heading">
   						<td>
   							Размер
   						</td>
   						<? foreach ($arGroups as $idGroup => $arGroup): ?>
   							<td>
   								Группа <?=$arGroup['UF_NAME'];?>
   							</td>
   						<? endforeach ?>
   					</tr>
   				</thead>
   				<tbody>
		       		<? foreach ($arTable as $idRow => $arRow): ?>
		       			<tr id="price_row_<?=$idRow;?>">
		       				<td>
		       					<input class="pricelist-size pricelist-input" data-pricen="<?=$idRow;?>" size="25" type="text" name="ROW_SIZE_VALUE_<?=$idRow;?>" value="<?=$arRow['size'];?>" />
		       				</td>
		       				<? foreach ($arRow['groups'] as $idGroup => $arGroupValue): ?>
		       					<td>
		       						<input class="pricelist-group pricelist-input" data-pricen="<?=$idRow;?>" data-group="<?=$idGroup;?>" size="3" type="text" name="ROW_PRICE_VALUE_<?=$idRow;?>_<?=$idGroup;?>" value="<?=$arGroupValue;?>">
		       					</td>
							<? endforeach ?>
							<td style="text-align: center; vertical-align:middle;">
								<input type="checkbox" name="ROW_DEL_<?=$idRow;?>" id="ROW_DEL_<?=$idRow;?>" value="Y" class="adm-designed-checkbox">
								<label class="adm-designed-checkbox-label" for="ROW_DEL_<?=$idRow;?>" title=""></label>
							</td>
		       			</tr>
					<? endforeach ?>
	       		</tbody>
       		</table>
       		<div class="admin_edit-table_buttons">
       			<input class="adm-btn-big" onclick="AddPriceRow();" type="button" value="Добавить" title="Добавить строку">
       		</div>
   		</div>      
   		<?
   		$return = ob_get_contents();
   		ob_end_clean();
   		return  $return;        
    }
}