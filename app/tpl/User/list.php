<?php
/**
 * List users
 * @author m.augustynowicz
 *
 * @param array $rows from UserModel
 */

$paginator = $this->getChild('p');

?>
<section>
    <ol start="<?=$paginator->getFirstItemIndex()?>">
        <?php if (empty($rows)) : ?>
            <li class="empty">
                <?=$this->trans('No users to show.')?>
            </li>
        <?php else : ?>
            <?php foreach ($rows as & $row) : ?>
                <li>
                    <?php
                    //echo $this->l2c($row['login'], 'User', '', array($row['login']));
                    echo $this->inc('user_link', array('user'=>$row));
                    ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ol>

    <?php
    $paginator->render();
    ?>
</section>
