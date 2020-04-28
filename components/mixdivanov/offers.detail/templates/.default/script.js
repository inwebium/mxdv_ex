var choosedSize = 0;
//var choosedSize = $('input[name="SIZE"]:checked').val();
var defaultClothId = 0;
var defaultGroup = 1;
var choosedClothes = {
	'main': defaultClothId,
	'second': defaultClothId,
	'third': defaultClothId,
	'fourth': defaultClothId
};
var choosedGroups = {
	'main': defaultGroup,
	'second': defaultGroup,
	'third': defaultGroup,
	'fourth': defaultGroup
};
var choosedOptions = [];

var basePrice = 0;
var optionsPrice = 0;

var isPhotoOrder = 'N';

var currentGroup = 1;
var currentType = 0;

var titleBottom = 0;
var stickyBlockHeight = 0;

$(document).ready(function() {
	// кликнуто Выбрать ткань в карточке товара
	$(".btn-modal-cloth").click(function(event) {
		event.preventDefault();
		$('#modal-cloth').arcticmodal({
			afterOpen: function(data, el) {
				titleBottom = $('.modal-cloth__title').position().top + $('.modal-cloth__title').height() + 20 + 24;
				stickyBlockHeight = $('.cloth-chosen').height();

				$( ".arcticmodal-container" ).scroll({ passive: true }, function(e){
					//e.preventDefault();

					if ($(this).scrollTop() >= titleBottom)
					{
						//$('.cloth-chosen').addClass('fixed');
						$('.cloth-chosen').css({
							'top': ($( ".arcticmodal-container" ).scrollTop() - titleBottom + 10)
						});
					}
					else
					{
						$('.cloth-chosen').css({
							'top': 0
						});
					}

				});
			}
		});

		getClothesList(currentGroup, currentType);
	});

	var offerClothGroup = $('input[name="DEFAULT_CLOTH_GROUP"]').val();
	defaultGroup = offerClothGroup;
	currentGroup = defaultGroup;

	choosedGroups['main'] = defaultGroup;
	choosedGroups['second'] = defaultGroup;
	choosedGroups['third'] = defaultGroup;
	choosedGroups['fourth'] = defaultGroup;
});

// кликнута ткань в выборе тканей
$(document).on('click', '.js-cloth_link', function(event) {
	event.preventDefault();

	var clickedCollection = $(this).parents('.js-collection');
	var clickedCloth = $(this).parents('.js-cloth_block');
	var clickedCollectionName = $(clickedCollection).find('.js-collection_name').text();
	var clickedClothName = $(clickedCloth).attr('data-name');
	var clickedClothSrc = $(this).attr('data-detailsrc'), image = new Image();

	image.onload = function() {
		$('.js-pick-collection_name').text(clickedCollectionName);
		$('.js-pick-cloth_name').text(clickedClothName);
		$('.js-pick-cloth_name').attr('data-id', $(clickedCloth).attr("data-id"));
		$('.js-pick-cloth_name').attr('data-group', $(clickedCollection).attr("data-group"));
		$('.js-pick-cloth_image').attr('src', clickedClothSrc);

		$('#modal-cloth-pick').arcticmodal();
	}
	image.src = clickedClothSrc;
		
});

// Клик по названию коллекции в списке коллекций в окне выбора тканей
$(document).on('click', '.js-cloth_collection', function(event) {
	event.preventDefault();
	var clickedId = $(this).attr('data-id');
	var clickedXmlId = $(this).attr('data-xmlid');

	//console.log($('.js-clothes_list .js-collection[data-id="' + clickedId + '"]').offset().top);
	//console.log($('.js-clothes_list .js-collection[data-id="' + clickedId + '"]').position().top);

	//var collectionTopOffset = $('.js-clothes_list .js-collection[data-id="' + clickedId + '"]').offset().top - titleBottom;
	var collectionTopOffset = $('.js-clothes_list .js-collection[data-id="' + clickedId + '"]').position().top;
	
	//console.log(collectionTopOffset, titleBottom);

	$(".arcticmodal-container").animate({
        scrollTop: collectionTopOffset
    }, 600);
});

