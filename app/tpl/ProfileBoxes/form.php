<?php
/**
 * Create new user profile box
 * @author m.augustynowicz
 *
 * @param array $user user's data including [Ident]
 * @param bool $own_profile is this logged-in user's profile
 */
extract(
    array_merge(
        array(
            'user'        => null,
            'own_profile' => false,
            'editing'     => false
        ),
        (array) $____local_variables
    ),
    EXTR_REFS|EXTR_PREFIX_INVALID, 'param'
);

$form_ident = 'box';

if ($own_profile)
{
    if ($editing)
    {
        $title = $this->trans('Edit box from your profile');
    }
    else
    {
        $title = $this->trans('Create new box on your profile');
    }
}
else if ($user)
{
    if ($editing)
    {
        $title = $this->trans('Edit box on %s\'s profile');
    }
    else
    {
        $title = $this->trans('Create new box on %s\'s profile', $user['Ident']);
    }
}
else
{
    if ($editing)
    {
        $title = $this->trans('Edit a box');
    }
    else
    {
        $title = $this->trans('Create new box');
    }
}
$v->setTitle($title);
?>


<section class="modal-wannabe">

    <h2><?=$title?></h2>

    <?php
    $form = g('Forms', array('box', $this));
    ?>
    <div class="holoform">

        <?php
        $form->create();
        ?>

        <fieldset>
            <ul>
                <li class="field">
                    <?php
                    $form->label('title', 'title');
                    ?>
                    <?php
                    $form->input('title');
                    ?>
                </li>
                <li class="field">
                    <?php
                    $form->label('query', 'search query');
                    ?>
                    <?php
                    $form->input('query');
                    ?>
                </li>
                <li class="field">
                    <?php
                    $form->label('limit', 'number of items to show');
                    ?>
                    <?php
                    $form->input('limit');
                    ?>
                </li>
                <li class="field">
                    <?php
                    $form->label('list_paster', 'display paster name');
                    ?>
                    <?php
                    $form->input('list_paster');
                    ?>
                </li>
            </ul>
        </fieldset>

        <?php
        $this->inc(
            'Forms/buttons',
            array(
                'form'   => $form,
                'submit' => $editing ? 'edit' : 'add'
            )
        );
        ?>

        <?php
        $form->end();
        ?>

    </div> <!-- .holoform -->

</section>

