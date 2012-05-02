<?php
g()->load('DataSets', null);

/**
 * Profile boxes model
 * @author m.augustynowicz
 */
class BoxModel extends Model
{
    /**
     * Add fields, relations and set primary keys
     * @author m.augustynowicz
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // fields

        $this->_addField(new FId('id'));
        $this->_addField(new FEnum('profile_type', 'profile_type', true));
        $this->_addField(new FForeignId('profile_id', true, 'User'));
        $this->_addField(new FInt('order'));
        $this->_addField(new FString('title', false, '', 0, 256));
        $this->_addField(new FMultilineString('query'));
        $this->_addField(new FInt('limit', 2, true, null, 1, 25))
            ->mess(array(
                'min_val_excided' => 'Let\'s show at least one paste&hellip;',
                'max_val_excided' => 'We can show at most twenty five latest pastes.',
                'invalid'         => 'That\'s not a number&hellip;'
            ))
        ;
        $this->_addField(new FBool('list_paster', true, true));
        $this->_addField(new FTimestamp('removed'));

        // relations

        $this->relate('User', 'User', 'Nto1');


        $this->_pk('id');

        $this->whiteListAll();
    }
}

