<?php if ($tree) : ?>
    <ol class="tree">
        <?php foreach ($tree as &$paste) : ?>
            <li class="node">
                <?=$this->l2a($this->trans('v%s', $paste['version']), '', array($paste['url'], 'v'=>$paste['version']))?>
                <?=$this->trans('by')?>
                <?=$this->inc('paster', array('row' => $paste))?>
                <time class="published" datetime="<?=$paste['creation']?>">
                    <?=$f->formatDate($paste['creation'])?>
                </time>
                <?php
                $this->inc('tree', array('tree'=>&$paste['Children']));
                ?>
            </li>
        <?php endforeach; ?>
    </ol>
<?php endif; ?>
