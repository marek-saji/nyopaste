/**
 * New paste page
 * --------------
 */
$(function(){

    var ace_editor = $('.editor').data('ace_editor');

    if (!ace_editor)
    {
        return;
    }

    /**
     * ### Switching source syntax in ACE
     */
    $('.type-specific.source :input[name$="[syntax]"]')
        .bind('change.ace', function(){
            ace_editor.setOption('mode', $(this).val());
        })
        .trigger('change.ace')
    ;


    /**
     * ### Switching colour scheme in ACE
     */
    $('.type-specific.source :input[name$="[colour_scheme]"]')
        .bind('change.ace', function(){
            ace_editor.setOption(
                'theme',
                $(this).val()
                    .replace(/ace-/, '')
                    .replace(/-/g, '_')
            );
        })
        .trigger('change.ace')
    ;


    /**
     * ### Toggling line numbers in ACE
     */
    $('.type-specific.source :input[name$="[line_numbers]"]')
        .bind('change.ace', function(){
            ace_editor.setOption('gutter', $(this).is(':checked') ? 'true' : 'false');
        })
        .trigger('change.ace')
    ;

});

