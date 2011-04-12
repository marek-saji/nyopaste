<?php
/**
 * New paste form
 * @author m.augustynowicz
 *
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
                        $form->label('paster', $this->trans('paster <small>(not you? %s)</small>', $this->l2c('sign out', 'User', 'logout')));
                        ?>
                        <strong><?=$static_fields['paster']?></strong>
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
                                <?=$t->trans('You can <a href="%s">sign-in</a> or <a href="%s">create an account</a> to be able to easily find your paste later and delete it, if you have to.',
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
                                <?=$t->trans('List of tags that will be helpful in searching this paste. You can also use them to group pastes at <a href="%s">your profile page</a>.', $this->url2c('User', '', array(g()->auth->ident())))?>
                            <?php else : ?>
                                <?=$t->trans('List of tags that will be helpful in searching this paste. After you <a href="%s">sign in</a>, you will be able to use them to group pastes at your profile page.', $this->url2c('User', 'login'))?>
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


        <?php if (g()->debug->allowed()) : ?>
        <fieldset class="verbose">
            <legend><?=$this->trans('privacy')?></legend>
            <?php
            $less_important_options_json = htmlspecialchars(json_encode(array(
                'expand' => $this->trans('show options including per-user and group permissions and encryption'),
                'collapse' => $this->trans('it\'s too cluttered, hide these options'),
            )), ENT_QUOTES);
            ?>
            <ul class="less-important-options" data-less-important-options='<?=$less_important_options_json?>'>
                <li>@todo</li>
            </ul>
        </fieldset>
        <?php endif; ?>


        <?php if (!g()->auth->loggedIn()) : ?>
            <p>
                <?=$t->trans('Don\'t forget to read <a href="%s" target="_blank" class="ext">terms of use</a>.', $t->url2c('Paste', '', array('TOS'))); ?>
            </p>
        <?php endif; ?>

        <?php
        $form->input('root_id');
        $form->input('parent_id');
        ?>

        <?php
        $this->inc('Forms/buttons', array(
            'form' => & $form,
            'submit' => 'accept terms of use and add',
            'cancel' => g()->req->getReferer() ? 'cancel' : false,
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

