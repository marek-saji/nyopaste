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
                    <div class="help">
                        <p>
                            <?=$this->trans('Choose wisely, it cannot be changed later on.')?>
                        </p>
                    </div>
                </li>

                <li class="field">
                    <?php
                    $form->label('passwd', 'password');
                    ?>
                    <?php
                    $form->input('passwd');
                    ?>
                    <div class="help">
                        <p>
                            <?=$this->trans('Enter your password. Twice, just to be sure you didn\'t make a typo.')?>
                        </p>
                    </div>
                </li>

                <li class="field">
                    <?php
                    $form->label('email', 'e-mail');
                    ?>
                    <?php
                    $form->input('email');
                    ?>
                    <div class="help">
                        <p>
                            <?=$this->trans('This is not required, but useful if you happen to forget your password.')?>
                        </p>
                        <p>
                            <?=$this->trans('Don\'t worry, we won\'t to anything evil with it.')?>
                        </p>
                    </div>
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

