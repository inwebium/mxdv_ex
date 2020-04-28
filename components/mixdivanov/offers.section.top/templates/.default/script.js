/**
 * По клику на .js-more-links показать скрытые ссылке в блоке топа разделов
 */
$(document).on('click', '.js-more-links', function(event) {
	event.preventDefault();
	var sectionBlock = $(this).parent().parent().parent();

	if (true) {}
	$(sectionBlock).toggleClass('js-collapsed js-expanded');

	var linksItems = $(sectionBlock).find('.links-item.js-hidden');

	if ($(sectionBlock).hasClass('js-expanded')) {
		$(this).text('Скрыть');

		$(linksItems).each(function(index, item) {
			$(item).show();
		});
	} else {
		$(this).text('Показать еще');

		$(linksItems).each(function(index, item) {
			$(item).hide();
		});
	}
});