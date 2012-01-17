/**
 * HoloGram live events
 * ====================
 */

 hg.init = {};

/**
 * Help values (displayed when form input is empty)
 * ------------------------------------------------
 * @author m.augustynowicz
 *
 * #### USAGE
 *
 *     <textarea class="helpval" title="Describe it here"></textarea>
 */
$(function(){
    try
    {
        hg('helpval')(true);
    }
    catch (e)
    {
        console.error('failed to initialize helpval:', e);
    }
});



/**
 * nyroModal
 * ---------
 * @author m.augustynowicz
 *
 * #### USAGE
 *
 *     <a href="hg link" class="modal">foo</a>
 *
 * Will open modal window, but also execute javascript, add stuff
 * to head, change page title etc.
 *
 * Things added to `<head />` stay there after closing modal window
 * (except value of title)
 */
$(function(){
    try
    {
        if (hg('nyroModalFit')())
        {
            if ($.nyroModalManual)
            {
                hg("nyroModalInit")();
                $('body').addClass('modal-enabled');
            }
            else
            {
                throw("nyroModalManual not found");
            }
        }
        else
        {
            console.log('Screen smaller than 500x500, not enabling modal windows');
        }
    }
    catch (e)
    {
        console.error('failed to initialize hg-specific nyroModal flavour:', e);
    }
});



/**
 * jQuery UI datepicker
 * --------------------
 * @author m.augustynowicz
 *
 * #### USAGE
 *
 *     <input type="text" class="date" />
 *
 * customization:
 *
 *     hg_datepickers['.foo'] = {
 *       dateFormat : 'dd-mm-yy',
 *       cleanLabel : 'remove',
 *       showClean : true,
 *       onClean : function(){alert("I'm clean!")},
 *       cleanLabel : 'remove'
 *     };
 *
 * these settings beats `default_hg_opts`,
 * `default_hg_opts` beats `$.datepicker.regional[lang]`
 */
$(function(){
    try
    {
        if (typeof $.datepicker == 'undefined')
        {
            return;
        }
        var default_hg_opts = {
            dateFormat: 'yy-mm-dd',
            showOn: 'button',
            showClean: false,
            cleanLabel: 'clean',
            onClean: function(){}
        };
        var lang = $('html').attr('lang');
        var default_opts = $.extend(
            {},
            $.datepicker.regional[lang],
            default_hg_opts
        );

        $('input.date').each(function(){
            var me = $(this);
            var opts = $.extend({}, default_opts); // clone
            if (typeof hg_datepickers != 'undefined')
            {
                $.each(hg_datepickers, function(selector) {
                    if (me.is(selector))
                    {
                        $.extend(opts, this);
                    }
                });
            }

            me.datepicker(opts);

            if (opts.showClean)
            {
                me.after('<a class="input-date-cleaner" title="'+opts.cleanLabel+'" href="javascript:void(0)">'+opts.cleanLabel+'</a>');
                me.next().click(function(){
                    opts.onClean.apply(this);
                    var input = $(this).prev();
                    input.val('');
                });
            }
        }); // each input.date
    }
    catch (e)
    {
        console.error('failed to initialize datepickers:', e);
    }
}); // onload, datepicker



/**
 * Tabbed interface
 * ----------------
 * @author m.augustynowicz
 *
 * Not tested on nested tabs
 *
 * #### USAGE
 *
 *     <div class="tabbed">
 *       <ol class="tabs">
 *         <li><a href="#foo">foo</a>
 *       </ol>
 *       <div class="contents">
 *          <div class="tab" id="foo">
 *              FOO!
 *          </div>
 *       </div>
 *     </div>
 *
 * selected `li` tab gets class active
 *
 * (using `visibility:hidden`, because `display:none` breaks xinha)
 */
