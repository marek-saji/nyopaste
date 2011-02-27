<?php
/**
 * Displaying a paste
 * @author m.augustynowicz
 *
 * @param array $row one row data
 */

$title = $row['title'];
$v->setTitle($title);

?>

<section>
    <header>
        <h2>
            <?=$title?>
            <small>
                <?php
                if ($row['paster_id'])
                    $paster_html = $t->inc('user_link', array('user'=>$row['Paster']));
                else
                {
                    if ($row['paster'])
                        $paster = $row['paster'];
                    else
                        $paster = $t->trans('anonymous');
                    $paster_html = $f->tag(
                        'span',
                        array('class' => 'vcard'),
                        $f->tag(
                            'i',
                            array('class' => 'fn'),
                            $paster
                        )
                    );
                }
                ?>
                <?=$paster_html?>
            </small>
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
            <p>
                <?=$t->trans('tags')?>: <?=$this->inc('tags', array('tags'=>&$row['Tags']))?>
            </p>
        <?php endif; ?>

        <?php
        $type->inc('meta');
        ?>

        <?php
        $t->inc('row_actions', array(
            'actions' => & $row['Actions']
        ));
        ?>
    </header>

    <article id="content" class="<?=$type->getType()?>">
        <?php
        $type->render();
        ?>
    </article>

</section>

