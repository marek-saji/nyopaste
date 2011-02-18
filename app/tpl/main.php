<?php
/**
 * Layout
 *
 * @author m.augustynowicz
 */

// render all the components first
// (they will set page (sub)title etc)
ob_start();
$this->render();
$contents = ob_get_clean();
?>
<?php
// include JSes, CSS, set page title etc.
$t->inc('main_common');
?>

<header id="head">
    <nav class="skip-to-content">
        <a href="#content"><?=$t->trans('skip to content')?></a>
    </nav>
    <hgroup>
        <h1>
            <?= $t->l2c(g()->conf['site_name'], ''); ?>
        </h1>
        <h2><?=$this->trans('a little more social pastebin')?></h2>
    </hgroup>
</header> <!-- #head -->

<nav id="menu">
    <?php
    // global navigation (<nav id="menu" />)
    $this->getPermaCtrl('menu')->render();
    ?>
</nav> <!-- #menu -->

<?php
// user navigation (<nav class="usernav" />)
$this->getPermaCtrl('usernav')->render();
?>

<?php
// show page infos. will render <aside id="infos" /> at least
$t->inc('infos');
?>

<div id="whole-content">
    <?= $contents ?>
</div> <!-- #content -->

<footer id="foot">
    <nav>
        <?php
        //$this->inc('menu', array('name'=>'footer', 'menu' => g()->conf['menu']['foot']));
        ?>
    </nav>
    <section class="tech">
        <span class="powered"><?=$t->trans('powered by %s', '<a class="hg">Hologram</a>')?></span>
        <span class="ver"><abbr title="<?=$t->trans('application version')?>">app v.</abbr> <?= g()->conf['version']?></span>
    </section> <!-- .powered -->
</footer> <!-- #foot -->

<?php
if (null !== @g()->conf['keys']['google analytics']) :
?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("<?=g()->conf['keys']['google analytics']?>");
pageTracker._trackPageview();
<?php
/** @todo think of more custom vars, see http://code.google.com/apis/analytics/docs/tracking/gaTrackingCustomVariables.html */
?>
pageTracker._setCustomVar(1, 'authorized', '<?=g()->auth->loggedIn()?'yes':'no'?>', 1);
} catch(err) {}</script>

<?php
endif; // google analytics

