<?php
g()->view->setTitle($t->trans('change account type'));
?>

<?php

if(isset($values))
{
    $form = g('Forms', array('changetype', $this));
    $form->create($t->url2c('User', 'changetype', array($login)));
?>
    <fieldset>
        <ul class="fields">
            <li>
                <span class="field_label"><?=$this->trans('account type')?></span>
                <?php $form->input('type', compact('values')); ?>
            </li>
        </ul>
    </fieldset>
<?php
    $this->inc('Forms/buttons', array('submit' => $this->trans('change')));
    $form->end();
}
