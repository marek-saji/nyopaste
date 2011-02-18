<?php
$v->setTitle($t->trans('recover account'));
?>
<section id="content">
    <?php
    $form = g('Forms', array('delete', $this));
    $form->create($t->url2c('User', 'delete', array($login, 'recover')));
    echo $this->trans('Are you sure to recover this user?');
    $form->input('id');
    $this->inc('Forms/buttons', array('submit' => $this->trans('recover')));
    $form->end();
    ?>
</section>
