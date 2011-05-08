$(function(){

    $('ul.radio-optiongroups').each(function(){
        var $list = $(this),
            $select = $('<select />', {'class': 'hg'});

        $list
            .children('li.field').children('label').find('input[type="radio"]')
                .each(function(){
                    var $radio = $(this),
                        $label = $radio.closest('label');
                    $select
                        .append(
                            $('<option />', {
                                'text': $label.text(),
                                'value': $radio.val()
                            })
                            .data({
                                'radio': $radio,
                                'suboptions': $radio.closest('li.field')
                            })
                        );
                    if ($radio.is(':checked'))
                    {
                        $select.val($radio.val());
                    }
                    $label.hide();
                });

        console.info('radio-optiongroups with ', $select);
        $select
            .appendTo($list.closest('fieldset').find('legend:first').html(''))
            .change(function(){
                $(this).find(':selected').data('suboptions')
                    .siblings(':visible')
                        .slideUp()
                    .end()
                    .slideDown()
                    .find('input[type="radio"]:first')
                        .click();
            })
            .find(':selected').data('suboptions')
                .siblings()
                    .hide()
                .end()
                .show();
    });


    $('ul.less-important-options')
        .each(function(){
            var $list = $(this),
                visible = $list.is(':visible'),
                data = $list.data('less-important-options'),
                $toggler = $('<a />', {
                    'class': 'less-important-options-toggler '+(visible?'collapser':'expander'),
                    'html': visible ? data.collapse : data.expand
                });

            $toggler
                .bind('click.less-important-options', function(e){
                    e.preventDefault();
                    var way = $list.is(':visible');

                    $list
                        .slideToggle(way, function(){
                            $toggler
                                .html(data[way ? 'expand' : 'collapse'])
                                .toggleClass('expander', way)
                                .toggleClass('collapser', !way)
                            ;
                        })
                        .data('less-important-options')
                            .$exceprt
                                .slideToggle(!way)
                    ;
                })
                .insertBefore($list)
            ;

            if (!$list.is('.with-excerpt'))
            {
                data.$exceprt = $();
            }
            else
            {
                data.$exceprt = 
                    $('<p />', {
                        'class' : 'less-important-options-excerpt'
                    })
                    .insertBefore($toggler)
                ;
            }
        })
        .filter('.with-excerpt')
            .find(':input')
                .bind('change.less-important-options', function(){
                    var $list = $(this).closest('.less-important-options'),
                        data = $list.data('less-important-options')
                    ;
                    switch (true)
                    {
                        case $list.is('.privacy') :
                            var privacy = $list.find(':input[name="Paste_paste[privacy]"]:checked').val();
                            $list
                                .siblings('.less-important-options-excerpt')
                                    .html(data.excerpts[privacy])
                            ;
                            break;
                    }
                })
                .trigger('change.less-important-options')
    ;


    /**
     * ACE companion
     * @url http://ajaxorg.github.com/ace/build/textarea/editor.html
     */
    function load(path, module, callback)
    {
        path = hg.include_path + "ace/build/textarea/src/" + path;
        if (!load.scripts[path])
        {
            load.scripts[path] = {
                loaded: false,
                callbacks: [callback]
            };
            $('<script />', {'src': path}).appendTo('head');
            var loader = window.setInterval(function(){
                if (window.__ace_shadowed__ && window.__ace_shadowed__.define.modules[module])
                {
                    window.clearInterval(loader);
                    load.scripts[path].loaded = true;
                    load.scripts[path].callbacks.forEach(function (callback) {
                        callback();
                    });
                }
            }, 50);
        }
        else if (load.scripts[path].loaded)
        {
            callback();
        }
        else
        {
            load.scripts[path].callbacks.push(callback);
        }
    }
    load.scripts = {};
    window.__ace_shadowed_load__ = load;

    var ace = window.__ace_shadowed__,
        $textarea = $('.editor');
    $('.editor').each(function(){
        console.info('init ACE on ', this);
        $(this).data('ace_editor', ace.transformTextarea(this));
    });

});

