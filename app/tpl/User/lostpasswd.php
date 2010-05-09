<?php

$v->setTitle($t->trans('Lost password'));
$form = g('Forms', array('lostpasswd', $this));
$form->create();
?>
<fieldset>
<ul class="fields">
	<li>
		<label class="text">
			<span class="field_label"><?=$this->trans('e-mail')?></span>
            <?php $form->input('email'); ?>
        </label>
    </li>
</fieldset>
<?php
$this->inc('Forms/buttons', array('submit' => $this->trans('send')));
$form->end();
