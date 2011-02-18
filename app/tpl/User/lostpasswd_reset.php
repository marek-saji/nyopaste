<?php
g()->view->setTitle($t->trans('Change password'));
?>

<?php
$form = g('Forms', array('lostpasswd_reset', $this));
$form->create();
?>
    <fieldset id="content">
        <ul class="fields">
            <li>
                <!--<label class="password">-->
                    <span class="field_label"><?=$this->trans('new password')?></span>
                    <?php $form->input('passwd'); ?>
                <!--</label>-->
            </li>
        </ul>
    </fieldset>
<?php
$this->inc('Forms/buttons', array('submit' => $this->trans('change')));
$form->end();
