$(function(){
    $('.wrapper.line_no > .content').each(function(){
        var $pre     = $(this),
            data     = $pre.data('paste') || {},
            texts    = data.texts || {},
            lineLink = texts.lineLink || '%d',
            $code    = $pre.children('code'),
            $numbers = $('<div />', {
                'class': 'numbers'
            }).insertBefore($code.eq(0));

        $code.each(function(){
            var id = $(this).attr('id'),
                no = 1 + parseInt(id.replace(/[^0-9]/g, ''));
            $('<a />', {
                'href'  : '#' + id,
                'title' : lineLink.replace('%d', no),
                'text'  : no
            }).appendTo($numbers);
        });
    });
});

