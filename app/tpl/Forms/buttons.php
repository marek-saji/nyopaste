<?php
/**
 * Standard buttons visible in (almost?) every form.
 *
 * Button labels are translated using $this->trans().
 * @author m.augustynowicz
 *
 * (parameters passed as assigned local variables)
 * @param object|string|bool $form form to use to obtain backlink
 *        false -- don't use any. use referer
 *        object|string form object eighter it's short ident
 * @param array $buttons array with buttons and links:
 *        - strings will be used as HTML,
 *        - arrays with [href] will be used as <a />'s attributes and
 *          [label] as tag's content
 *        - any other array will be used as <input />'s attributes
 *          (with [type] defaulting to "submit")
 *          [name] will be wrapped with form's ident (e.g. "foo" -> "Ctrl_form[foo]")
 * @param string $submit text on submit button, defaults to "submit"
 * @param string $submit_class HTML classes for submit button, default blank
 * @param string $cancel in format accepted by Forms/backlink template ($cancel_label)
 * @param array $cancel_link in format accepted by Forms/backlink
 */
extract(array_merge(
    array(
        'form'            => null, // null value will raise error
        'buttons'         => array(),
        // "submit" a.k.a. first button
        'submit'          => 'save',
        'submit_class'    => '',
        // separator between buttons
        'separator'       => 'or',
        'separator_class' => '',
        // "cancel" a.k.a. last button (link)
        'cancel'          => 'cancel',
        'cancel_link'     => true,
    ),
    (array) $____local_variables
));

if (null === $form)
{
    trigger_error('$form parameter passed to Forms/buttons is null', E_USER_WARNING);
}

// add $submit and $cancel to $buttons

if ($submit)
{
    array_unshift(
        $buttons,
        array(
            'type'  => 'submit',
            'value' => $this->trans($submit),
            'class' => $submit_class
        )
    );
}

if (!empty($cancel) && $cancel_link)
{
    if (true === $cancel_link)
    {
        if (is_object($form))
        {
            $form = $form->getShortIdent();
        }
        $cancel_link = @$this->data[$form]['_backlink'] or $cancel_link = true;
    }
    ob_start();
    $this->inc('Forms/backlink', array(
        'cancel_link'  => $cancel_link,
        'cancel_label' => $cancel
    ));
    $buttons[] = ob_get_clean();
}

if (empty($buttons))
{
    return;
}

$buttons_html_arr = array();
foreach ($buttons as $button)
{
    if (is_string($button))
    {
        $buttons_html_arr[] = $button;
    }
    elseif (array_key_exists('href', $button))
    {
        $label = & $button['label'];
        unset($label);
        $buttons_html_arr[] = $f->tag(
            'a',
            $button,
            $label
        );
    }
    else
    {
        $label =& $button['value'];
        unset($button['value']);

        if (@$button['name'])
        {
            $long_ident = $this->getFormsIdent($form);
            $button['name'] = sprintf('%s[%s]', $long_ident, $button['name']);
        }
        $buttons_html_arr[] = $f->tag(
            'button',
            $button,
            $label
        );
    }
}

$separator_html = $f->tag(
    'span',
    array(
        'class' => "separator {$separator_class}"
    ),
    $separator
);

?>
<fieldset class="buttons">
    <?php
    echo join(" {$separator_html} ", $buttons_html_arr);
    ?>
</fieldset>

