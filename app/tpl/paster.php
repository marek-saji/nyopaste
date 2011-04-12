<?php
/**
 * Paster (link to profile or plain text)
 *
 * hCard classes
 * @author m.augustynowicz
 *
 * @param array $row paste row (with [paster] and [paster_id]),
 *        enchanced with [Paster]
 * @param array $Paster_data user data
 * @param string $paster paster name
 */
if (@$row)
{
    $Paster = @$row['Paster'];
    $paster = @$row['paster'];
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
