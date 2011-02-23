<?php
/**
 * New paste form
 * @author m.augustynowicz
 *
 */

$title = $this->trans('add new paste');
$v->setTitle($title);

$v->addLess($this->file('new', 'less'));
$v->addJs($this->file('new', 'js'));

$form = g('Forms', array('paste', $this));
?>

<section>
    <header>
        <h2><?=$title?></h2>
    </header>

    <p>
        <?=$t->trans('Fields marked with asterisk (<strong class="required">*</strong>) are required.')?>
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
                        <strong><?=$static_fields['paster']?></strong>
                    <?php else : ?>
                        <?php
                        $form->label('paster', 'paster');
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
                                <?=$t->trans('You can <a href="%s">sign-in</a> or <a href="%s">create an account</a> to be able to easily find your paste later and delete delete it, if you have to.',
                                    $this->url2c('User','login'), $this->url2c('User','new')
                                )?>
                            </p>
                        </div>
                    <?php endif; ?>
                </li>

                <!-- title -->
                <li class="title field">
                    <?php
                    $form->label('title', 'title');
                    ?>
                    <?php
                    $form->input('title');
                    ?>
                    <?php if (g()->debug->allowed()) : ?>
                        @todo set, when creating new version
                    <?php endif; ?>
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
                                <?=$t->trans('List of tags that will be helpful in searching this paste. After you <a href="%s">sign in</a>, you will be able to use them to group pastes at your profile page.', $this->url2c('User', 'login'))?>
                            <?php else : ?>
                                <?=$t->trans('Lista etykiet, które pozwolą na łatwiejsze wyszukanie wpisu. Możesz ich również użyć do grupowania wpisów na <a href="%s">swoim profilu</a>.', $this->url2c('User', '', array(g()->auth->id())))?>
                            <?php endif; ?>
                        </p>
                    </div>
                </li>

                <!-- preffered URL -->
                <li class="preffered_url field">
                    <?php
                    $form->label('url', 'preffered URL', array('required'=>false));
                    ?>
                    <?php
                    $form->input('url');
                    ?>
                    <div class="help">
                        <p>
                            <?=$t->trans('URL address under which you\'d like your paste to be posted.')?>
                        </p>
                        <p>
                            <?=$t->trans('If none specified, will be generated from paste title.')?>
                        </p>
                    </div>
                </li>

                <?php if (g()->debug->allowed()) : ?>
                <li class="keep_for field">
                    @todo "keep for..", preffered_url
                </li>
                <?php endif; ?>

            </ul>
        </fieldset>

        <fieldset class="verbose">
            <legend><?=$this->trans('the paste')?></legend>
            <!-- content: upload or paste -->
            <ul class="radio-optiongroups">

                <?php if (g()->debug->allowed()) : ?>
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
                </li>
                <?php endif; ?>

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
                            'class' => 'autoexpandable'
                        )
                    );
                    ?>
                </li>

            </ul>
        </fieldset>

        <fieldset class="verbose">
            <legend><?=$this->trans('type')?></legend>
            <!-- paste type and paste-type-specific options -->
            <ul class="radio-optiongroups">
                <?php foreach ($this->_types as $idx => $type) : ?>
                    <?php
                    $name = (string) $type;
                    ?>
                    <li class="type field">
                        <?php
                        $form->input('type_id', array(
                            'value' => $idx,
                            'label' => $t->trans($name)
                        ));
                        ?>
                        <fieldset class="type-specific <?=urlencode($name)?>">
                            <?php
                            $type->render();
                            ?>
                        </fieldset>
                    </li>
                <?php endforeach; ?>
            </ul>
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

