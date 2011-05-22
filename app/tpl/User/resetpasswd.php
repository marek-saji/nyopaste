<?php
/**
 * Creating new account
 *
 * @parm bool $viewer_is_admin
 * @param string $recaptcha_publickey
 * @param bool $use_captcha
 */

$title = $t->trans('reset password');
$v->setTitle($title);
?>

<section>

    <header>
        <h2><?=$title?></h2>
    </header>

    <p>
        <?=$this->trans('Hello, %s. You may change your password.', $user['DisplayName'])?>
    </p>

    <p>
        <?=$this->trans('Note that this page will stay valid only one day after you requested password reset.')?>
    </p>


    <?php
    $form = g('Forms', array('passwd_reset', $this));
    ?>
    <div class="holoform" id="content">

        <?php
        $form->create();
        ?>

        <fieldset>
            <ul>

                <li class="field">
                    <?php
                    $form->label('passwd', 'new password');
                    ?>
                    <?php
                    $form->input('passwd');
                    ?>
                    <p class="help">
                        <?=$this->trans('Enter your password. Twice, just to be sure you didn\'t make a typo.')?>
                    </p>
                </li>

            </ul>
        </fieldset>

        <?php
        $this->inc(
            'Forms/buttons',
            array(
                'form'   => $form,
                'submit' => 'set password',
                'cancel' => 'cancel'
            )
        );
        ?>

        <?php
        $form->end();
        ?>

    </div> <!-- .holoform -->

</section>

