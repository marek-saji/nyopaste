<?php
/**
 * List users
 * @author m.augustynowicz
 *
 * @param int $leader_id (optional)
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
                    echo $this->inc('user_link', array('user'=>$row));
                    ?>
                    <?php if ($row['id'] == $leader_id) : ?>
                        <small>
                            (<?=$this->trans('leader')?>)
                        </small>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ol>

    <?php
    $paginator->render();
    ?>
</section>
