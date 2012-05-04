<?php
/**
 * Invite users to group
 * @author m.augustynowicz
 *
 * @param array $row from Group model
 */

$title = $this->trans('Invite to %s', $row['DisplayName']);
$v->setTitle($title);
?>

<section id="content">

    <h2><?=$title?></h2>

    <?php
    $form = g('Forms', array('invite', $this));
    ?>
    <div class="holoform">

        <?php
        $form->create();
        ?>

        <fieldset>
            <ul>
                <li class="field">
                    <?php
                    $form->label('members', 'Users');
                    ?>
                    <?php
                    $form->input('members');
                    ?>
                    <div class="help">
                        <p>
                            <?=$this->trans('Comma-separated logins of users you want to invite to the group.')?>
                        </p>
                    </div>
                </li>
            </ul>
        </fieldset>

        <?php
        $this->inc(
            'Forms/buttons',
            array(
                'form'   => $form,
                'submit' => 'invite'
            )
        );
        ?>

        <?php
        $form->end();
        ?>

    </div> <!-- .holoform -->
</section>

