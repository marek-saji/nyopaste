<?php
/**
 * Standard buttons visible in (almost?) every form.
 *
 * Button labels are translated using $this->trans().
 * @author m.augustynowicz
 *
 * (parameters passed as assigned local variables)
 * @param string $submit text on submit button, defaults to "submit"
 * @param string $submit_class HTML classes for submit button, default blank
 * @param string $cancel in format accepted by Forms/backlink template ($cancel_label)
 * @param array $cancel_link in format accepted by Forms/backlink
 * @param object|string|bool $form form to use to obtain backlink
 *        false -- don't use any. use referer
 *        object|string form object eighter it's short ident
 */
extract(array_merge(
    array(
        'submit'  => 'Save',
        'submit_class'  => '',
        'cancel'  => 'Cancel',
        'cancel_link' => true,
        'form'    => null, // null value will raise error
    ),
    (array) $____local_variables
));

if (null === $form)
    trigger_error('$form parameter passed to Forms/buttons is null', E_USER_WARNING);

?>
<fieldset class="buttons">
    <input type="submit" value="<?=$t->trans($submit)?>" class="<?= $submit_class; ?>" />
    <?php
    if(!empty($cancel) && $cancel_link)
    {
        if (null === $form )
            trigger_error('$form parameter passed to Forms/buttons is null', E_USER_WARNING);

        if($form && true === $cancel_link)
        {
            if(is_object($form))
                $form = $form->getShortIdent();
            $cancel_link = @$this->data[$form]['_backlink'];
            if(!$cancel_link)
                $cancel_link = true;
        }
        $t->inc('Forms/backlink', array(
                'cancel_link'  => $cancel_link,
                'cancel_label' => $cancel,
            ));
    }
    ?>
</fieldset>