(function(){
    try
    {
        var hidden_tab_css  = {visibility:'hidden',height:0,overflow:'hidden'};
        var visible_tab_css = {visibility:'visible',height:'auto',overflow:'visible'};

        function toggleTab()
        {
            var me = $(this);
            var sel = me.attr('href');
            var tabs = me.closest('.tabs');
            var contents = tabs.closest('.tabbed').find('.contents:first');

            contents.children('.tab:visible').css(hidden_tab_css).removeClass('active');
            tabs.children('.active').removeClass('active');

            contents.children(sel).css(visible_tab_css).addClass('active');
            me.closest('li').addClass('active');

            return false;
        }

        $('.tabbed .tabs > li a').live('click', toggleTab);

        $(function(){
            $('.tabbed').each(function(){
                var context = $(this);
                var tabs = context.find('.tabs:first');
                if (!tabs.children().is('.active'))
                {
                    tabs.children(':first').addClass('active');
                }
                tabs.children('.active').each(function(){
                    // ok. I don't know why, but calling toggleTab here
                    // did not work /:
                    var sel = $(this).find('a:first').attr('href');
                    var contents = $(this).closest('.tabbed').find('.contents:first');
                    contents.children('.tab:visible').css(hidden_tab_css).removeClass('active');
                    contents.children(sel).css(visible_tab_css).addClass('active');
                });
            });
        }); // onload
    }
    catch (e)
    {
        console.error('failed to initialize tabbed interface:', e);
    }
})(); // scope, tabbed interface



/**
 * Cloneable fields
 * ----------------
 * @author m.augustynowicz
 *
 * #### USAGE
 *
 *     <ol class="clonable" hg__clone_label="+">
 *         <li>clone me!</li>
 *         <li>some row</li>
 *         <!-- cloned row will be placed here -->
 *     </ol>
 */
$(function(){
    try
    {
        $('ol.cloneable[hg__clone_label], ul.cloneable[hg__clone_label]').each(function(){
            var me = $(this);
            var label = me.attr('hg__clone_label') || '+';
            var cloner = $('<a href="#" class="cloner btn grey '+me.attr('class')+'">'+label+'</a>');
            cloner.click(function(){
                if(!cloner.hasClass('disabled'))
                {
                    var context = $(this).prev();
                    /**
                     * ### changed 2010-03-27 by b.matuszewski
                     *
                     * - removing `value` from all inputs in `<li></li>`
                     * - seting `name` so that new field will be accessible in controller
                     *   throuh `$this->data['form_name']['field_name']['cloned'][some int id]`
                     * - giving "unique" id for a new field
                     *
                     * old version
                     *
                     *     context.children(':first').clone().appendTo(context);
                     */

                    // I take `:last` insted of `:first` couse im enhencing "id" with ending '_'
                    // and want to provide unique one
                    var tmpObj = context.children(':last').clone();
                    tmpObj.find('ul.data_record :input').each(function()
                    {
                        $(this).removeAttr("value");
                        $(this).attr("name",$(this).attr("name").replace(/\[[0-9]+\]/,"[cloned][]"));
                        $(this).attr("id",$(this).attr("id")+"_");
                    });
                    tmpObj.appendTo(context);
                    /**
                     * changes end here
                     */
                }
                return false;
            }).insertAfter(me);
            me.removeAttr('hg__clone_label'); // to make sure not to create button again
        });
    }
    catch (e)
    {
        console.error('failed to initialize cloneable fields:', e);
    }
}); // onload, clonable fields



/**
 * Collapsable
 * -----------
 * @author m.augustynowicz
 *
 * #### USAGE
 *
 *     <section id="foo" class="collapsable"
 *              hg_collapsable_expand_label="więcej"
 *              hg_collapsable_collapse_label="mniej"
 *              hg_collapsable_expand_title="kliknij, aby zobaczyć całość"
 *              hg_collapsable_collapse_title="kliknij, aby ukryć"
 *              hg_collapsable_height="3"
 *     >
 *         Lorem ipsum..
 *     </section>
 *
 * becomes:
 *
 *     <div class="collapsable_wrapper">
 *         <section id="foo" class="collapsable"
 *                  style="height: {rzy linijki}"
 *         >
 *             Lorem ipsum..
 *         </section>
 *         <a>more</a>
 *     </div>
 */