// клик по типу тканей в окне выбора тканей
$(document).on('click', '.js-cloth_type', function(event) {
	event.preventDefault();
	var clickedId = $(this).attr('data-typeid');
	var clickedXmlId = $(this).attr('data-xmlid');

	getGroupsWithType(clickedId);
});

// Изменение радио с выбором размера
$(document).on('change', 'input[name="SIZE"]', function(event) {
	event.preventDefault();
	choosedSize = $('input[name="SIZE"]:checked').val();
	calculatePrice();
});

// Изменение чекбоксов опций
$(document).on('change', '.js-options-checkbox', function(event) {
	event.preventDefault();
	choosedOptions = [];
	var options = $('.js-options-checkbox:checked');

	$.each(options, function(index, element) {
		if ($(element).is(':checked')) {
			choosedOptions.push($(element).val());
		}
	});
	
	calculatePrice();
});

// Клик "заказать как на фото"
$(document).on('change', '.js-order-photo', function(event) {
	event.preventDefault();
	$(this).toggleClass('toggled');
	$('.product-cloth').slideToggle();

	if ($(this).hasClass('toggled')) {
		choosedClothes['main'] = photoClothFirst;
		choosedClothes['second'] = photoClothSecond;
		choosedClothes['third'] = defaultClothId;
		choosedClothes['fourth'] = defaultClothId;

		choosedGroups['main'] = photoClothGroup;
		choosedGroups['second'] = photoClothGroup;
		choosedGroups['third'] = defaultGroup;
		choosedGroups['fourth'] = defaultGroup;

		currentGroup = photoClothGroup;

		isPhotoOrder = 'Y';
	} else {
		choosedClothes['main'] = defaultClothId;
		choosedClothes['second'] = defaultClothId;
		choosedClothes['third'] = defaultClothId;
		choosedClothes['fourth'] = defaultClothId;
		
		choosedGroups['main'] = defaultGroup;
		choosedGroups['second'] = defaultGroup;
		choosedGroups['third'] = defaultGroup;
		choosedGroups['fourth'] = defaultGroup;

		currentGroup = defaultGroup;

		isPhotoOrder = 'N';
	}

	calculatePrice();
});

// клик по Добавить в корзину
$(document).on('click', '.js-add_to_basket', function(event) {
	event.preventDefault();

	var cornerPosition = $("input[name='CORNER_POSITION']").val();

	if (cornerPosition === undefined) {
		cornerPosition = 0;
	}

	$.ajax({
		type: "POST",
		url: "/basket/add.php",
  		data: 	'AJAX=Y&' +
				'ACTION=ADD&' +
  				"NAME=" + dataName + '&' +
  				"DETAIL_PAGE_URL=" + dataDetailPageUrl + '&' +
  				"ID=" + dataId + '&' +
  				"PRICE=" + dataPrice + '&' +
  				"COUNT=" + dataCount + '&' +
  				"SIZE=" + priceTable[choosedSize].size + '&' +
  				"IS_PHOTO_ORDER=" + isPhotoOrder + '&' +
  				"CLOTHES=" + JSON.stringify(choosedClothes) + '&' +
  				"OPTIONS=" + JSON.stringify(choosedOptions) + '&' +
  				"CORNER_POSITION=" + cornerPosition,
  		success: function(response){
  		  	window.location.href = "/basket/";
  		}
	});
});

// клик по группе тканей
$(document).on('click', '.js-cloth_group', function(event) {
	event.preventDefault();

	if ($(this).hasClass('disabled'))
	{
		return false;
	}

	var clickedId = $(this).attr("data-id");
	var clickedXmlId = $(this).attr("data-xmlid");
	var clickedGroup = $(this).attr("data-group");

	getClothesList(clickedGroup, currentType);
});

