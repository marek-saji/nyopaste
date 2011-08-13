<?php if ($tree) : ?>
    <ol>
        <?php foreach ($tree as &$paste) : ?>
            <li>
                <?=$this->l2a($this->trans('v%s', $paste['version']), '', array($paste['url'], 'v'=>$paste['version']))?>
                <?=$this->trans('by')?>
                <?php if ($paste['paster_id']) : ?>
                    <?php
                    $paster = array(
                        'DisplayName' => $paste['paster'],
                        'login'       => $paste['paster']
                    );
                    ?>
                    <?=$this->inc('paster', array('Paster' => $paster))?>
                <?php else : ?>
                    <?=$this->inc('paster', array('paster' => $paste['paster']))?>
                <?php endif; ?>
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
