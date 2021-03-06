<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Requirements_Checks_ExecutionTime extends Ess_M2ePro_Model_Requirements_Checks_Abstract
{
    const NICK = 'ExecutionTime';

    //########################################

    public function isMeet()
    {
        if ($this->getReal() <= 0) {
            return true;
        }

        return $this->getReal() >= $this->getMin();
    }

    //########################################

    public function getMin()
    {
        return $this->getReader()->getExecutionTimeData('min');
    }

    public function getReal()
    {
        return Mage::helper('M2ePro/Client')->getExecutionTime();
    }

    //########################################
}
