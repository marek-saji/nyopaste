<?php
$v->setTitle($t->trans('Lost password'));
?>
<section id="content">
    <?php
    echo $t->trans('Instructions have been sent to your e-mail address.');
    echo '<br/>';
    echo $t->l2a($t->trans('Click here for log in.'), 'login', array(), array('class' => 'modal'));
    ?>
</section>
