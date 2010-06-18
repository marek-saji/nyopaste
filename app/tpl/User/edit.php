<?php
/**
 * Editing user's data
 * @author m.augustynowicz
 *
 * @param array $row one row data
 */

if (g()->auth->id() == $row['id'])
    $title = $t->trans('edit your profile');
else
    $title = $t->trans('edit <em>%s\'s</em> profile', $row['DisplayName'] );
$v->setTitle($title);

$form = g('Forms', array('edit', $this));
?>

<section>
    <header>
        <h2><?=$title?></h2>
        <?php
        $t->inc('row_actions', array(
            'actions' => & $row['Actions']
        ));
        ?>
    </header>

    <?php
    $form->create();
    ?>
        <fieldset>
            <dl>
                <!-- website -->
                <dt class="website text url">
                    <label for="<?=$f->nextUniqueId()?>">
                        <?= $t->trans('website'); ?>
                    </label>
                </dt>
                <dd class="website text url">
                    <?php
                    $form->input('website');
                    ?>
                </dd>
                <!-- about me -->
                <dt class="about_me text big">
                    <label for="<?=$f->nextUniqueId()?>">
                        <?= $t->trans('something about you'); ?>
                    </label>
                </dt>
                <dd class="about_me text big">
                    <?php
                    $form->input('about_me', array(
                        'class' => 'autoexpandable'
                    ));
                    ?>
                </dd>
            </dl>
        </fieldset>
        <?php
        $t->inc('Forms/buttons', array(
            'form' => & $form,
            'submit' => 'update profile info',
            'cancel' => 'cancel'
        ));
        ?>
    <?php
    $form->end();
    ?>
</section>

