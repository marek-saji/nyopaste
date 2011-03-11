<?php
/**
 * Form input label
 * @author m.augustynowicz
 *
 * @param string $input_id id of field 
 * @param string $label label text
 * @param bool $required whether field is required
 */
extract(
    array_merge(
        array(
            'input_id' => null,
            'label'    => null,
            'required' => false,
        ),
        (array) $____local_variables
    ),
    EXTR_REFS|EXTR_PREFIX_INVALID, 'param'
);

?>

<label for="<?=$input_id?>" class="<?=$required ? 'required' : ''?>">
    <span class="inside">
        <?=$this->trans($label)?>
    </span>
    <?php
    if($required)
    {
        echo $f->tag('small',
            array(
                'class' => 'required'
            ),
            $this->trans('(field required)')
        );
    }
    ?>
</label>

