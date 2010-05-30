<?php
/**
 * User controller, action add
 *
 * @parm bool $viewer_is_admin
 * @param string $recaptcha_publickey
 * @param bool $use_captcha
 */
$v->setTitle($t->trans('Create an Account'));
$form = g('Forms', array('add', $t));
$form->create($viewer_is_admin ? $t->url2c($t->url(), $t->getLaunchedAction()) : null);
?>
    <fieldset>
        <ul class="fields">
            <li>
                <label class="text">
                    <span class="field_label"><?= $t->trans('login'); ?></span>
                    <?php $form->input('login'); ?>
                </label>
            </li>
            <li>
                <!--<label class="password">-->
                    <span class="field_label"><?= $t->trans('password'); ?></span>
                    <?php $form->input('passwd'); ?>
                <!--</label>-->
            </li>
            <li>
                <label class="text email">
                    <span class="field_label"><?= $t->trans('e-mail'); ?></span>
                    <?php $form->input('email'); ?>
                    <p class="help"><small><?= $t->trans('required for password recovery'); ?></small></p>
                </label>
            </li>
<?php if ($viewer_is_admin) : ?>
            <li>
                <label class="text">
                    <span class="field_label"><?= $t->trans('account type'); ?></span>
                    <?php $form->input('type', compact('values')); ?>
                </label>
            </li>
<?php endif; ?>
        </ul>
    </fieldset>
    <?php if ($use_captcha) : ?>
    <fieldset>
        <ul class="fields">
            <li class="captcha">
                <?=recaptcha_get_html($recaptcha_publickey);?>
            </li>
        </ul>
    </fieldset>
    <?php endif; ?>

<?php if (!$viewer_is_admin) : ?>
    <p>
        <?=$t->trans('Don\'t forget to read <a href="%s">terms of use</a>.', $t->url2c('Paste', '', array('TOS'))); ?>
    </p>
<?php endif; ?>

<?php
$button_text = $viewer_is_admin ? 'Add user' : 'Accept terms of use and create account';
$t->inc('Forms/buttons', array(
    'form' => & $form,
    'submit' => $t->trans($button_text),
));
$form->end();

