<?php
/**
 * Paste with source code, add new form.
 * @author m.augustynowicz
 */
$form = g('Forms', array('paste', $this));

$v->addJs($this->file('type-new', 'js'));
?>

<ul>

    <li class="field">
        <?php
        $form->label('syntax', 'language');
        ?>
        <?php
        $form->input('syntax', array('values' => $syntax_values));
        ?>
        <div class="help">
            <p><?=$t->trans('Syntax paste as it\'s written in this language.')?></p>
            <p><?=$t->trans('If your language is not here, but it\'s kind of C-like, C is always a good option.')?></p>
        </div>
    </li>

</ul>

