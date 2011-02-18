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
        $form->create();
        ?>

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
                'submit' => 'send',
                'cancel' => 'cancel'
            )
        );
        ?>

        <?php
        $form->end();
        ?>

    </div>

</section>

