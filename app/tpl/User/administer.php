<?php
$v->addCss($t->file('style','css'));
echo '<ul class="users_list">';

foreach($list as $user)
{
    printf('<li class="%s %s">', ($user['type'] == USER_TYPE_ADMIN) ? 'administrator' : 'moderator', ($user['status'] != STATUS_ACTIVE) ? 'deleted' : '');
    echo '<h2>' . $t->l2a($user['login'], '', array($user['login'])) . '</h2><div style="clear:both; margin: 1em;"></div>';
	
    echo $t->l2a($t->trans('edit'), 'edit', array($user['login']));
    echo ' ';

    if($user['status'] != STATUS_ACTIVE)
        echo $t->l2a($t->trans('recover'), 'delete', array($user['login'], 'recover'), array('class' => 'modal edit_acount'));
    else
        echo $t->l2a($t->trans('delete'), 'delete', array($user['login']), array('class' => 'modal edit_acount'));

    echo ' ';
    echo $t->l2a($t->trans('change account type'), 'changeType', array($user['login']), array('class' => 'modal edit_acount'));
    echo '</li>';
}

echo '</ul>';
echo $t->l2a($t->trans('add new user'), 'add', array('admin' => 1), array('class' => 'edit_acount'));
