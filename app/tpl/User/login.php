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
    <?php
    $form->create($t->url2c($this->url(), $this->getLaunchedAction()));
    ?>
        <fieldset>
            <dl>
                <!-- login -->
                <dt class="text">
                    <label for="<?=$f->nextUniqueId()?>">
                        <?= $t->trans('login or e-mail'); ?>
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
                        <?= $t->trans('password'); ?>
                    </label>
                </dt>
                <dd class="text password">
                    <?php
                    $form->input('passwd');
                    ?>
                    <p class="help">
                        <small><?= $this->l2c($t->trans('I forgot my password'), 'User', 'lostPasswd'); ?></small>
                    </p>
                </dd>
                <!-- options -->
                <dd class="checkbox">
                    <?php
                    /*
                    $form->input('remember_me');
                    */
                    ?>
                </dd>
            </dl>
        </fieldset>
        <?php
        $this->inc('Forms/buttons', array(
            'form' => & $form,
            'submit' => 'sign in',
            'cancel' => 'cancel',
        ));
    $form->end();
?>
</section><!-- #login -->