// Клик по кнопке выбора ткани
$(document).on('click', '.js-choose_cloth', function(event) {
	event.preventDefault();

	var clickedTarget = $(this).attr('data-target');
	var clickedImageSrc = $('.js-pick-cloth_image').attr('src');
	var target = $('.js-cloth_target[data-target="' + clickedTarget + '"]');

	choosedClothes[clickedTarget] = $(".js-pick-cloth_name").attr("data-id");
	choosedGroups[clickedTarget] = $(".js-pick-cloth_name").attr("data-group");

	calculatePrice();

	$(target).find('.js-cloth_target-image').html('<img src="' + clickedImageSrc + '" />');
	$(target).find('.js-cloth_target-name').text($('.js-pick-cloth_name').text());
	$(target).find('.js-cloth_target-clear').attr('data-group', choosedClothes[clickedTarget]);
	$(target).find('.js-cloth_target-clear').attr('data-id', choosedGroups[clickedTarget]);
	$('#modal-cloth-pick').arcticmodal('close');
});

// клик на крестик у выбранной ткани в карточке
$(document).on('click', '.js-cloth_target-clear', function(event) {
	var targetToClear = $(this).parents('.js-cloth_target').attr('data-target');
	var groupToClear = $(this).attr('data-group');
	var idToClear = $(this).attr('data-id');
	var target = $('.js-cloth_target[data-target="' + targetToClear + '"]');

	choosedClothes[targetToClear] = defaultClothId;
	choosedGroups[targetToClear] = defaultGroup;

	$(target).find('.js-cloth_target-image').html('');
	$(target).find('.js-cloth_target-name').text('');
	$(target).find('.js-cloth_target-clear').attr('data-group', '');
	$(target).find('.js-cloth_target-clear').attr('data-id', '');

	calculatePrice();
});

// клик на картинку выбранной ткани в карточке товара
$(document).on('click', '.js-cloth_target-image', function(event) {
	event.preventDefault();
	// кликнет на кнопку "выбрать ткань" ну или как там ее
	$(".btn-modal-cloth").click();
});

$(document).on('click', '.js-submit_clothes', function(event) {
	event.preventDefault();
	$.arcticmodal('close');
});

// клик на иконку подсказки у механизма трансформации
$(document).on("click", ".js-mechanism-tooltip", function(event) {
	event.preventDefault();
	$(".js-mechanism-tooltip-text").slideToggle();
});

$(document).on('change', 'input[name="ARMRESTS_COLOR"]', function() {
	calculatePrice();
});

// Клик по "Как выбрать ткань"
$(document).on('click', '.js-cloth_instruction', function(event) {
	event.preventDefault();
	$('#modal-cloth_instructions').arcticmodal();
});

function getClothesList(group, type) {
	group = group || 1; // Группа ткани по умолчанию 1
	type = type || 0; // Тип ткани по умолчанию 0 = все типы

	$.ajax({
  		type: 	"POST",
  		url: 	"/catalog/clothes.php",
  		data: 	'AJAX=Y&' +
				'ACTION=GETLIST&' +
  				"GROUP=" + group + '&' +
			  	"TYPE=" + type,
  		success: function(response){
  			$('.js-cloth_group').removeClass('active');
			$('.js-cloth_group[data-id="' + group + '"]').addClass('active');
  		  	$('.js-clothes_list').html(response);
  		  	currentGroup = group;
  		  	getCollectionsList(group, type);
  		}
	});
}

function getCollectionsList(group, type) {
	group = group || 1; // Группа ткани по умолчанию 1
	type = type || 0; // Тип ткани по умолчанию 0 = все типы

	$.ajax({
  		type: 	"POST",
  		url: 	"/catalog/clothes.php",
  		data: 	'AJAX=Y&' +
				'ACTION=GETCOLLECTIONSLIST&' +
  				"GROUP=" + group + '&' +
			  	"TYPE=" + type,
  		success: function(response) {
  		  	$('.js-cloth_collection-container').html(response);
  		}
	});
}

