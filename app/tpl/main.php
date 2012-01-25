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
        <small>
            <a href="#content"><?=$t->trans('skip to content')?></a>
        </small>
    </nav>

    <header id="head">
        <h1>
            <?= $t->l2c('<span class="kern_n">n</span><i class="kern_ny">y</i><span class="kern_yo">o</span><span class="kern_op">p</span><span class="kern_pa">a</span><span class="kern_as">s</span><span class="kern_st">t</span><span class="kern_te">e</span>', '', '', array(), array('class'=>'nyopaste')); ?>
        </h1>
        <small><?=$this->trans('a little more social pastebin')?></small>
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

    <?php if ($this->getLaunchedAction() !== 'search') : ?>
        <div class="search">
            <?php
            // render without using Form class to save memory
            ?>
            <?php
            echo $f->tag(
                'form',
                array(
                    'action' => $this->url2c('Paste', 'search'),
                    'method' => 'post'
                ),
                null,
                'open'
            );
            ?>
                <?php
                echo $f->tag(
                    'input',
                    array(
                        'type' => 'search',
                        'class' => 'query',
                        'name' => 'Paste_search[query]',
                        'placeholder' => $this->trans('search for a paste'),
                        'autosave' => 'search'
                    )
                );
                ?>
                <?php
                echo $f->tag(
                    'input',
                    array(
                        'type' => 'submit',
                        'value' => $this->trans('search')
                    )
                );
                ?>
            <?php
            echo $f->tag('form', array(), null, 'close');
            ?>
        </div>
    <?php endif; /* launched action !== search */ ?>

</div> <!-- #top -->

<?php
// show page infos. will render <aside id="infos" /> at least
$t->inc('infos');
?>

<div id="whole-content">
    <?= $contents ?>
</div> <!-- #content -->

<footer id="foot">
    <section class="copyright">
        <p>
            <?=
            $this->trans(
                'This is <abbr title="version">v</abbr>%s of %s.',
                g()->conf['version'],
                g()->conf['site_name']
            )
            ?>
        </p>
        <p>
            <?php
            $admin_login   = g()->conf['users']['admin']['login'];
            $admin_profile = $this->url2c('User', '', array($admin_login));
            ?>
            <?= $this->trans('Created and maintained by <span class="vcard"><a class="fn url avoid-br" href="%s">Marek Augustynowicz</a></span>.', $admin_profile) ?>
        </p>
        <p>
            <?= $this->trans('The code is licensed under <a rel="license" href="http://www.opensource.org/licenses/mit-license.php" class="avoid-br">MIT License</a> and available at <a href="https://github.com/marek-saji/nyopaste">GitHub</a>.') ?>
        </p>
    </section>
    <section class="more">
        <p>
            <?=$this->trans('If you have an idea how we can improve the site, please <a class="uservoice avoid-br" href="https://nyopaste.uservoice.com">let us know</a>.')?>
        </p>
        <p>
            <?php
            echo $this->trans('Before using %s, read %s.',
                g()->conf['site_name'],
                $this->l2c($this->trans('Terms of Service'), 'Paste', '', array('TOS'), array('class'=>'avoid-br'))
            );
            ?>
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
            var uv;
            if (hg('nyroModalFit')())
            {
                uv = document.createElement('script');
                uv.type = 'text/javascript';
                uv.async = true;
                uv.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'widget.uservoice.com/<?=$key?>.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(uv, s);

                $('.uservoice').bind('click.uservoice', function(e){
                    e.preventDefault();
                    UserVoice.showPopupWidget();
                });
            }
        })();
    </script>
<?php endif; ?>


