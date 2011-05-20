/**
 * Paste page
 * ==========
 */

$(function(){

    /**
     * the "more info" toggler
     * -----------------------
     */
    var $hgroup  = $('#paste hgroup'),
        $meta    = $('#meta'),
        data     = $meta.data('toggler'),
        $toggler = $('<a />', {
                href    : '#meta',
                'class' : 'meta-toggler'
            })
    ;

    $toggler
        .append($('<span />', {
            'class' : 'text',
            text    : data.showLabel
        }))
        .append($('<span />', {'class' : 'icon'}))
        .click(function(e){
            e.preventDefault();
            e.stopPropagation();
            var way = !$meta.is(':visible');
            $meta.slideToggle(way);
            $toggler
                .toggleClass('collapser', way)
                .find('.text')
                    .text(data[(way?'hide':'show')+'Label'])
            ;
        })
        .appendTo($hgroup)
    ;

});

