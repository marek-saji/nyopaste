<?php
/**
 * Siging in
 * @author m.augustynowicz
 */

$title = $this->trans('sign in');
$v->setTitle($title);

$form = g('Forms', array('login', $this));
?>

<section id="login" class="modal-wannabe">

    <header>
        <h2><?=$title?></h2>
    </header>

    <?php
    $form = g('Forms', array('login', $this));
    ?>
    <div class="holoform" id="content">

        <?php
        $form->create($this->url2a($this->getLaunchedAction()));
        ?>

        <fieldset>
            <ul>
                <li class="field">
                    <?php
                    $form->label('login', 'login or e-mail');
                    ?>
                    <?php
                    $form->input('login');
                    ?>
                    <p class="help">
                        <?=$this->trans('Enter your login. E-mail address is fine too.')?>
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
                        <?=$this->trans('Forgot your password? You may %s.', $this->l2c($t->trans('reset it'), 'User', 'lostPasswd', array(), array('class'=>'modal', 'title'=>$this->trans('reset your password'))))?>
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


