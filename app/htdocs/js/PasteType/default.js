$(function(){
    $('pre.line_no').each(function(){
        var $pre  = $(this),
            $code = $pre.children('code'),
            $numbers = $('<div />', {
                'class': 'numbers'
            }).insertBefore($code.eq(0));

        $code.each(function(){
            var id = $(this).attr('id'),
                no = id.replace(/[^0-9]/g, '');
            $('<a />', {
                'href': '#' + id,
                'text': no
            }).appendTo($numbers);
            $('<br />').appendTo($numbers);
        });
    });
});