// Фильтрация доступных групп при выбранном типе ткани
function getGroupsWithType(type) {
	type = type || 0; // Тип ткани по умолчанию 0 = все типы

	$.ajax({
  		type: 	"POST",
  		url: 	"/catalog/clothes.php",
  		data: 	'AJAX=Y&' +
				'ACTION=GETGROUPS&' +
			  	"TYPE=" + type,
  		success: function(response){
  			var responseObj = JSON.parse(response);
  			var clothGroupsBlocks = $(".js-cloth_group");

  			// console.log(responseObj);

  			$.each(clothGroupsBlocks, function(i,e) {
  				$(e).removeClass('disabled');
  				// console.log("Disabled groups:", $(e).attr('data-id'));

  				if (!responseObj.includes($(e).attr('data-id')))
  				{
  					$(e).addClass('disabled');
  				}
  			});

  			currentType = type;
  		  	getClothesList(currentGroup, currentType);
  		}
	});
}

// считает и выводит конечную стоимость
function calculatePrice() {
	var arrGroups = Object.keys( choosedGroups ).map(function ( key ) { return choosedGroups[key]; });
	var maxGroup = Math.max.apply( null, arrGroups );

	// console.log("choosedSize: ", choosedSize);
	// console.log("priceTable: ", priceTable);
	var clothMarkup = parseInt(priceTable[choosedSize].groups[maxGroup]);

	// если выбрана третья или четвертая ткань, то добавить их наценку (toggle опций)
	if (choosedClothes['third'] != defaultClothId) {
		$('.js-options-checkbox[data-xmlid="third_cloth"]').prop('checked', true);
	} else {
		$('.js-options-checkbox[data-xmlid="third_cloth"]').prop('checked', false);
	}

	if (choosedClothes['fourth'] != defaultClothId) {
		$('.js-options-checkbox[data-xmlid="fourth_cloth"]').prop('checked', true);
	} else {
		$('.js-options-checkbox[data-xmlid="fourth_cloth"]').prop('checked', false);
	}

	var optionsPrice = getOptionsPrice();

	var totalMarkup = parseInt(clothMarkup + optionsPrice);

	// console.log("maxGroup: ", maxGroup);
	// console.log("clothMarkup: ", clothMarkup);
	// console.log("totalMarkup: ", totalMarkup);

	dataPrice = parseInt(basePriceValue + totalMarkup);

	printPrice(parseInt(basePriceValue + totalMarkup));
}

// выводит текущую "насчитанную" цену
function printPrice(price) {
	$('.js-final_price').text(price);

	if (currentGroup != 1) {
		$('.product-total__clothprice').hide();
	} else {
		$('.product-total__clothprice').show();
	}
	//$('.js-price_block-cloth_group').text(currentGroup);
	$('.js-cloth-choosed_price').text(price);
}

// считает и возвращает сумму выбранных опций
function getOptionsPrice() {
	var result = 0;

	var options = $('.js-options-checkbox:checked');

	$.each(options, function(index, element) {
		result += parseInt($(element).attr('data-price'));
	});

	if ($('.js-armrest-color').length > 0) {
		result += getArmrestsColorPrice();
	}

	return result;
}

function getArmrestsColorPrice() {
	var result = 0;
	result = parseInt($('.js-armrest-color .select-options__value.active').attr('data-price'));
	return result;
}

function add2wish(p_id, pp_id, p, name, dpu, th){
    $.ajax({
        type: "POST",
        url: "/ajax/wishlist.php",
        data: "p_id=" + p_id + "&pp_id=" + pp_id + "&p=" + p + "&name=" + name + "&dpu=" + dpu,
        success: function(html){
            $(th).addClass('active');
            $('.header-favorite span').html(html);
        }
    });
};