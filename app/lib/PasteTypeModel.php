<?php
g()->load('DataSets', null);

/**
 * List of available paste types
 * @author m.augustynowicz
 */
class PasteTypeModel extends Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_addField(new FId('id', 2));
        $this->_addField(new FString('name', true));

        $this->_pk('id');

        $this->whiteListAll();
    }


    /**
     * Get key-value pairs suitable to use with Forms
     *
     * @author m.augustynowicz
     *
     * @return array
     */
    public function getValues()
    {
        // backup original whitelist
        $wl = & $this->_whitelist;
        unset($this->_whitelist);

        // fetch. filters etc. will be used
        $this->whiteList(array('id','name'));
        $this->exec();

        // flatten
        $values = array();
        foreach ($this->_array as & $row)
        {
            $values[$row['id']] = $row['name'];
        }
        unset($row);

        // restore original whitelist
        $this->_whitelist = & $wl;
        unset($wl);

        return $values;
    }

}

