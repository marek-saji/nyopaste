<?php
/**
 * Notification about invitation to a closed group
 * @author m.augustynowicz
 *
 * @param array $group
 * @param array $user
 */

$this->assign(
    'mail_subject',
    $this->trans(
        'You have been invited to %s',
        $group['DisplayName']
    )
);
?>
<p><?=$this->trans('Hello, %s.', $user['login'])?></p>

<p><?=$this->trans('You have been invited to %s at %s.', $group['DisplayName'], g()->conf['site_name'])?></p>

<p><?=$this->trans('View group profile at:')?></p>
<p><?php
$url = $this->url2c('Group', '', array($group['id']), true);
echo $f->tag('a', array('href'=>$url), $url);
?></p>


