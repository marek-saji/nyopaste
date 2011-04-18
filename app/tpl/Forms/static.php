<?php
/**
 * Display text instead of input
 * @author m.augustynowicz
 */
if (sizeof($field_objects) == 1)
{
    $field = (object) reset($field_objects);
}
else
{
    $field = (object) null;
}

switch (true)
{
    case 'FBool' === get_class($field) :
        $____local_variables['attrs']['disabled'] = 'disabled';
        return $this->inc('Forms/FBool', $____local_variables);
        break;
}

echo $f->tag(
    'strong',
    array(
        'class' => $data ? '' : 'empty'
    ),
    $data ? $data : $this->trans('(not specified)')
);

return $this->inc('Forms/hidden', $____local_variables);

