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
                    $label
                        .hide()
                        .insertBefore($list);
                });

        $select
            .appendTo($list.closest('fieldset').find('legend:first').html(''))
            .change(function(){
                $(this).find(':selected').data('suboptions')
                    .siblings(':visible')
                        .slideUp()
                    .end()
                    .slideDown();
            })
            .find(':selected').data('suboptions')
                .siblings()
                    .hide()
                .end()
                .show();
    });
});


