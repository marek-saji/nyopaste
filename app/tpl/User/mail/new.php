<?php
$this->assign(
    'mail_subject',
    $this->trans(
        'Welcome to %s',
        g()->conf['site_name']
    )
);

?>
<p><?=$this->trans('Hello, %s.', $user_login)?></p>

<p><?=$this->trans('Go ahead and fill in your profile: <a href="%s">%1$s</a>', $profile_url)?></p>

