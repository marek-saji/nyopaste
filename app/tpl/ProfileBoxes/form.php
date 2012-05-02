<?php
/**
 * Create new profile box
 * @author m.augustynowicz
 *
 * @param array $profile profile data
 * @param bool $own_profile is this logged-in user's profile or is he a group leader
 */

$param_defaults = array(
    'profile'     => null,
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
else if ($profile)
{
    if ($editing)
    {
        $title = $this->trans('Edit pastes list on a profile of %s', $profile['DisplayName']);
    }
    else
    {
        $title = $this->trans('Create new list on a profile of %s', $profile['DisplayName']);
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
                    <?php if ($profile) : ?>
                        <div class="help">
                            <?php if ($own_profile) : ?>
                                <p><?=$this->trans('Pastes matching this search query will automagically show up on your profile.')?></p>
                                <p><?=$this->trans('To restrict to only your pastes, use <code>paster:%s</code>.', g()->auth->ident())?></p>
                            <?php else : ?>
                                <p><?=$this->trans('Pastes matching this search query will automagically show up on this profile.')?></p>
                                <p><?=$this->trans('To restrict to only this user\'s pastes, use <code>paster:%s</code>.', g()->auth->ident())?></p>
                            <?php endif; /* $own */ ?>
                        </div>
                    <?php endif; /* $profile */ ?>
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

