<?php
/**
 * Renders link to user's profile.
 * @author m.augustynowicz
 * 
 * @param string|null $text text to display,
 *        default (when null): user's display name
 * @param array $user
 *        1. none: authorized user's data used
 *        2. array:
 *           [login] (or whatever is set in conf[users][ident_field])
 *                   created to build URL
 *           [DisplayName] link label
 *           [email] used to generate gravatar
 *        3. string: used as [login] and [DisplayName]
 * @param string $class additional class names
 * @param integer|boolean $avatar size of awatar or false to hide
 *
 * @return string html code
 */

$login_field = g()->conf['users']['ident_field'];

extract(
    array_merge(
        array(
            'user' => array(
                $login_field => g()->auth->get($login_field),
                'DisplayName' => g()->auth->displayName(),
                'email' => g()->auth->get('email'),
            ),
            'class' => '',
            'text' => null,
            'avatar' => 22,
        ),
        (array) $____local_variables
    ),
    EXTR_REFS|EXTR_PREFIX_INVALID, 'param'
);


if (!is_array($user))
{
    $user = array(
        $login_field  => $user,
        'DisplayName' => $user
    );
}

$login = $user[$login_field];

if (null !== $text)
{
    $label = $text;
}
else if (@$user['DisplayName'])
{
    $label = $user['DisplayName'];
}
else if (@$user[$login_field])
{
    $label = $user[$login_field];
}
else
{
    $label = $login;
}

ob_start();
?>
<span class="vcard name <?=$class?>">
    <?php
    if ($avatar && array_key_exists('email', $user))
    {
        $gravatar = sprintf(
            'http://gravatar.com/avatar/%s?%s',
            md5($user['email']),
            http_build_query(array(
                's' => $avatar,
                'd' => 'identicon'
            ))
        );

        $label = sprintf(
            '%s%s',
            $f->tag(
                'img',
                array(
                    'class'  => 'photo',
                    'src'    => $gravatar,
                    'width'  => $avatar,
                    'height' => $avatar
                )
            ),
            $label
        );
    }
    ?>

    <?=$t->l2c($label, 'User', '', array($login), array('class'=>'fn n url'))?>
</span>
<?php
return ob_get_clean();

