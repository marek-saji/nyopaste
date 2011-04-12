<?php if ($tree) : ?>
    <ol>
        <?php foreach ($tree as &$paste) : ?>
            <li>
                <?=$this->l2a($this->trans('v%s', $paste['version']), '', array($paste['url'], 'v'=>$paste['version'], 'id'=>$paste['id']))?>
                <?=$this->trans('by')?>
                <?=$this->inc('user_link', array('user'=>&$paste['paster']))?>
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
