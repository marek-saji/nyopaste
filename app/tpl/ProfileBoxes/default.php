<?php
$this->inc('row_actions', array(
    'actions'     => $actions,
    'inside_link' => true
));
?>

<?php if (empty($boxes)) : ?>
    <ul class="empty boxes">
        <li>
            <?=$this->trans('no boxes')?>
        </li>
    </ul>
<?php else /* if empty($boxes) */ : ?>
    <ul class="boxes">
        <?php foreach ($boxes as & $box) : ?>
            <li class="box">
                <fieldset>
                    <?php if ($box['title']) : ?>
                        <legend><?=$box['title']?></legend>
                    <?php endif; /* $box['title'] */ ?>

                    <?php if (empty($box['Pastes'])) : ?>
                        <?=$this->trans('no items')?>
                    <?php else /* if empty($box['Pastes']) */ : ?>
                        <ol class="pastes">
                            <?php foreach ($box['Pastes'] as & $paste) : ?>
                                <li>
                                    <?=$this->l2c($this->trans('%s v%s', $paste['title'], $paste['version']), 'Paste', '', array($paste['url'], 'v'=>$paste['version']))?>
                                    <?php if ($box['list_paster']) : ?>
                                        <?=$this->trans('by')?>
                                        <?=$this->inc('paster', array('row' => $paste))?>
                                    <?php endif; /* $box['list_paster'] */ ?>
                                    <time class="published" datetime="<?=$paste['creation']?>">
                                        <?=$f->formatDate($paste['creation'])?>
                                    </time>
                                </li>
                            <?php endforeach; /* $box['Pastes'] */ ?>
                        </ol>
                        <?php
                        $this->inc('row_actions', array(
                            'actions'     => $box['Actions'],
                            'inside_link' => true
                        ));
                        ?>
                    <?php endif; /* if empty($box['Pastes']) else */ ?>
                </fieldset>
            </li>
        <?php endforeach; /* $boxes */ ?>
    </ul> <!-- .boxes -->
<?php endif; /* if empty($boxes) else */ ?>

