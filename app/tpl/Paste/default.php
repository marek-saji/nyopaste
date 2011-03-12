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
            </h2>
            <h3>
                <?php
                if ($row['paster_id'])
                {
                    $paster_html = $t->inc('user_link', array(
                        'user'=>$row['Paster'],
                        'class' => 'author'
                    ));
                }
                else
                {
                    if ($row['paster'])
                        $paster = $row['paster'];
                    else
                        $paster = $t->trans('anonymous');
                    $paster_html = $f->tag(
                        'span',
                        array('class' => 'author vcard'),
                        $f->tag(
                            'i',
                            array('class' => 'fn'),
                            $paster
                        )
                    );
                }
                ?>
                <?=$this->trans('by %s', $paster_html)?>
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
            <?=$t->trans('tags')?>:
            <?php
            $this->inc('tags', array('tags'=>&$row['Tags']));
            ?>
        <?php endif; ?>

        <p>
            <?=$this->trans('created')?>:
            <time class="published" datetime="<?=$row['creation']?>">
                <?=$f->formatDate($row['creation'])?>
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
        </div> <!-- .actions.wrapper -->
    </header>

    <article id="content" class="entry-content <?=$type->getType()?>">
        <?php
        $type->render();
        ?>

        <?php
        //var_dump($row['Tree']);
        $this->inc('tree', array('tree'=>&$row['Tree']));
        ?>
    </article>

</section>

