<?php
/**
 * Common template for adding and editing user gorup
 * @author m.augustynowicz
 *
 * @param bool $editing
 * @param array $row from GroupModel, only used when $editing is true
 */

if ($editing)
{
    $title = $this->trans('Edit <em>%s</em> group', $row['name']);
}
else
{
    $title = $this->trans('Create new group');
}
$v->setTitle($title);

$form = g('Forms', array('form', $this));

?>
<section id="content">
    <div class="holoform">

        <h2><?=$title?></h2>

        <?php
        $form->create();
        ?>

        <fieldset>
            <ul>
                <li class="field">
                    <?php
                    $form->label('name', 'name');
                    ?>
                    <?php
                    $form->input('name');
                    ?>
                </li>
                <li class="field">
                    <?php
                    $form->label('description', 'description');
                    ?>
                    <?php
                    $form->input('description');
                    ?>
                </li>
                <li class="field">
                    <?php
                    $form->label('website', 'website');
                    ?>
                    <?php
                    $form->input('website');
                    ?>
                </li>
                <li class="field">
                    <?php
                    $form->input('open', array(
                        'label' => 'new members join without verifying'
                    ));
                    ?>
                </li>
                <li class="field">
                    <?php
                    $form->input('hidden', array(
                        'label' => 'visible only for members'
                    ));
                    ?>
                </li>
            </ul>
        </fieldset>

        <?php
        $this->inc(
            'Forms/buttons',
            array(
                'form'   => $form,
                'submit' => $editing ? 'save' : 'add'
            )
        );
        ?>

        <?php
        $form->end();
        ?>

    </div> <!-- .holoform -->
</section> <!-- #content -->
