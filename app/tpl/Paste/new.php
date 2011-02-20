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
        <?=$t->trans('Only fields marked with asterisk (<strong class="required">*</strong>) are required.')?>
    </p>

    <div class="holoform" id="content">
        <?php
        $form->create();
        ?>

        <fieldset>
            <ul>

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
                </li>

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

                <!-- author / source -->
                <li class="source field">
                    <?php
                    $form->label('source', 'author / source');
                    ?>
                    <?php
                    $form->input('source');
                    ?>
                    <div class="help">
                        <p>
                            <?=$t->trans('If different than paster. Can also be URL to original context.')?>
                        </p>
                    </div>
                </li>

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

