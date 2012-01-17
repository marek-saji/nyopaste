<?php
/**
 * Create new user profile box
 * @author m.augustynowicz
 *
 * @param array $user user's data including [Ident]
 * @param bool $own_profile is this logged-in user's profile
 */

$param_defaults = array(
    'user'        => null,
    'own_profile' => false,
    'editing'     => false
);
foreach ($param_defaults as $param => $value)
{
    if (!isset($$param))
    {
        $$param = $value;
    }
}
unset($param, $value);


$form_ident = 'box';

if ($own_profile)
{
    if ($editing)
    {
        $title = $this->trans('Edit pastes list');
    }
    else
    {
        $title = $this->trans('Create new list on your profile');
    }
}
else if ($user)
{
    if ($editing)
    {
        $title = $this->trans('Edit pastes list on %s\'s profile');
    }
    else
    {
        $title = $this->trans('Create new list on %s\'s profile', $user['Ident']);
    }
}
else
{
    if ($editing)
    {
        $title = $this->trans('Edit pastes list');
    }
    else
    {
        $title = $this->trans('Create new pastes list');
    }
}
$v->setTitle($title);
?>


<section class="modal-wannabe" id="content">

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
                    <?php if ($user) : ?>
                        <div class="help">
                            <?php if ($own_profile) : ?>
                                <p><?=$this->trans('Pastes matching this search query will automagically show up on your profile.')?></p>
                                <p><?=$this->trans('To restrict to only your pastes, use <code>paster:%s</code>.', $user['Ident'])?></p>
                            <?php else : ?>
                                <p><?=$this->trans('Pastes matching this search query will automagically show up on %s\'s profile.', $user['DisplayName'])?></p>
                                <?=$this->trans('To restrict to only this user\'s pastes, use <code>paster:%s</code>.', $user['Ident'])?>
                            <?php endif; /* $own */ ?>
                        </div>
                    <?php endif; /* $user */ ?>
                </li>
                <li class="field">
                    <label>
                        <?php
                        ob_start();
                        $form->input('limit');
                        $input = ob_get_clean();
                        ?>
                        <?=$this->trans('show %s latest pastes', $input)?>
                    <?php
                    ?>
                </li>
                <li class="field">
                    <?php
                    $form->input('list_paster', array(
                        'label' => 'display paster name'
                    ));
                    ?>
                    <div class="help">
                        <p><?=$this->trans('You can disable it, if all pastes in the list are from the same person.')?>
                    </div>
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

</section> <!-- #content.modal-wannabe -->

