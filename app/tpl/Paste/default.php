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
?>

<section class="paste hentry">
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
        </h2>
        <?php if ($row['source_url']) : ?>
            <p>
                <?=$t->trans('source URL')?>: <a href="<?=$row['source_url']?>"><?=$row['source_url']?></a>
            </p>
        <?php endif; ?>
        <?php if ($row['author']) : ?>
            <p>
                <?=$t->trans('original author')?>: <?=$row['author']?>
            </p>
        <?php endif; ?>
        <?php if ($row['Tags']) : ?>
            <div>
                <?=$t->trans('tags')?>:
                <?php
                $this->inc('tags', array('tags'=>&$row['Tags']));
                ?>
            </div>
        <?php endif; ?>

        <p>
            <?=$this->trans('created')?>:
            <time class="published" datetime="<?=$row['creation']?>">
                <?=$f->formatDate($row['creation'], DATE_HUMAN_FORMAT|DATE_SHOW_ALL)?>
            </time>
        </p>

        <?php
        $type->inc('meta');
        ?>

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
            <div id="share" class="modaled">
                <nav class="share actions">
                    <?php /*
                    <script type="text/javascript">var addthis_config = {"data_track_clickback":true};</script>
                    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4d9267601252e98f"></script>
                    */ ?>
                    <?php
                    $permalink  = $this->url2a($this->getLaunchedAction(), array($this->getParam(0), 'v' => $this->getParam('v')), true);
                    $share_text = $this->trans('%s', $row['title']);
                    ?>
                    <ul>
                        <li class="addthis twitter action">
                            <?php
                            $twitter_q = http_build_query(array(
                                'text' => $share_text,
                                'url'  => $permalink // will get shortened by t.co
                            ));
                            ?>
                            <a class="action share twitter addthis_button_twitter" href="https://twitter.com/intent/tweet?<?=$twitter_q?>">tweet</a>
                        </li>
                        <li class="addthis identica action">
                            <?php
                            $identica_q = http_build_query(array(
                                'action'          => 'newnotice',
                                'status_textarea' => sprintf("%s\n%s", $share_text, $permalink)
                            ));
                            ?>
                            <a class="action share identica addthis_button_identica" href="http://identi.ca/index.php?<?=$identica_q?>">post to identi.ca</a>
                        </li>
                        <li class="addthis email action">
                            <?php
                            $mail_q = http_build_query(array(
                                'subject' => $this->trans('%s at %s', $row['title'], strip_tags(g()->conf['site_name'])),
                                'body'    => $this->trans("Take a look at this:\n\n%2\$s", $row['title'], $permalink)
                            ));
                            ?>
                            <a class="action share mail addthis_button_email" href="mailto:?<?=$mail_q?>">mail</a>
                        </li>
                        <li class="addthis compact action">
                            <?php
                            $addthis_q = http_build_query(array(
                                'title' => $row['title'],
                                'url'   => $permalink
                            ));
                            ?>
                            <a class="action share addthis addthis_button_compact" href="http://addthis.com/bookmark.php?<?=$addthis_q?>"><?=$this->trans('share via addthis <small class="nojs">(requires JavaScript)</small>')?></a>
                        </li>
                        <li class="copy-to-clipboard action">
                            <?php
                            //$v->loadJsLib('zeroclipboard');
                            ?>
                            <p>
                                <?=$this->trans('&hellip; or copy link to this paste and do whatever you want')?>
                            </p>
                            <label class="copyable">
                                <?php
                                echo $f->tag(
                                    'input',
                                    array(
                                        'type'          => 'text',
                                        'class'         => 'copyable',
                                        'value'         => $permalink,
                                        'id'            => $f->uniqueId(),
                                        'readonly'      => 'readonly',
                                        'data-copyable' => json_encode(array(
                                            'text'      => $this->trans('copy'),
                                            'title'     => $this->trans('click to copy this paste URL to clipboard'),
                                            'afterText' => $this->trans('copied!'),
                                        ))
                                    )
                                );
                                ?>
                            </label>
                        </li>
                    </ul>
                </nav>
            </div>
        </div> <!-- .actions.wrapper -->
    </header>

    <article id="content" class="entry-content <?=$type->getType()?>">
        <?php
        $type->render();
        ?>
    </article>

    <?php if (@$row['Tree'][$row['root_id']]['Children']) : ?>
        <section class="tree">
            <h3><?=$this->trans('Other versions of this paste:')?></h3>

            <?php
            $this->inc('tree', array('tree'=>&$row['Tree']));
            ?>
        </section>
    <?php endif; ?>

</section>

