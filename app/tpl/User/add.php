<?php
$v->setTitle($t->trans('create account'));
$form = g('Forms', array('add', $this));
$form->create($admin ? $t->url2c('User', 'add', array('admin' => 1)) : null);
?>
    <fieldset>
        <ul class="fields">
            <li>
                <label class="text">
                    <span class="field_label"><?= $this->trans('login'); ?></span>
                    <?php $form->input('login'); ?>
                </label>
            </li>
            <li>
                <!--<label class="password">-->
                    <span class="field_label"><?= $this->trans('password'); ?></span>
                    <?php $form->input('passwd'); ?>
                <!--</label>-->
            </li>
            <li>
                <label class="text email">
                    <span class="field_label"><?= $this->trans('e-mail'); ?></span>
                    <?php $form->input('email'); ?>
                    <p class="help"><small><?= $this->trans('required for password recovery'); ?></small></p>
                </label>
            </li>
<?php
if($admin)
{
?>
            <li>
                <label class="text">
                    <span class="field_label"><?= $this->trans('account type'); ?></span>
                    <?php $form->input('type', compact('values')); ?>
                </label>
            </li>
<?php
}
?>
        </ul>
    </fieldset>
    <fieldset>
        <ul class="fields">
            <li class="captcha">
                <?php
                if($use_captcha)
                    echo recaptcha_get_html($publickey);
                ?>
            </li>
        </ul>
    </fieldset>
<?php
if(!$admin)
{
	$button_text = 'accept terms of use and add user';
    ?>
    <p>
        <?= $this->trans('Don\'t forget to read'); ?> <?= $this->l2c($this->trans('terms of use(genitive)'), 'info', '', array('tos')); ?>.
    </p>
<?php
}
else
	$button_text = 'add user';

$this->inc('Forms/buttons', array(
    'form' => & $form,
    'submit' => $this->trans($button_text),
));
$form->end();