$(function(){
    try
    {
        $('.collapsable').not('.collapsable_wrapper > .collapsable').each(function(){
            var me = $(this);
            var hash = '#' + me.closest('[id]').attr('id');
            var expand_label = me.attr('hg_collapsable_expand_label') || '[+]';
            var expand_title = me.attr('hg_collapsable_expand_title') || '';
            var collapse_label = me.attr('hg_collapsable_collapse_label') || '[-]';
            var collapse_title = me.attr('hg_collapsable_collapse_title') || '';
            var height = parseFloat(me.attr('hg_collapsable_height')) || 2;

            var tmp = $('<div>x</div>').css({
                'line-height':me.css('line-height')
            }).appendTo(me);
            var line_height = tmp.height();
            tmp.remove();
            height = ~~(height*line_height);
            me.data({
                'hg__line_height' : line_height,
                'hg__collapsed_height' : height,
                'hg__collapsed_expand_label' : expand_label,
                'hg__collapsed_expand_title' : expand_title,
                'hg__collapsed_collapse_label' : collapse_label,
                'hg__collapsed_collapse_title' : collapse_title
            });
            me.removeAttr('hg_collapsable_expand_label')
              .removeAttr('hg_collapsable_expand_title')
              .removeAttr('hg_collapsable_collapse_label')
              .removeAttr('hg_collapsable_collapse_title')
              .removeAttr('hg_collapsable_height')
              .removeAttr('hg_collapsable_height');


            if (me.height() <= height)
            {
                return; // we happy with the things they are already.
            }

            me.css({
                'overflow' : 'hidden',
                'height'   : height + 'px'
            });

            var wrapper = me.wrap($('<div />', {
                'class' : 'collapsable_wrapper'
            })).parent();

            var expander = $('<a />', {
                'class' : 'collapsable_expander',
                'html'  : '<span class="collapsable_label">'+expand_label+'</span>',
                'title' : expand_title,
                'href'  : hash,
                'click' : function(e) {
                    e.preventDefault();
                    var expander = $(this);
                    var label = expander.children('.collapsable_label');
                    var content = expander.siblings('.collapsable');
                    if (expander.is('.expanded'))
                    {
                        content.css('height', content.data('hg__collapsed_height')+'px');
                        expander.children('.collapsable_fader').show();
                        expander.css('margin-top', -content.data('hg__line_height')+'px');
                        expander.attr('title', content.data('hg__collapsed_expand_title'));
                        label.text(content.data('hg__collapsed_expand_label'));
                        expander.removeClass('expanded');
                    }
                    else
                    {
                        content.css('height', 'auto');
                        expander.children('.collapsable_fader').hide();
                        expander.css('margin-top', '0');
                        expander.attr('title', content.data('hg__collapsed_collapse_title'));
                        label.text(content.data('hg__collapsed_collapse_label'));
                        expander.addClass('expanded');
                    }
                    this.blur();
                    return false;
                },
                'css' : {
                    'margin-top'  : -line_height+'px',
                    'display'     : 'block'
                }
            });
            var fader = $('<span />', {
                'class' : 'collapsable_fader',
                'css'   : {
                    'display' : 'block',
                    'height'  : line_height+'px'
                }
            });
            fader.prependTo(expander);

            expander.appendTo(wrapper);

            if (hash == window.location.hash)
            {
                expander.click();
            }
        });
    }
    catch(e)
    {
        console.error('failed to initialize collapsable fields', e);
    }
});



/**
 * Autoexpandable textareas
 * ------------------------
 * @author m.augustynowicz
 *
 * #### USAGE
 *
 *     <textarea class="autoexpandable"></textarea>
 *
 * will use current size as minimum
 */
$(function(){
    try
    {
        $('textarea.autoexpandable').live('keydown', function(e){
            var me = $(this);
            if (me.data('hg.autoexpandable.timeout'))
            {
                return;
            }
            me.data('hg.autoexpandable.timeout', window.setTimeout(function(){
                me.removeData('hg.autoexpandable.timeout');
                if (!me.data('hg.autoexpandable.min-height'))
                {
                    me.data('hg.autoexpandable.min-height', me.height());
                    var foo = $('<div />', {
                        'text' : 'Mj',
                        'css' : {
                            'white-space' : 'pre',
                            'font-family' : me.css('font-family'),
                            'font-size'   : me.css('font-size'),
                            'line-height' : me.css('line-height')
                        }
                    });
                    foo.insertAfter(me);
                    me.data('hg.autoexpandable.padding-bottom', foo.height()+1);
                    foo.remove();
                }
                var padding = me.data('hg.autoexpandable.padding-bottom');
                me.height(0);
                me.height(me[0].scrollHeight + padding*2);
            }, 200));
        }).keydown();
    }
    catch(e)
    {
        console.error('failed to initialize autoexpandable textareas', e);
    }
});


/**
 * Emulate `:focus`
 * ----------------
 * @author m.augustynowicz
 */
$(':input')
    .live('focusin focusout', function(e){
        var focusin = 'focusin'==e.type;

        if (focusin)
        {
            $('.recently-focused').removeClass('recently-focused');
        }

        $(this)
            .closest('.field')
                .andSelf()
                    .addClass('recently-focused')
                    .toggleClass('focus', focusin)
        ;
    })
