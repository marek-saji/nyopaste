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

<div id="top">

    <nav class="skip-to-content">
        <a href="#content"><?=$t->trans('skip to content')?></a>
    </nav>

    <header id="head">
        <hgroup>
            <h1>
                <?= $t->l2c('n<i>y</i>opaste', ''); ?>
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

</div>

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
        <p>
            <?=
            $this->trans(
                'This is <abbr title="version">v</abbr>%s of %s.',
                g()->conf['version'],
                $t->l2c('nyopaste', '')
            )
            ?>
        </p>
        <p>
            <?= $this->trans('Created and maintained by <span class="vcard"><a class="fn url" href="https://github.com/marek-saji">Marek Augustynowicz</a></span>.') ?>
        </p>
        <p>
            <?= $this->trans('Licensed under <a rel="license" href="http://www.opensource.org/licenses/mit-license.php">MIT License</a>. Source is available at <a href="https://github.com/marek-saji/nyopaste">GitHub</a>.') ?>
        </p>
        <p>
            <?=$this->trans('If you have an idea how we can improve the site, please <a class="uservoice" href="https://nyopaste.uservoice.com">let us know</a>.')?>
        </p>
    </section> <!-- .powered -->
</footer> <!-- #foot -->


<?php if (null !== ($key = @g()->conf['keys']['google analytics'])) : ?>
    <script type="text/javascript">
        var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
        document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
        </script>
        <script type="text/javascript">
        try {
        var pageTracker = _gat._getTracker("<?=$key?>");
        pageTracker._trackPageview();
        <?php
        /** @todo think of more custom vars, see http://code.google.com/apis/analytics/docs/tracking/gaTrackingCustomVariables.html */
        ?>
        pageTracker._setCustomVar(1, 'authorized', '<?=g()->auth->loggedIn()?'yes':'no'?>', 1);
        } catch(err) {}
    </script>
<?php endif; /* google analytics */ ?>

<?php if (null !== ($key = @g()->conf['keys']['uservoice'])) : ?>
    <script type="text/javascript">
        (function() {
            var uv = document.createElement('script');
            uv.type = 'text/javascript';
            uv.async = true;
            uv.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'widget.uservoice.com/<?=$key?>.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(uv, s);

            $('.uservoice').bind('click.uservoice', function(e){
                e.preventDefault();
                UserVoice.showPopupWidget();
            });
        })();
    </script>
<?php endif; ?>


