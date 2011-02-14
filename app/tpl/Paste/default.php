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
                        $paster = $t->inc('user_link', array('user'=>$row['Paster']));
                    else
                        $paster = $row['paster'];
                    echo $t->trans('by %s', $paster);
                ?>
            </small>
        </h2>
        <?php if ($row['source']) : ?>
            <p>
                source / original author: <?=$f->linkify($row['source'])?>
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

    <article class="<?=$type->getType()?>">
        <?php
        $type->render();
        ?>
    </article>

</section>

