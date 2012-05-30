<?php
/**
 * List latest user groups
 * @author m.augustynowicz
 *
 * @param array $rows from GroupModel
 */

$title = $this->trans('Latest groups');
$v->setTitle($title);

$v->addLess($this->file('latest', 'less'));
?>
<section id="content">
    <h2><?=$title?></h2>

    <?php
    $this->inc('row_actions', array(
        'actions'     => $actions,
        'inside_link' => true
    ));
    ?>

    <?php
    $this->inc('list', array('rows' => & $rows));
    ?>
</section>
