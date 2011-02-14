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
                <dt class="email text url">
                    <label for="<?=$f->uniqueId(false)?>">
                        <?= $t->trans('email'); ?>
                    </label>
                </dt>
                <dd class="email text url">
                    <?php
                    $form->input('email');
                    ?>
                </dd>
                <!-- website -->
                <dt class="website text url">
                    <label for="<?=$f->uniqueId(false)?>">
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
                    <label for="<?=$f->uniqueId(false)?>">
                        <?= $t->trans('something about you'); ?>
                    </label>
                </dt>
                <dd class="about_me text big">
                    <?php
                    $form->input('about_me', array(
                        'class' => 'autoexpandable'
                    ));
                    ?>
                    <p class="help">
                        <small>
                            You can use
                            <a href="http://daringfireball.net/projects/markdown/syntax">Markdown</a>
                            here. With some
                            <a href="http://michelf.com/projects/php-markdown/extra/">extra syntax</a>.
                        </small>
                    </p>
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

