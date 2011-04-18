<?php
/**
 * Definitions of enumerated fields
 *
 * WARNING: After creating model with FEnum field you *can't* add new values
 * to it. You'd have to remove all enum fields from models and then re-create
 * them (loosing all values). So think it through.
 * Or supply a patch fixing that. [;
 * @see FEnum
 * @author m.augustynowicz
 */

$conf['enum'] = array(

    'paste_privacy' => array(
        'public',
        'not listed',
        'protected',
        'private',
    ),

);


