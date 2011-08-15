<?php
/**
 * Paster (link to profile or plain text)
 *
 * hCard classes
 *
 * only one of [row], [paster], [Paster] is required
 * @author m.augustynowicz
 *
 * @param array $row row data including [paster] or [Paster] keys
 * @param string $Paster [Paster] enchancement (including [DisplayName])
 * @param string $paster paster name (used as display name and identifier)
 */
extract(
    array_merge(
        array(
            'paster' => null,
            'Paster' => null,
            'row'    => null
        ),
        (array) $____local_variables
    ),
    EXTR_REFS|EXTR_PREFIX_INVALID, 'param'
);

if (@$row)
{
    $Paster = @$row['Paster'];
    $paster = @$row['paster'];
    if (!$Paster && $row['paster_id'])
    {
        $Paster['DisplayName'] = $Paster['login'] = $row['paster'];
    }
}

if (@$Paster['DisplayName'])
{
    $paster_html = $t->inc('user_link', array(
        'user'  => $Paster,
        'class' => 'author'
    ));
}
else
{
    if (!@$paster)
        $paster = $t->trans('anonymous');
    $paster_html = $f->tag(
        'span',
        array('class' => 'author vcard'),
        $f->tag(
            'i',
            array('class' => 'fn'),
            $paster
        )
    );
}

return $paster_html;

