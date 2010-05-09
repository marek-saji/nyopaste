<?php

$v->setTitle($t->trans('delete account'));
$form = g('Forms', array('delete', $this));
$form->create($t->url2c('User', 'delete', array($login)));
echo $this->trans('Delete user <em>%s</em>?', $login);
$form->input('id');
?>
    <fieldset class="user">
        <ul class="fields">
            <li>
                <label class="">
                    <span class="field_label"><?= $this->trans('delete with objects'); ?></span>
                    <?php $form->input('with_objects'); ?>
                </label>
            </li>
            <li>
                <label class="">
                    <span class="field_label"><?= $this->trans('delete with comments'); ?></span>
                    <?php $form->input('with_comments'); ?>
                </label>
            </li>
        </ul>
    </fieldset>
<?php
$this->inc('Forms/buttons', array('submit' => $this->trans('delete')));
$form->end();
