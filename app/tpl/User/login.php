<?php
/**
 * Siging in
 * @author m.augustynowicz
 */

$title = $this->trans('sign in');
$v->setTitle($title);

$form = g('Forms', array('login', $this));
?>

<section id="login">

    <header>
        <h2><?=$title?></h2>
    </header>

    <?php
    $form = g('Forms', array('login', $this));
    ?>
    <div class="holoform">

        <?php
        $form->create($this->url2a($this->getLaunchedAction()));
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
                </li>

                <li class="field">
                    <?php
                    $form->label('passwd', 'password');
                    ?>
                    <?php
                    $form->input('passwd');
                    ?>
                    <p class="help">
                        <small><?= $this->l2c($t->trans('I forgot my password'), 'User', 'lostPasswd'); ?></small>
                    </p>
                </li>

                <!--
                <li>
                    <?php
                    /*
                    $form->input('remember_me');
                    */
                    ?>
                </li>
                -->
            </ul>
        </fieldset>

        <?php
        $this->inc(
            'Forms/buttons',
            array(
                'form' => $form,
                'submit' => 'sign in'
            )
        );
        ?>

        <?php
        $form->end();
        ?>

    </div> <!-- .holoform -->

</section><!-- #login -->


