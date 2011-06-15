<?php
/**
 * Editing user's data
 * @author m.augustynowicz
 *
 * @param array $row one row data
 */

$own_profile = g()->auth->id() == $row['id'];

if ($own_profile)
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
                    <li class="field">
                        <?php
                        echo $this->inc('user_link', array(
                            'user'   => & $row,
                            'text'   => false,
                            'avatar' => 128
                        ));
                        ?>
                        <p>
                            <?php
                            if ($own_profile)
                            {
                                $text = 'To change avatar, register your e-mail (%s) at <a href="http://%s.gravatar.com">Gravatar.com</a>.';
                            }
                            else
                            {
                                $text = 'Avatars powered by <a href="http://%2$s.gravatar.com">Gravatar.com</a>.';
                            }
                            echo $this->trans($text, $row['email'], g()->lang->get());
                            ?>
                        </p>
                    </li>
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
                            <p>
                                <?=$this->trans('But we will use it to display your <a href="http://gravatar.com/">Gravatar</a>. <code class="emoticon">:)</code>')?>
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

            <fieldset class="verbose">
                <legend><?=$this->trans('Change your password')?></legend>
                <ul>
                    <li class="field">
                        <?php
                        $form->label('old_passwd', 'current password <small>(leave empty not to change)</small>');
                        ?>
                        <?php
                        $form->input('old_passwd', array('autocomplete'=>false));
                        ?>
                    </li>
                    <li class="field">
                        <?php
                        $form->label('new_passwd', 'new password');
                        ?>
                        <?php
                        $form->input('new_passwd', array('autocomplete'=>false));
                        ?>
                        <p class="help">
                            <?=$this->trans('Enter your new password. Twice, just to be sure you didn\'t make a typo.')?>
                        </p>
                    </li>
                </ul>
            </fieldset>

            <?php
            $t->inc('Forms/buttons', array(
                'form'   => & $form,
                'submit' => 'save changes',
                'cancel' => 'cancel'
            ));
            ?>
        <?php
        $form->end();
        ?>
    </div> <!-- .holoform -->

</section>

