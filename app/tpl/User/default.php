<?php
/**
 * User's profile
 * @author m.augustynowicz
 *
 * @param array $row one row data
 */

$title = $row['DisplayName'];
$v->setTitle($title);

?>

<section>
    <header>
        <h2><?=$title?></h2>
        <?php
        $t->inc('row_actions', array(
            'actions' => & $row['Actions']
        ));
        ?>
    </header>

    <dl>
        <?php if ($row['website']) : ?>
            <!-- website -->
            <dt class="website text url">
                <?=$t->trans('website')?>
            </dt>
            <dd class="website text url">
                <a href="<?=$row['website']?>"
                   target="_blank"><?=$row['website']?></a>
            </dd>
        <?php endif; ?>
        <?php if ($row['AboutMe']) : ?>
            <!-- about me -->
            <dt class="about_me text big">
                <?=$t->trans('something about you')?>
            </dt>
            <dd class="about_me text big">
                <?=$row['AboutMe']?>
            </dd>
        <?php endif; ?>
    </dl>
</section>

