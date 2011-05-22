<?php
$this->assign(
    'mail_subject',
    $this->trans(
        '%s password reset request',
        g()->conf['site_name']
    )
);

$reset_url = $this->url2a('resetPasswd', array($user['id'], $reset_hash), true);
?>
<p><?=$this->trans('Hello, %s.', $user['DisplayName'])?></p>

<p><?=$this->trans('You got this, because someone used <q>lost password</q> feature on %s and typed in your e-mail. If it wasn\'t you, ignore this and don\'t worry &#8212; your account is safe.', g()->conf['site_name'])?></p>

<p><?=$this->trans('To reset your password, follow the link below (it will work for one day only):')?></p>

<p><a href="<?=$reset_url?>"><?=$reset_url?></a></p>

