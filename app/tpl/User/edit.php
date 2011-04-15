<?php
/**
 * Editing user's data
 * @author m.augustynowicz
 *
 * @param array $row one row data
 */

if (g()->auth->id() == $row['id'])
    $title = $t->trans('edit your profile');
else
    $title = $t->trans('edit <em>%s\'s</em> profile', $row['DisplayName'] );
$v->setTitle($title);

$form = g('Forms', array('edit', $this));
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

    <div class="holoform">
        <?php
        $form->create();
        ?>
            <fieldset>
                <ul>
                    <!-- e-mail -->
                    <li class="email field">
                        <?php
                        $form->label('email', 'e-mail');
                        ?>
                        <?php
                        $form->input('email');
                        ?>
                        <div class="help">
                            <p>
                                <?=$this->trans('Don\'t worry, your e-mail address will not be publicly available, nor used in any evil way.')?>
                            </p>
                        </div>
                    </li>
                    <!-- website -->
                    <li class="website field">
                        <?php
                        $form->label('website', 'website');
                        ?>
                        <?php
                        $form->input('website');
                        ?>
                        <div class="help">
                            <p>
                                <?=$this->trans('Your blog, twitter, github profile. Any URL with more information about you.')?>
                            </p>
                        </div>
                    </li>
                    <!-- about me -->
                    <li class="about_me field">
                        <?php
                        $form->label('about_me', 'something about you');
                        ?>
                        <?php
                        $form->input('about_me', array(
                            'class' => 'autoexpandable'
                        ));
                        ?>
                        <div class="help">
                            <p>
                                <?=$this->trans('Tell other people something about yourself.')?>
                            </p>
                            <p>
                                <?=$this->trans('You can use <a href="http://daringfireball.net/projects/markdown/syntax">Markdown</a> here. With some <a href="http://michelf.com/projects/php-markdown/extra/">extra syntax</a>.')?>
                            </p>
                        </div>
                    </li>
                </ul>
            </fieldset>
            <?php
            $t->inc('Forms/buttons', array(
                'form' => & $form,
                'submit' => 'update profile info',
                'cancel' => 'cancel'
            ));
            ?>
        <?php
        $form->end();
        ?>
    </div> <!-- .holoform -->

</section>