;


/**
 * ### Move "recently focus" to form field of a clicked message
 * @author m.augustynowicz
 */
$('.holoform .field_error')
    .live('click', function(e){
        $('.field.recently-focused')
            .removeClass('recently-focused');

        $(this)
            .closest('.field')
                .addClass('recently-focused');
    })
;



/**
 * Inputs just to be copied to a clipboard
 * ---------------------------------------
 * @author m.augustynowicz
 */
hg.init.copyToClipboard = function () {
    if (typeof ZeroClipboard != 'object')
    {
        return false;
    }

    for (var i in ZeroClipboard.clients)
    {
        if (ZeroClipboard.clients.hasOwnProperty(i))
        {
            ZeroClipboard.clients[i].reposition();
        }
    }

    $('embed[id^=ZeroClipboardMovie_]').parent().remove();
    $('.copier').remove();

    $(':input.copyable[id][readonly]')
        .each(function(){
            var $input   = $(this),
                copierId = $input.attr('id') + '-copier',
                clip     = new ZeroClipboard.Client(),
                options  = {
                    'text'      : 'copy',
                    'title'     : 'click to copy to system clipboard',
                    'afterText' : 'copied!'
                }
            ;

            $.extend(options, $input.data('copyable'));

            var $copier = $('<span />', {
                    'id'    : copierId,
                    'text'  : options.text,
                    'class' : 'copier',
                    'data'  : {'source' : $input}
                })
                .insertAfter($input)
            ;

            clip.setHandCursor(true);
            clip.addEventListener('onMouseDown', function(client){
                clip.setText($($(client.domElement).data('source')).val());
            });
            clip.addEventListener('onComplete', function(client, text){
                $(client.domElement).text(options.afterText);
                client.reposition();
            });
            clip.glue(copierId);

            if (options.title)
            {
                $copier
                    .add($('#ZeroClipboardMovie_' + clip.id))
                        .attr('title', options.title)
                ;
            }
        })
    ;

    return true;
};

hg.init.copyToClipboard();



/**
 * Smoothly scroll to anchor links
 * -------------------------------
 * @author m.augustynowicz
 */
$('a[href^="#"]')
    .live('click.smoothAnchorLink', function(e){
        var hash = $(this).attr('href'),
            $target = $(hash).eq(0)
        ;
        if ($target.length)
        {
            $('html, body') // some browsers like html, some body
                .animate(
                    {
                        scrollTop : $target.offset().top
                    },
                    'swing',
                    function(){
                        window.location.hash = hash;
                    }
                )
            ;
            e.preventDefault();
        }
    })
;



/**
 * Scroll to first error
 * ---------------------
 *  @author m.augustynowicz
 */
$(function () {
    var $error = $('.error, .field_error:not(:empty)').eq(0);
    if ($error.length !== 0) {
        var err_top = $error.offset().top,
            html = $('html')[0]
        ;
        if (html.scrollTop > err_top || err_top - html.scrollTop > window.innerHeight) {
            html.scrollTop = err_top;
        }
    }
});


/**
 * Autocomplete fields
 * -------------------
 * @author m.augustynowicz
 */
$(window).load(function () {
    $('.autocomplete').each(function () {
        var $this       = $(this),
            $dataSource = $this.data('impersonating') || $this,
            data        = $dataSource.data('hgAutocomplete') || {}
        ;

        if (!data.url) {
            return;
        }

        if ($this.is(':not(:input)')) {
            $this = $this.find(':input');
        }

        if ($this.length === 0) {
            return;
        }
        else if ($this.length > 1) {
            $this = $this.eq(0);
        }

        var cache = {'': []};

        $this.autocomplete({
            source : function (current, callback) {
                var input = current.term || '';

                if (typeof cache[input] !== 'undefined') {
                    callback(cache[input]);
                    return;
                }

                $.ajax({
                    type    : 'POST',
                    data    : { input : input },
                    url     : data.url,
                    success : function (json) {
                        cache[input] = json.suggestions;
                        callback(cache[input]);
                    }
                });
            }
        });
    });
});


/**
 * Focus clicked elements
 * ----------------------
 * Chrome tries to fool us, but we are smarter than this.
 */
$(':input').live('click', function () {
    $(this).filter(':not(:focus)').focus();
});

