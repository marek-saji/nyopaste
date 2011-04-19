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
        'form'         => null, // null value will raise error
        'buttons'      => array(),
        // "submit" a.k.a. first button
        'submit'       => 'Save',
        'submit_class' => '',
        // "cancel" a.k.a. last button (link)
        'cancel'       => 'Cancel',
        'cancel_link'  => true,
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

?>
<fieldset class="buttons">
    <?php if ($buttons) : ?>
        <?php foreach ($buttons as $button) : ?>
            <?php if (is_string($button)) : ?>
                <?php
                echo $button;
                ?>
            <?php elseif (array_key_exists('href', $button)) : ?>
                <?php
                $label = & $button['label'];
                unset($label);
                echo $f->tag(
                    'a',
                    $button,
                    $label
                );
                ?>
            <?php else : ?>
                <?php
                $button += array(
                    'type' => 'submit'
                );
                if (@$button['name'])
                {
                    $long_ident = $this->getFormsIdent($form);
                    $button['name'] = sprintf('%s[%s]', $long_ident, $button['name']);
                }
                echo $f->tag(
                    'input',
                    $button
                );
                ?>
            <?php endif; /* array_key_exists('href', $button */ ?>
        <?php endforeach; ?>
    <?php endif; /* $buttons */ ?>
</fieldset>

