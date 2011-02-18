<?php
/**
 * New paste form
 * @author m.augustynowicz
 *
 */

$title = $this->trans('add new paste');
$v->setTitle($title);

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
                <li class="field">
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
                <li class="field">
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
                        <p class="help">
                            <small>
                                <?=$t->trans('posting anonymously. you can <a href="%s">sign-in</a> or <a href="%s">create an account</a>.',
                                    $this->url2c('User','login'), $this->url2c('User','new')
                                )?>
                            </small>
                        </p>
                    <?php endif; ?>
                </li>

                <!-- author / source -->
                <li class="field">
                    <?php
                    $form->label('source', 'author / source');
                    ?>
                    <?php
                    $form->input('source');
                    ?>
                    <p class="help">
                        <small><?=$t->trans('if different than paster. can also be URL to original context.')?></small>
                    </p>
                </li>

            </ul>
        </fieldset>

        <fieldset class="verbose">
            <legend><?=$this->trans('the paste')?></legend>
            <!-- content: upload or paste -->
            <ul>

                <?php if (g()->debug->allowed()) : ?>
                <li class="field">
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
                <li class="field">
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
            <ul>
                <?php foreach ($this->_types as $idx => $type) : ?>
                    <?php
                    $name = (string) $type;
                    ?>
                    <li class="field">
                        <?php
                        $form->input('type_id', array(
                            'value' => $idx,
                            'label' => $t->trans($name)
                        ));
                        ?>
                        <fieldset>
                            <?php
                            $type->render();
                            ?>
                        </fieldset>
                    </li>
                <?php endforeach; ?>
            </ul>
        </fieldset>

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

