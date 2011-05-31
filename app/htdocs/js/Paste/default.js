/**
 * Paste page
 * ==========
 */

/**
 * the "more info" toggler
 * -----------------------
 */
$(function(){

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


/**
 * periodically check for new versions
 * -----------------------------------
 */
var newVerCheckerTimeout;
function newVerChecker(url, timeout, msg)
{
    window.clearTimeout(newVerCheckerTimeout);

    var failsAllowed = 3,
        failsLeft    = failsAllowed,
        check = function() {

            hg('ajax')({
                url     : url,
                success : function(data, textStatus, XHR) {
                    if (data.count <= 0)
                    {
                        newVerCheckerTimeout = window.setTimeout(check, timeout);
                    }
                    else
                    {
                        $('<div />', {
                            'class' : 'ajaj-msg',
                            html : msg,
                            css  : {
                                display : 'none'
                            }
                        })
                        .appendTo($('body'))
                        .slideDown();
                    }
                },
                error   : function(XHR, textStatus, errorThrown) {
                    var msg = "Checking for new version failed";
                    errorThrown && (msg += " ("+errorThrown+")");
                    if (--failsLeft > 0)
                    {
                        console.error(msg + ", will try "+failsLeft+" more times.");
                        newVerCheckerTimeout = window.setTimeout(check, timeout);
                    }
                    else
                    {
                        console.error(msg + " for the last time(s).");
                    }
                }
            });
        }
    ;

    newVerCheckerTimeout = window.setTimeout(check, timeout);
}

