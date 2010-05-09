<?php
$t->inc('main_common');

ob_start();
$t->contents();
$contents = ob_get_clean();
?>

<?// $this->view->addCss('style.css');?>
<div id="header">
    <h1>n<em>y</em>opaste</h1>
</div>

<?php
    $this->inc('infos');
?>

<div id="content">
    <h2><?=$v->getTitle()?></h2>
    <div class="content">
        <?=$contents?>
    </div>
</div>

<div id="footer">
    <div class="copyright">
        <small>&copy; Marek Augustynowicz</small>
    </div>
    <div class="powered">
        <small>powered by Hologram</small>
    </div>
</div>

