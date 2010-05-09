<?php

$v->addCss($t->file('style','css'));
$clean_label = $t->trans('[clear]');
$v->addInlineJs(<<< JS
if(typeof hg_datepickers == 'undefined')
    var hg_datepickers = {};
hg_datepickers['form#User_edit input.birth_date.date'] = {
    changeMonth: true,
    changeYear: true,
    minDate: '-100Y',
    maxDate: '+0D',
    yearRange: "-100:+100"
};

JS
);

$v->setTitle($t->trans('edit account'));

if(empty(g()->infos['error']))
{
	if($admin)
		$f = 'edit_admin';
	else
		$f = 'edit';

    $form = g('Forms', array($f, $this));
    $form->create($t->url2c('User', 'edit', array($login)), array('autocomplete' => false));

?>
    <fieldset class="User">
        <ul class="fields">
            <li>
                <label class="text">
                    <span class="field_label"><?= $this->trans('login'); ?></span>
                    <?= $this->l2c($login, 'User', '', array($login)); ?>
                </label>
            </li>
<?php
if(!$admin)
{
?>
            <li>
                <!--<label class="password">-->
                    <span class="field_label"><?= $this->trans('old password'); ?></span>
                    <?php $form->input('old_passwd'); ?>
                <!--</label>-->
                <small><?= $t->trans('Required only if you want to change your password'); ?></small>
            </li>
<?php
}
?>
            <li>
                <!--<label class="password">-->
                    <span class="field_label"><?= $this->trans('new password'); ?></span>
                    <?php $form->input('passwd'); ?>
                <!--</label>-->
            </li>
            <li>
                <label class="text email">
                    <span class="field_label"><?= $this->trans('e-mail'); ?></span>
                    <?php $form->input('email'); ?>
                </label>
            </li>
            <li>
                <label class="">
                    <span class="field_label"><?= $this->trans('current photo'); ?></span>
                    <img src="<?= $avatar; ?>" alt="<?= $login; ?>" id="<?= $login; ?>" />
                </label>
            </li>
            <li>
                <label class="">
                    <span class="field_label"><?= $this->trans('delete photo'); ?></span>
                    <?php
                    $del_input = $form->input('del_photo');
					$id = $del_input['id'];
				    $v->addOnLoad("
				    	$('#" . $id . "').click(function ()
				    	{
				    		if($('#" . $id . ":checked').length == 1)
				    		{
				    			$('#photo_li').hide();
				    			$('#photo_li').html($('#photo_li').html());
				    		}
				    		else
				    		{
				    			$('#photo_li').show();
				    		}
				    	});
				    ");
				    ?>
                </label>
            </li>
            <li id="photo_li">
                <label class="">
                    <span class="field_label"><?= $this->trans('new photo'); ?></span>
                    <?php $form->input('photo'); ?>
                </label>
                <small><?= $t->trans('Old photo will be automatically replaced'); ?></small>
            </li>
            <li>
                <label class="text">
                    <span class="field_label"><?= $this->trans('birth date'); ?></span>
                    <?php $form->input('birth_date', array('class' => 'birth_date')); ?>
                </label>
            </li>
            <li>
                <label class="text">
                    <span class="field_label"><?= $this->trans('location'); ?></span>
                    <div class="map"><?php $form->input('location_coords'); ?></div>
                </label>
            </li>
            <li>
                <label class="text">
                    <span class="field_label"><?= $this->trans('country'); ?></span>
                    <?php $form->input('country', compact('values')); ?>
                </label>
            </li>
            <li>
                <label class="text">
                    <span class="field_label"><?= $this->trans('description'); ?></span>
                    <?php $form->input('description'); ?>
                </label>
            </li>
            <li>
                <label class="text">
                    <span class="field_label"><?= $this->trans('e-mail notices'); ?></span>
                    <?php $form->input('notices'); ?>
                </label>
            </li>
            <?php
            /*
			<li>
                <label class="text">
                    <span class="field_label"><?= $this->trans('Status'); ?></span>
                    <?php $form->input('status'); ?>
                </label>
            </li>
            <li>
                <label class="text">
                    <span class="field_label"><?= $this->trans('Type'); ?></span>
                    <?php $form->input('type'); ?>
                </label>
            </li>
			*/
			?>
        </ul>
    </fieldset>
    <?php
    $this->inc('Forms/buttons', array('submit' => 'save', 'form' => $form));
    $form->end();
}
