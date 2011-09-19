<?php
/**
 * Displaying a paste
 * @author m.augustynowicz
 *
 * @param array $row one row data
 */

$title = $row['title'];
$v->setTitle($title);
$v->setMeta('og:type', 'article', 'property');
$v->setMeta('og:url', $this->url2a('', array($row['url']), true), 'property');
$v->setDescription($this->trans(
    'a paste by %s',
    $row['Paster'] ? $row['Paster']['DisplayName'] : $row['paster']
));

$v->addLess($this->file('default', 'less'));
$v->addJs($this->file('default', 'js'));

$meta_data_toggler = json_encode(array(
    'showLabel' => $this->trans('more info'),
    'hideLabel' => $this->trans('less info')
));
?>

<section class="paste hentry" id="paste">
    <header>
        <hgroup>
            <h2 class="entry-title">
                <?=$title?>
                <span class="version">
                    <?=$this->trans('v%s', $row['version'])?>
                </span>
            </h2>
            <h3>
                <?=$this->trans('by %s', $this->inc('paster', $row))?>
            </h3>
        </hgroup>

        <dl <?=$f->xmlAttr(array('id' => 'meta', 'data-toggler' => $meta_data_toggler))?>>

            <dt><?=$this->trans('created')?></dt>
            <dd>
                <time class="published" datetime="<?=$row['creation']?>">
                    <?=$f->formatDate($row['creation'], DATE_HUMAN_FORMAT|DATE_SHOW_ALL)?>
                </time>
            </dd>

            <?php if ($row['source_url']) : ?>
                <dt><?=$t->trans('source URL')?></dt>
                <dd><a href="<?=$row['source_url']?>"><?=$row['source_url']?></a></dd>
                </p>
            <?php endif; ?>

            <?php if ($row['author']) : ?>
                <dt><?=$t->trans('original author')?></dt>
                <dd><?=$row['author']?></dd>
            <?php endif; ?>

            <?php if (!$f->anyToBool($row['publicly_versionable'])) : ?>
                <dt><?=$this->trans('creating new versions by other users denied')?></dt>
            <?php endif; ?>

            <?php
            $type->inc('meta');
            ?>

            <?php if ($row['Tags']) : ?>
                <dt><?=$t->trans('tags')?></dt>
                <dd>
                    <?php
                    $this->inc('tags', array('tags'=>&$row['Tags']));
                    ?>
                </dd>
            <?php endif; ?>

        </dl>

        <div class="actions wrapper">
            <?php
            $t->inc('row_actions', array(
                'actions' => & $row['Actions']
            ));
            ?>
            <?php
            $t->inc('row_actions', array(
                'actions' => & $row['BasicActions'],
                'class' => 'basic'
            ));
            ?>
        </div> <!-- .actions.wrapper -->
    </header>

    <article id="content" class="entry-content <?=$type->getType()?>">
        <?php
        $type->render();
        ?>
    </article>

    <div id="share" class="modaled">
        <h3>
            <?=$this->trans('share this paste')?>
        </h3>
        <?php
        $permalink = g()->req->getBaseUri(true)
            . sprintf(
                g()->conf['paste']['permalink'],
                $this->getParam(0), $this->getParam('v')
            )
        ;
        $share_text   = $this->trans('%s', $row['title']);
        $mail_subject = $this->trans('%s at %s', $row['title'], strip_tags(g()->conf['site_name']));
        $mail_body    = $this->trans("Take a look at this:\n\n%2\$s", $row['title'], $permalink);

        $this->inc('share', array(
            'link'         => $permalink,
            'title'        => $share_text,
            'mail_subject' => $mail_subject,
            'mail_body'    => $mail_body
        ));
        ?>
    </div>

    <?php if (@$row['Tree'][$row['root_id']]['Children']) : ?>
        <section class="tree">
            <h3><?=$this->trans('All versions of this paste:')?></h3>

            <?php
            $this->inc('tree', array('tree'=>&$row['Tree']));
            ?>
        </section>
    <?php endif; ?>

</section>

<?php
$url_json = json_encode($this->url2a('newVerCheck', array($row['url'], 'timestamp'=>$timestamp)));
$msg_json = json_encode($this->trans('New version(s) of this paste available. You may want to refresh the page, to see them.'));
$ver_check_timeout_json = (int) $ver_check_timeout;;

$v->addOnLoad( <<< VER_CHECKER

    newVerChecker({$url_json}, {$ver_check_timeout_json}, {$msg_json});

VER_CHECKER
);

