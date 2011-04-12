<?php
echo $f->tag(
    'strong',
    array(
        'class' => $data ? '' : 'empty'
    ),
    $data ? $data : $this->trans('(not specified)')
);

return $this->inc('Forms/hidden', $____local_variables);

