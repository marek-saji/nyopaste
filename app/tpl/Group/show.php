<?php
/**
 * User group profile
 * @author m.augustynowicz
 *
 * @param array $row from GroupModel
 */

$title = $this->trans('<em>%s</em> user group', $row['name']);
$v->setTitle($title);

?>

<section id="content">

    <h2><?=$this->trans($title)?></h2>

    <?php
    $this->inc('row_actions', array(
        'actions'     => $actions,
        'inside_link' => true
    ));
    ?>

    <?php
    var_dump($row);
    ?>

</section> <!-- #content -->

