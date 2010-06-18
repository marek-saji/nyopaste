<?php
/**
 * Creating new account
 *
 * @parm bool $viewer_is_admin
 * @param string $recaptcha_publickey
 * @param bool $use_captcha
 */

$title = $t->trans('create an account');
$v->setTitle($title);

$form = g('Forms', array('new', $this));
?>

<section>
    <header>
        <h2><?=$title?></h2>
    </header>

    <?php
    $form->create();
    ?>
        <fieldset>
            <dl>
                <!-- login -->
                <dt class="text">
                    <label for="<?=$f->nextUniqueId()?>">
                        <?=$t->trans('login')?>
                    </label>
                </dt>
                <dd class="text">
                    <?php
                    $form->input('login');
                    ?>
                </dd>
                <!-- password -->
                <dt class="text password">
                    <label for="<?=$f->nextUniqueId()?>">
                        <?=$t->trans('password')?>
                    </label>
                </dt>
                <dd class="text password">
                    <?php
                    $form->input('passwd');
                    ?>
                </dd>
                <!-- e-mail -->
                <dt class="text email">
                    <label for="<?=$f->nextUniqueId()?>">
                        <?=$t->trans('e-mail')?>
                    </label>
                </dt>
                <dd class="text email">
                    <?php
                    $form->input('email');
                    ?>
                    <p class="help">
                        <small><?= $t->trans('Required for password recovery'); ?></small>
                    </p>
                </dd>
            </dl>
        </fieldset>
        <?php if (g()->auth->loggedIn()) : ?>
            <fieldset>
                <dl>
                    <!-- account type -->
                    <dt class="checkbox">
                        <?=$t->trans('account type')?>
                    </dt>
                    <dd class="checkbox">
                        <?php
                        $form->input('type', array(
                            'values' => & $user_types_values
                        ));
                        ?>
                    </dd>
                </dl>
            </fieldset>
        <?php endif; ?>
        <?php if ($use_captcha) : ?>
            <fieldset>
                <dl>
                    <!-- CAPTCHA -->
                    <dd class="captcha">
                        <?=recaptcha_get_html($recaptcha_publickey);?>
                    </dd>
                </dl>
            </fieldset>
        <?php endif; ?>

        <?php if (!g()->auth->loggedIn()) : ?>
            <p>
                <?=$t->trans('Don\'t forget to read <a href="%s">terms of use</a>.', $t->url2c('Paste', '', array('TOS'))); ?>
            </p>
        <?php endif; ?>

        <?php
        if (g()->auth->loggedIn())
            $button_text = 'create an account';
        else
            $button_text =  'accept terms of use and create an account';
        $t->inc('Forms/buttons', array(
            'form' => & $form,
            'submit' => $button_text,
            'cancel' => 'cancel'
        ));
        ?>

    <?php
    $form->end();
    ?>
</section>

