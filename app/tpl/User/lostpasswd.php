<?php
$title = $t->trans('Lost password');
$v->setTitle($title);
?>

<section>

    <header>
        <h2><?=$title?></h2>
    </header>

    <?php
    $form = g('Forms', array('lostpasswd', $this));
    ?>
    <div class="holoform" id="content">

        <?php
        $form->create($this->url2a($this->getLaunchedAction()));
        ?>

        <p>
            <?=$this->trans('Enter your e-mail address and you will receive a link allowing you to reset your password.')?>
        </p>

        <fieldset>
            <ul>
                <li class="field">
                    <?php
                    $form->label('email', 'e-mail');
                    ?>
                    <?php
                    $form->input('email');
                    ?>
                </li>
            </ul>
        </fieldset>

        <?php
        $this->inc(
            'Forms/buttons',
            array(
                'form' => $form,
                'submit' => 'send me the reset link',
                'cancel' => 'cancel'
            )
        );
        ?>

        <?php
        $form->end();
        ?>

    </div>

</section>

