$(function(){

    var ace_editor = $('.editor').data('ace_editor');

    if (!ace_editor)
    {
        return;
    }

    $('.type-specific.source :input[name$="[syntax]"]').change(function(){
        ace_editor.setOption('mode', $(this).val());
    });

    $('.type-specific.source :input[name$="[colour_scheme]"]').change(function(){
        ace_editor.setOption(
            'theme',
            $(this).val()
                .replace(/ace-/, '')
                .replace('-', '_')
        );
    });

    $('.type-specific.source :input[name$="[line_numbers]"]').change(function(){
        ace_editor.setOption('gutter', $(this).is(':checked') ? 'true' : 'false');
    });

});

