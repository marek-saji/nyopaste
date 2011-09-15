<?php
/**
 * New paste form
 * @author m.augustynowicz
 *
 * @param array $static_fields
 * @param bool $new_version
 * @param int $max_upload_size_mb
 * @param string $recaptcha_publickey
 * @param bool $use_captcha
 */

$title = $this->trans('add new paste');
$v->setTitle($title);

$v->addLess($this->file('new', 'less'));
$v->loadJsLib('ace');
$v->addJs($this->file('new', 'js'));

$form = g('Forms', array('paste', $this));
?>

<section>
    <header>
        <h2><?=$title?></h2>
    </header>

    <p>
        <?=$this->trans('Fields marked with asterisk are required.')?>
    </p>

    <div class="holoform" id="content">
        <?php
        $form->create();
        ?>

        <fieldset>
            <ul>

                <!-- paster -->
                <li class="paster field">
                    <?php if (@$static_fields['paster']) : ?>
                        <?php
                        $form->label('paster', $this->trans('paster '));
                        ?>
                        <strong><?=$static_fields['paster']?></strong>
                        <?=$this->trans('<small>(not you? %s)</small>', $this->l2c('sign out', 'User', 'logout'))?>
                    <?php else : ?>
                        <?php
                        $form->label('paster', 'paster (you)');
                        ?>
                        <?php
                        $form->input('paster');
                        ?>
                    <?php endif; ?>
                    <?php if (!g()->auth->loggedIn()) : ?>
                        <div class="help">
                            <p>
                                <?=$t->trans('Pasting anonymously.')?>
                            </p>
                            <p>
                                <?=$t->trans('You can <a href="%s">sign in</a> or <a href="%s">create an account</a> to be able to easily find your paste later and delete it, if you have to.',
                                    $this->url2c('User','login'), $this->url2c('User','new')
                                )?>
                            </p>
                        </div>
                    <?php endif; ?>
                </li>

                <!-- title -->
                <li class="title field">
                    <?php
                    $form->label('title', 'title', array(
                        'required' => !(bool)@$static_fields['title']
                    ));
                    ?>
                    <?php
                    $form->input('title');
                    ?>
                    <div class="help">
                        <p>
                            <?=$t->trans('You can call the paste with a filename (<abbr lang="la" title="exampli gratia">e.g.</abbr>&nbsp;<code>main.c</code>)')?>
                        </p>
                    </div>
                </li>
            </ul>

            <?php
            $less_important_options_json = htmlspecialchars(json_encode(array(
                'expand' => $this->trans('more options including tags, original author, source URL and <q>keep for&hellip;</q>'),
                'collapse' => $this->trans('it\'s too cluttered, hide these options'),
            )), ENT_QUOTES);
            ?>
            <ul class="less-important-options" data-less-important-options='<?=$less_important_options_json?>'>
                <!-- author -->
                <li class="author field">
                    <?php
                    $form->label('author', 'original author');
                    ?>
                    <?php
                    $form->input('author');
                    ?>
                    <div class="help">
                        <p>
                            <?=$t->trans('If different than paster.')?>
                        </p>
                    </div>
                </li>

                <!-- source -->
                <li class="source_url field">
                    <?php
                    $form->label('source_url', 'source URL');
                    ?>
                    <?php
                    $form->input('source_url');
                    ?>
                    <div class="help">
                        <p>
                            <?=$t->trans('URL address to original context.')?>
                        </p>
                    </div>
                </li>

                <!-- tags -->
                <li class="tags field">
                    <?php
                    $form->label('tags', 'tags');
                    ?>
                    <?php
                    $form->input('tags');
                    ?>
                    <div class="help">
                        <p>
                            <?php if (g()->auth->loggedIn()) : ?>
                                <?=$t->trans('List of comma-separated tags that will be helpful in searching this paste. You can also use them to group pastes at <a href="%s">your profile page</a>.', $this->url2c('User', '', array(g()->auth->ident())))?>
                            <?php else : ?>
                                <?=$t->trans('List of comma-separated tags that will be helpful in searching this paste. After you <a href="%s">sign in</a>, you will be able to use them to group pastes at your profile page.', $this->url2c('User', 'login'))?>
                            <?php endif; ?>
                        </p>
                    </div>
                </li>

                <!-- preffered URL -->
                <li class="preffered_url field">
                    <?php
                    $label = ((bool)@$static_fields['url'] ? '' : 'preffered ') . 'URL';
                    $form->label('url', $label, array('required'=>false));
                    ?>
                    <?php
                    $form->input('url');
                    ?>
                    <div class="help">
                        <p>
                            <?=$t->trans('End of URL address under which you\'d like paste will be visible at.')?>
                        </p>
                        <p>
                            <?=$t->trans('If none specified, will be generated from paste title.')?>
                        </p>
                        <p>
                            <?=$t->trans('Allowed characters are letters, numbers, dash (<q>-</q>) and underscore (<q>_</q>). Other characters will get converted to a dash.')?>
                        </p>
                    </div>
                </li>

                <?php if (g()->debug->allowed()) : ?>
                <li class="keep_for field">
                    @todo "keep for.."
                </li>
                <?php endif; ?>

            </ul>
        </fieldset>

        <fieldset class="verbose">
            <legend><?=$this->trans('the paste')?></legend>
            <!-- content: upload or paste -->
            <ul class="radio-optiongroups">

                <li class="file content field">
                    <?php
                    $form->input(
                        'content_type',
                        array(
                            'value' => 'content_file',
                            'label' => $this->trans('upload')
                        )
                    );
                    ?>
                    <?php
                    $form->input('content_file');
                    ?>
                    <small><?=$this->trans('max file size: %0.2f <abbr title="megabyte(s)">MB</abbr>', $max_upload_size_mb);?></small>
                </li>

                <li class="text content field">
                    <?php
                    $form->input(
                        'content_type',
                        array(
                            'value' => 'content_text',
                            'label' => $this->trans('type in')
                        )
                    );
                    ?>
                    <?php
                    $form->input(
                        'content_text',
                        array(
                            'class' => 'editor'
                        )
                    );
                    ?>
                    <noscript>
                        <p class="nojs advice description">
                            <?=$this->trans('With JavaScript available, this field would become a neat editor. Try enabling it, if you can.')?>
                        </p>
                    </noscript>
                </li>

            </ul>
        </fieldset>

        <fieldset class="verbose">
            <?php if (@$static_fields['type']) : ?>
                <legend><?=$this->trans('type: %s', $static_fields['type'])?></legend>
                <ul>
                    <?php
                    $type = reset($this->_types);
                    ?>
                    <fieldset class="type-specific <?=urlencode($type)?>">
                        <?php
                        $type = reset($this->_types);
                        $type->render();
                        ?>
                    </fieldset>
                </ul>
            <?php else : ?>
                <legend><?=$this->trans('type')?></legend>
                <!-- paste type and paste-type-specific options -->
                <ul class="radio-optiongroups">
                    <?php foreach ($this->_types as $idx => $type) : ?>
                        <li class="type field">
                            <?php
                            $form->input('type', array(
                                'value' => $idx,
                                'label' => $t->trans($type)
                            ));
                            ?>
                            <fieldset class="type-specific <?=urlencode($type)?>">
                                <?php
                                $type->render();
                                ?>
                            </fieldset>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </fieldset>


        <fieldset class="verbose">
            <legend><?=$this->trans('privacy')?></legend>
            <?php
            $less_important_options_json = htmlspecialchars(json_encode(array(
                //'expand TODO'   => $this->trans('show options including per-user and group permissions and encryption'),
                'expand'   => $this->trans('show options'),
                'collapse' => $this->trans('it\'s too cluttered, hide these options'),
                'excerpts' => array(
                    'public'     => $this->trans('Your paste will be listed in "pastes" section and indexed by search engines.'),
                    'not listed' => $this->trans('Your paste will not be listed in "pastes" section nor indexed by search engines. It will be accessible only by knowing it\'s address.'),
                    'private'    => $this->trans('Only users of your selection will be able to access your paste.'),
                ),
            )), ENT_QUOTES);
            ?>
            <ul class="less-important-options with-excerpt privacy" data-less-important-options='<?=$less_important_options_json?>'>
                <li class="privacy field">
                    <ul>
                        <li class="public privacy field">
                            <?php
                            $form->input(
                                'privacy',
                                array(
                                    'value' => 'public',
                                    'label' => 'public: list in <q>pastes</q>, allow to be searchable'
                                )
                            );
                            ?>
                        </li>
                        <li class="not-listed privacy field">
                            <?php
                            $form->input(
                                'privacy',
                                array(
                                    'value' => 'not listed',
                                    'label' => 'not listed: access only for people who know the address'
                                )
                            );
                            ?>
                        </li>
                        <li class="private privacy field">
                            <?php
                            $form->input(
                                'privacy',
                                array(
                                    'value' => 'private',
                                    'label' => 'allow only selected users'
                                )
                            );
                            ?>
                            <fieldset>
                                <ul>
                                    <li class="users privacy field">
                                        <?php
                                        $form->input('users');
                                        ?>
                                    </li>
                                </ul>
                            </fieldset>
                        </li>
                    </ul>
                </li>
                <?php if (g()->auth->loggedIn()) : ?>
                    <li class="field">
                        <?php
                        $form->input('publicly_versionable', array('label' => 'allow other users to create new versions of this paste'));
                        ?>
                        <div class="help">
                            <p>
                                <?=$this->trans('Normally, other users can create new versions of a paste, which will be listed on paste\'s page. If you do not want that, uncheck this option.')?>
                            </p>
                        </div>
                    </li>
                <?php endif; /* g()->auth->loggedIn( */ ?>
            </ul>
        </fieldset>


        <?php if ($use_captcha) : ?>
            <fieldset class="verbose">
                <legend><?=$this->trans('prove that you are human')?></legend>
                <ul>
                    <!-- CAPTCHA -->
                    <li class="captcha field">
                        <?=recaptcha_get_html($recaptcha_publickey);?>
                        <div class="help">
                            <p>
                                <?=$this->trans('To prove you are not an evil robot, complete this chalange.')?>
                            </p>
                            <p>
                                <?=$this->trans('After signing-in, this will not appear.')?>
                            </p>
                        </div>
                    </li>
                </ul>
            </fieldset>
        <?php endif; ?>


        <?php if (g()->auth->loggedIn()) : ?>
            <fieldset>
                <ul>
                    <li class="field">
                        <?php
                        $form->input(
                            'store_settings',
                            array(
                                'class' => 'store-settings',
                                'label' => $this->trans('<span class="nojs"><em>do not</em> create a paste, </span>just store settings as default for future pastes')
                            )
                        );
                        ?>
                        <div class="help">
                            <p>
                                <?=$this->trans('<em>Instead</em> of creating a new paste, save values as default for future pastes.')?>
                            </p>
                        </div>
                    </li>
                </ul>
            </fieldset>
        <?php else : ?>
            <p>
                <?=$t->trans('Don\'t forget to read <a href="%s" target="_blank" class="ext">terms of service</a>.', $t->url2c('Paste', '', array('TOS'))); ?>
            </p>
        <?php endif; ?>

        <?php
        $form->input('root_id');
        $form->input('parent_id');
        ?>

        <?php
        if ($new_version)
        {
            $submit = 'create new version';
        }
        else
        {
            $submit = 'create new paste';
        }
        if (!g()->auth->loggedIn())
        {
            $submit = 'accept terms of service and ' . $submit;
        }
        $data_values = json_encode(array(
            false => $submit,
            true  => $this->trans('store settings')
        ));
        $this->inc('Forms/buttons', array(
            'form'    => & $form,
            'submit'  => false,
            'buttons' => array(
                array(
                    'value'       => $submit,
                    'data-values' => $data_values
                )
            ),
            'cancel'  => g()->req->getReferer() ? 'cancel' : false,
        ));
        ?>
        <?php if (g()->debug->allowed()) : ?>
            <button>@todo preview</button>
            <?php endif; ?>

        <?php
        $form->end();
        ?>
    </div> <!-- .holoform -->

</section>

