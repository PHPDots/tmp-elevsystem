
(function ($) {
    $.fn.rtable = function (options) {
        defaults = {style: 'notable', sacrifice: [], }
        options = $.extend(defaults, options);
        var elements = this;
        elements.each(function () {
            var table = $(this);
            var headers = null;
            var isOddRow = true;
            if (!table.find('> thead').length) {
                table.find('> tbody > tr:first-child, > tr:first-child').each(function () {
                    headers = $(this).find('> th');
                    if (headers.length) {
                        table.prepend('<thead></thead>');
                        $(this).appendTo(table.find('> thead'));
                        return false;
                    }
                });
                if (!headers.length) {
                    return;
                }
            }
            table.addClass('rtable ' + options.style);
            headers = table.find('> thead > tr > th');
            if (!table.find('> tbody').length) {
                table.find('> tr').wrapAll('<tbody></tbody>');
            }
            table.find('> tbody > tr').each(function () {
                $(this).addClass((isOddRow ? 'rtable-odd' : 'rtable-even'));
                if (options.style == 'notable') {
                    $(this).find('> td').each(function (idx) {
                        $(this).attr('data-title', headers.eq(idx).text());
                    });
                }
                isOddRow = isOddRow ? false : true;
            });
        });
        $(window).resize(function () {
            elements.each(function () {
                if (!$(this).hasClass('rtable')) {
                    return;
                }
                $(this).removeClass('active')
                $(this).css('max-width', 'inherit');
                for (i = 0; i < options.sacrifice.length; i++) {
                    $(this).find('> thead > tr, > tbody > tr').each(function () {
                        $(this).find('> th, > td').css('display', '');
                    });
                }
                ;
                for (i = 0; i < options.sacrifice.length; i++) {
                    if ($(this).width() > $(this).parent().width()) {
                        $(this).find('> thead > tr, > tbody > tr').each(function () {
                            $(this).find('> th, > td').eq(options.sacrifice[i]).hide();
                        });
                    } else {
                        break;
                    }
                }
                if ($(this).width() > $(this).parent().width()) {
                    for (i = 0; i < options.sacrifice.length; i++) {
                        $(this).find('> thead > tr, > tbody > tr').each(function () {
                            $(this).find('> th, > td').css('display', '');
                        });
                    }
                    ;
                    $(this).addClass('active');
                }
                $(this).css('max-width', '');
            });
        }).trigger('resize');
    };
}(jQuery));
$(document).ready(function () {
    $('#main table.notable').rtable({sacrifice: [1]});
    $('#main table.flipscroll').rtable({style: 'flipscroll', sacrifice: [1]});
});