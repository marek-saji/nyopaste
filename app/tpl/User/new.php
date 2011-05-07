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
?>

<section>

    <header>
        <h2><?=$title?></h2>
    </header>

    <p>
        <?=$this->trans('Fields marked with asterisk are required.')?>
    </p>


    <?php
    $form = g('Forms', array('new', $this));
    ?>
    <div class="holoform" id="content">

        <?php
        $form->create();
        ?>

        <fieldset>
            <ul>
                <li class="field">
                    <?php
                    $form->label('login', 'login');
                    ?>
                    <?php
                    $form->input('login');
                    ?>
                    <p class="help">
                        <?=$this->trans('Choose wisely, it cannot be changed later on.')?>
                    </p>
                </li>

                <li class="field">
                    <?php
                    $form->label('passwd', 'password');
                    ?>
                    <?php
                    $form->input('passwd');
                    ?>
                    <p class="help">
                        <?=$this->trans('Enter your password. Twice, just to be sure you didn\'t make a typo.')?>
                    </p>
                </li>

                <li class="field">
                    <?php
                    $form->label('email', 'e-mail');
                    ?>
                    <?php
                    $form->input('email');
                    ?>
                    <p class="help">
                        <?=$this->trans('You will need this address to activate your accout. Also, if you happen to forget your password in the future, you can use this address to get a new one.')?>
                    </p>
                </li>

                <?php if (g()->auth->loggedIn() && !empty($user_types_values)) : ?>

                    <li class="field">
                        <?php
                        $form->label('type', 'account type');
                        ?>
                        <?php
                        $form->input('type');
                        ?>
                    </li>

                <?php endif; ?>

            </ul>
        </fieldset>

        <?php if ($use_captcha) : ?>
            <fieldset>
                <ul>
                    <!-- CAPTCHA -->
                    <li class="captcha field">
                        <?=recaptcha_get_html($recaptcha_publickey);?>
                    </li>
                </ul>
            </fieldset>
        <?php endif; ?>

        <?php if (!g()->auth->loggedIn()) : ?>
            <p>
                <?=$t->trans('Don\'t forget to read <a href="%s" target="_blank" class="ext">terms of use</a>.', $t->url2c('Paste', '', array('TOS'))); ?>
            </p>
        <?php endif; ?>

        <?php
        if (g()->auth->loggedIn())
            $button_text = 'create an account';
        else
            $button_text = 'accept terms of use and create an account';
        $this->inc(
            'Forms/buttons',
            array(
                'form' => $form,
                'submit' => $button_text,
                'cancel' => 'cancel'
            )
        );
        ?>

        <?php
        $form->end();
        ?>

    </div> <!-- .holoform -->

</section>

