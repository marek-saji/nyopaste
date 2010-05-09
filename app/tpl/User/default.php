<?php
$v->addCss($t->file('style','css'));

if(!empty($data['login']))
{
    $v->setTitle($t->trans('%s\'s profile', $data['login']));
?>
        <ul class="user_properties">
            <li>
                <span><!--<?= $this->trans('login'); ?>--></span>
                <h2><?= $data['login']; ?></h2>
            </li>

            <li>
            	<img src="<?= $avatar; ?>" alt="<?= $data['login']; ?>" id="<?= $data['login']; ?>" />
                <span><!--<?= $this->trans('description'); ?>--></span>
                <p><?= $data['description']; ?></p>
            </li>
        </ul>
<?php

    $this->comment_component->render();
    
    $this->trip_component->contents();

    if($links['edit'])
        echo $t->l2a($t->trans('edit account'), 'edit', array($data['login']), array('class' => 'edit_acount'));

    if($links['delete'])
        echo $t->l2a($t->trans('delete account'), 'delete', array($data['login']), array('class' => 'modal edit_acount'));

    if($links['recover'])
        echo $t->l2a($t->trans('recover account'), 'delete', array($data['login'], 'recover'), array('class' => 'modal edit_acount'));

    if($links['changeType'])
        echo $t->l2a($t->trans('change account type'), 'changeType', array($data['login']), array('class' => 'modal edit_acount'));
}
