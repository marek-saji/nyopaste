<?php
/**
 * Layout
 *
 * @author m.augustynowicz
 */

// render all the components first
// (they will set page (sub)title etc)
ob_start();
$this->contents();
$contents = ob_get_clean();

//$v->addCss($this->file('common', 'css'));
// include JSes, CSS, set page title etc.
$t->inc('main_common');

// show page infos. will render <aside id="infos" /> at least
$t->inc('infos');
?>

<header id="head">
    <h1>
        <?= $t->l2c(g()->conf['site_name'], ''); ?>
    </h1>
    <nav>
        <ul>
            <?php
            if (g()->auth->loggedIn()) :
                $username = g()->auth->get('login');
            ?>
                <li class="welcome">
                    <?= $t->l2c($username, 'User', '', array($username)) ?>
                </li>
                <li class="signout">
                    <?= $t->l2c($t->trans('Sign Out'), 'User', 'logout'); ?>
                </li>
            <?php
            else :
            ?>
                <li class="signin">
                    <?= $t->l2c($t->trans('Sign In'), 'User', 'login', array(), array('class' => 'btn modal', 'anchor' => 'login')); ?>
                </li>
                <li class="create_account">
                    <?= $this->l2c($t->trans('Create an Account'), 'User', 'add'); ?>
                </li>
            <?php
            endif;
            ?>
        </ul>
    </nav>
</header> <!-- #head -->

<nav id="menu">
    <?php
        echo 'menu goes here';
        // $this->inc('menu');
    ?>
</nav> <!-- #menu -->

<div id="content">
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
if (PROD_ENV == ENVIRONMENT) :
    /** @todo fill up UA code and uncomment */
    /** @todo think of more custom vars, see http://code.google.com/apis/analytics/docs/tracking/gaTrackingCustomVariables.html */
?>
<!--
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-1337666-0");
pageTracker._trackPageview();
pageTracker._setCustomVar(1, 'authorized', '<?=g()->auth->loggedIn()?'yes':'no'?>', 1);
} catch(err) {}</script>
-->

<?php
endif; // prod environment

