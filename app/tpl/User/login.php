<?php
$v->setTitle($this->trans('Login'));
$form = g('Forms', array('login', $this));
?>

<div id="login">
    <?
    if(!@$logged_in)
    {
    ?>
    <?php $form->create($t->url2c('User', 'login')); ?>
        <fieldset>
            <ul class="fields">
                <li>
                    <label class="text">
                        <span class="field_label"><?= $t->trans('login or e-mail'); ?></span>
                        <?php $form->input('login'); ?>
                    </label>
                </li>
                <li>
                    <label class="text password">
                        <span class="field_label"><?= $t->trans('password'); ?></span>
                        <?php $form->input('passwd'); ?>
                    </label>
                </li>
                <li>
                	<label><small><?= $this->l2c($t->trans('I forgot my password'), 'User', 'lostPasswd'); ?></small></label>
                </li>
                <!--
                <li>
                    <label class="checkbox">
                        <input name="long_session"
                               type="checkbox" />
                        remember me
                    </label>
                </li>
                -->
        </fieldset>
        <?= $this->l2c($t->trans('Don\'t you have an account? Register yourself'), 'User', 'add'); ?>
        <?php
        $this->inc('Forms/buttons', array('submit' => 'log in', 'cancel' => false));
        $form->end();
    }
    else
    {
    ?>
        <p><?= $t->trans($t->trans('Thank you for logging in')); ?>.</p>
		<p>Go to <?= $this->l2c('main page', ''); ?>.</a></p>
    <?
    }
    ?>
</div>
