<?php
g()->load('DataSets', null);

/**
 * @author m.augustynowicz
 *
 */
class UserModel extends Model
{
    public function __construct()
    {
        parent::__construct();

        $this->__addField(new FId('id'));
        $this->__addField(new FString('login', true, null, 4, 32)); // UNIQUE
        $this->__addField(new FEmail('email', false)); // UNIQUE
        $this->__addField(new FPassword('passwd', 3, 128));

        //user's statuses configuration can be found in conf.php
        $this->__addField(new FInt('status', 2, true, '0'));    //1 - activated, -1 - deleted by user, -2 - deleted by someone else
        $this->__addField(new FInt('type', 4, true, '1'));    //0 - guest, 1 - registered, -2 - mod, -1 - admin

        $this->__pk('id');
        $this->whiteListAll();
    }
}
