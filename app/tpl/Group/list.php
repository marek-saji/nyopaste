<?php
/**
 * List user groups
 * @author m.augustynowicz
 *
 * @param array $rows from GroupModel
 */

$paginator = $this->getChild('p');

?>
<section>
    <ol start="<?=$paginator->getFirstItemIndex()?>">
        <?php if (empty($rows)) : ?>
            <li class="empty">
                <?=$this->trans('No groups to show.')?>
            </li>
        <?php else : ?>
            <?php foreach ($rows as & $row) : ?>
                <li>
                    <h4>
                        <?php
                        echo $this->l2c($row['name'], 'Group', '', array($row['id']));
                        ?>
                        <?php if (@$row['MembersCount']) : ?>
                            <small>
                                <?=$this->trans('%d member(s)', $row['MembersCount'])?>
                            </small>
                        <?php endif; ?>
                    </h4>
                    <?php if (@$row['DisplayDescription']) : ?>
                        <div class="note">
                            <?=$row['DisplayDescription']?>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ol>

    <?php
    $paginator->render();
    ?>
</section>
