<?php
/**
 * Renders link to user's profile.
 * @author m.augustynowicz
 * @author m.jutkiewicz
 *
 * @todo MAKE IT WORK (copied 1:1 from bdb)
 * 
 * @param string $text text to display, default: user's login
 * @param array $user user's information; default value: array with logged-in user's login and e-mail
 * @param string $return if true html code gets returned instead of printed.
 *
 * @return string html code if $return was true
 */
extract(array_merge(array(
	'text' => '',
    'user' => array(
        'id' => g()->auth->id(),
        'login' => g()->auth->get('login'),
        'email' => g()->auth->get('email'),
    ),
    'return' => false,
), (array)$____local_variables), EXTR_PREFIX_INVALID, // and numeric
'param');

if(!is_array($user))
    $user = array('id' => $user);

ob_start();

echo '<div class="vcard name">';
echo $this->l2c(!empty($text) ? $text : $user['login'], 'User', '', array(
    $user['id']
), array(
    'class' => 'fn n url'
));
echo "</div>";

if($return)
    return ob_get_clean();
else
    ob_end_flush();
