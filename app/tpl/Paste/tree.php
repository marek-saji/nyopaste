<?php
/**
 * Pasts tree.
 * @author m.augustynowicz
 *
 * @param array $tree
 * @param integer $current current paste's id
 */
?>
<?php if ($tree) : ?>
    <ol class="tree">
        <?php foreach ($tree as &$paste) : ?>
            <?php
            $active = ($paste['id'] == $current);
            ?>
            <li class="node">

                <?php if ($active) : ?>
                    <strong>
                <?php endif; ?>

                <?=$this->l2a($this->trans('v%s', $paste['version']), '', array($paste['url'], 'v'=>$paste['version']))?>
                <?=$this->trans('by')?>
                <?=$this->inc('paster', array('row' => $paste))?>
                <time class="published" datetime="<?=$paste['creation']?>">
                    <?=$f->formatDate($paste['creation'])?>
                </time>

                <?php if ($active) : ?>
                    </strong>
                <?php endif; ?>

                <?php
                $this->inc('tree', array(
                    'tree'    => & $paste['Children'],
                    'current' => $current
                ));
                ?>
            </li>
        <?php endforeach; ?>
    </ol>
<?php endif; ?>
