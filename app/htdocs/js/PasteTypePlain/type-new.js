$(function () {
    // check [do_wrap_at], when chaning [wrap_at]
    $(':input[name$="[wrap_at]"]').bind('change click', function () {
        $(this).closest('.field')
            .find(':input[name$="[do_wrap_at]"]')
                .prop('checked', true)
        ;
    });
});
