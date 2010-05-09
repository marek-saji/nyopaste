<?php
g()->load('DataSets', null);

/**
 * @author m.jutkiewicz
 *
 */
class UserModel extends Model
{
    public $image_files = array(
        //please define sizes from smaller to bigger
        //it is necessary to correct working of user_link template
        'sizes' => array('48x48', '128x128'),
        'store_original' => false,
        'stripes' => false,
    	'format' => 'png',
    );

    public function __construct()
    {
        parent::__construct();

        $this->__addField(new FId('id'));
        $this->__addField(new FString('login', true, null, 4, 32)); // UNIQUE
        $this->__addField(new FEmail('email', false)); // UNIQUE
        $this->__addField(new FPassword('passwd', 3, 128));
        $this->__addField(new FImageFile('photo_hash', false, 'ImagesUpload'));

        $this->__addField(new FDate('birth_date'));
        $this->__addField(new FString('country', false, null, 0, 2));
        $this->__addField(new FLocationCoords('location_coords'));
        $this->__addField(new FString('description', false, null, 0, 512));
        $this->__addField(new FBool('notices'));
        //user's statuses configuration can be found in conf.php
        $this->__addField(new FInt('status', 2, true, '0'));    //1 - activated, -1 - deleted by user, -2 - deleted by someone else
        $this->__addField(new FInt('type', 4, true, '1'));    //0 - guest, 1 - registered, -2 - mod, -1 - admin
        $this->__addField(new FTimestamp('creation_date'));
        $this->__addField(new FTimestamp('last_correct_login'));
        $this->__addField(new FInt('incorrect_login_count', 4, true, '0'));
        $this->__addField(new FString('password_reset_link', false, null, 4, 64));

        $this->relate('Objects', 'Object', '1toN', 'id', 'owner');
        $this->relate('Comments', 'Comment', '1toN', 'id', 'users_id');
        $this->relate('Images', 'ImagesUpload', '1to1', 'photo_hash', 'id');
        $this->__pk('id');
        $this->whiteListAll();
    }
}
