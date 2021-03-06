<?php
$ctrl     = $this->displayingCtrl();
$ctrl_url = $ctrl->url();
$act      = $ctrl->getLaunchedAction();
$url      = "$ctrl_url/$act";
?>
<ul>
    <?php
    $class = ($url === 'Paste/new') ? 'active' : '';
    ?>
    <li class="<?=$class?>">
        <?=$this->l2c('new paste', 'Paste','new')?>
    </li>

    <?php
    $class = ($url !== 'Paste/new' && $ctrl_url === 'Paste') ? 'active' : '';
    ?>
    <li class="<?=$class?>">
        <?=$this->l2c('pastes', 'Paste','search')?>
    </li>

    <?php
    $class = ($ctrl_url === 'Group') ? 'active' : '';
    ?>
    <li class="<?=$class?>">
        <?=$this->l2c('user groups', 'Group')?>
    </li>

</ul>

