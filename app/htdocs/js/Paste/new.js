$(function(){
    $('.holoform .field_error').live('click', function(e){
        $('.field.recently-focused')
            .removeClass('recently-focused');

        $(this)
            .closest('.field')
                .addClass('recently-focused');
    });


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


    $('ul.less-important-options').each(function(){
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
                if ($list.is(':visible'))
                {
                    $list.slideUp(function(){
                        $toggler
                            .html(data.expand)
                            .addClass('expander')
                            .removeClass('collapser');
                    });
                }
                else
                {
                    $list.slideDown(function(){
                        $toggler
                            .html(data.collapse)
                            .removeClass('expander')
                            .addClass('collapser');
                    });
                }
            })
            .insertBefore($list);
    });
});


