<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Resource_Listing_Other_Log
    extends Ess_M2ePro_Model_Resource_Log_Abstract
{
    //########################################

    public function _construct()
    {
        $this->_init('M2ePro/Listing_Other_Log', 'id');
    }

    public function getLastActionIdConfigKey()
    {
        return 'other_listings';
    }

    //########################################
}
