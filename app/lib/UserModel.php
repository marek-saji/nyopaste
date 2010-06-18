<?php
g()->load('DataSets', null);

/**
 * User
 */
class UserModel extends Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_addField(new FId('id'));
        $this->_addField(new FString('login', true, null, 4, 32)); // UNIQUE
        $this->_addField(new FEmail('email', false)); // UNIQUE
        $this->_addField(new FPassword('passwd', 3, 128));

        // for user's statuses (STATUS_*) see main conf
        $this->_addField(new FInt('status', 2, true,
                                  (string) STATUS_ACTIVE));
        // for user's types (USER_TYPE_*) see conf.users
        $this->_addField(new FInt('type', 4, true,
                                  (string) USER_TYPE_AUTHORIZED));

        $this->_addField(new FTimestamp('last_edit'));

        // metadata
        $this->_addField(new FHTTP('website', false, null, null, 1024));
        $this->_addField(new FMultilineString('about_me', false, null, 0, 1048576));

        $this->_pk('id');
        $this->whiteListAll();
    }
}

