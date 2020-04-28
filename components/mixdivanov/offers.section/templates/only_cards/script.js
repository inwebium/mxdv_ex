var isLoading = false;
var pageN = 2;

var filterApplied = false;
var filterPriceMin = 0;
var filterPriceMax = 100000;
var filtration = "";

/*$(window).scroll(function() {
    if (isScrolledIntoView($(".js-ajax-more")) && !isLoading && pageN <= totalPages) {
        isLoading = true;
        $.ajax({
            type: "POST",
            data: "AJAX=Y&PAGEN_1=" + pageN + "" + filtration,
            success: function(response) {
                $('.js-catalog-section').append(response).imagesLoaded().then(function(){
                    pageN++;
                    isLoading = false;
                });
                
            }
        });
    }
});*/

$(document).ready(function() {
    // Price range slider
    if($('#price-range-slider').length > 0) {
        var minPrice = parseInt($("#price-range-slider").attr('data-min'));
        var maxPrice = parseInt($("#price-range-slider").attr('data-max'));
        var minValue = parseInt($("#price-range__min").val());
        filterPriceMin = minValue;
        var maxValue = parseInt($("#price-range__max").val());
        filterPriceMax = maxValue;

        $("#price-range-slider").slider({
            range: true,
            min: minPrice,
            max: maxPrice,
            values: [minValue, maxValue],
            step: 100,
            slide: function(event, ui) {
                $("#price-range__min").val(ui.values[0]);
                $("#price-range__max").val(ui.values[1]);
            }
        });

        //$("#price-range__min").val($("#price-range-slider").slider("values", 0));
        //$("#price-range__max").val($("#price-range-slider").slider("values", 1));

        /*$("#price-range__min").change(function() {
            $("#price-range-slider").slider("values", 0, $(this).val());
        });
        $("#price-range__max").change(function() {
            $("#price-range-slider").slider("values", 1, $(this).val());
        })*/
    }
});

$(document).on('click', '#js-catalog-filter-submit', function(event) {
    event.preventDefault();
    filterPriceMin = $("#price-range__min").val();
    filterPriceMax = $("#price-range__max").val();
    filterApplied = true;

    filtration = "&MIN_PRICE=" + filterPriceMin + "&MAX_PRICE=" + filterPriceMax;
    reloadOffers();
});
$(document).on('click', '#js-catalog-filter-cancel', function(event) {
    event.preventDefault();
    filtration = "";
    filterApplied = false;
    reloadOffers();
});

function reloadOffers()
{
    isLoading = true;
    $.ajax({
        type: "POST",
        data: "AJAX=Y&PAGEN_1=1" + filtration,
        success: function(response) {
            $('.js-catalog-section').html(response).imagesLoaded().then(function(){
                pageN++;
                isLoading = false;
            });
            
        }
    });
}


function isScrolledIntoView(elem)
{
    var docViewTop = $(window).scrollTop();
    var docViewBottom = docViewTop + $(window).height();

    var elemTop = $(elem).offset().top;
    var elemBottom = elemTop + $(elem).height();

    return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
}

$.fn.imagesLoaded = function () {

    // get all the images (excluding those with no src attribute)
    var $imgs = this.find('img[src!=""]');
    // if there's no images, just return an already resolved promise
    if (!$imgs.length) {return $.Deferred().resolve().promise();}

    // for each image, add a deferred object to the array which resolves when the image is loaded (or if loading fails)
    var dfds = [];  
    $imgs.each(function(){

        var dfd = $.Deferred();
        dfds.push(dfd);
        var img = new Image();
        img.onload = function(){dfd.resolve();}
        img.onerror = function(){dfd.resolve();}
        img.src = this.src;

    });

    // return a master promise object which will resolve when all the deferred objects have resolved
    // IE - when all the images are loaded
    return $.when.apply($,dfds);

}