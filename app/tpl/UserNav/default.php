<?php
/**
 * UserNav, action default: user account navigation
 * @author m.augustynowicz
 */
?>

<nav class="usernav">
    <ul>

        <?php if (g()->auth->loggedIn()) : ?>

            <li class="welcome">
                <?= $this->inc('user_link') ?>
            </li>
            <li class="signout">
                <?= $t->l2c($t->trans('sign out'), 'User', 'logout') ?>
            </li>

        <?php else : /* if lgged in */ ?>

            <li class="signin">
                <?=$t->l2c($t->trans('sign in'), 'User', 'login', array(), array('class' => 'modal', 'anchor' => 'login'))?>
            </li>
            <li class="create_account">
                <?= $this->l2c($t->trans('create an account'), 'User', 'new') ?>
            </li>

        <?php endif; /* if logged in else */ ?>

    </ul>
</nav>

