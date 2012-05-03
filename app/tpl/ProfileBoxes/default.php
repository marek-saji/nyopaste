<?php
$v->addLess($this->file('default', 'less'));
?>

<section class="boxes">

    <?php
    $this->inc('row_actions', array(
        'actions'     => $actions,
        'inside_link' => true
    ));
    ?>

    <?php if (empty($boxes)) : ?>

        <div class="empty boxes">
            <?php if ($this->getParent()->getAssigned('profile_owner')) : ?>
                <p><?=$this->trans('You can create lists of pastes here for everyone to see.')?></p>
                <?php if (!@$owner_is_group) : ?>
                    <p><?=$this->trans('These can be your pastes, but don\'t have to.')?></p>
                <?php endif;  ?>
                <p>
                    <?=$this->l2aInside('add your first list', 'new', array('#' => 'content'), array('class' => 'modal'))?>
                </p>
            <?php else : ?>
                <p>
                    <?php if (@$owner_is_group) : ?>
                        <?=$this->trans('Group did not define any lists yet.')?>
                    <?php else : ?>
                        <?=$this->trans('User did not define any lists yet.')?>
                    <?php endif; ?>
                </p>
            <?php endif;?>
        </div>

    <?php else /* if empty($boxes) */ : ?>

        <ul class="boxes">
            <?php foreach ($boxes as & $box) : ?>
                <li class="box">
                    <fieldset>
                        <?php if ($box['title']) : ?>
                            <legend><?=$box['title']?></legend>
                        <?php endif; /* $box['title'] */ ?>

                        <?php if (empty($box['Pastes'])) : ?>
                            <p class="empty pastes">
                                <?=$this->trans('no pastes')?>
                            </p>
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

</section> <!-- .boxes -->
