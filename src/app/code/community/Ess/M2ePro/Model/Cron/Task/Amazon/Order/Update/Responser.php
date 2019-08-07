<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Cron_Task_Amazon_Order_Update_Responser
    extends Ess_M2ePro_Model_Amazon_Connector_Orders_Update_ItemsResponser
{
    /** @var Ess_M2ePro_Model_Order $_order */
    protected $_order = array();

    //########################################

    public function __construct(array $params, Ess_M2ePro_Model_Connector_Connection_Response $response)
    {
        $this->_order = Mage::helper('M2ePro/Component_Amazon')->getObject('Order', $params['order']['order_id']);
        parent::__construct($params, $response);
    }

    //########################################

    public function failDetected($messageText)
    {
        parent::failDetected($messageText);

        $this->_order->getLog()->setInitiator(Ess_M2ePro_Helper_Data::INITIATOR_EXTENSION);
        $this->_order->addErrorLog('Amazon Order status was not updated. Reason: %msg%', array('msg' => $messageText));
    }

    //########################################

    protected function processResponseData()
    {
        Mage::getResourceModel('M2ePro/Order_Change')->deleteByIds(array($this->_params['order']['change_id']));

        $responseData = $this->getResponse()->getData();

        // Check separate messages
        //----------------------
        $isFailed = false;

        /** @var Ess_M2ePro_Model_Connector_Connection_Response_Message_Set $messagesSet */
        $messagesSet = Mage::getModel('M2ePro/Connector_Connection_Response_Message_Set');
        $messagesSet->init($responseData['messages']);

        foreach ($messagesSet->getEntities() as $message) {
            $this->_order->getLog()->setInitiator(Ess_M2ePro_Helper_Data::INITIATOR_EXTENSION);
            if ($message->isError()) {
                $isFailed = true;

                $this->_order->addErrorLog(
                    'Amazon Order status was not updated. Reason: %msg%',
                    array('msg' => $message->getText())
                );
            } else {
                $this->_order->addWarningLog($message->getText());
            }
        }

        //----------------------

        if ($isFailed) {
            return;
        }

        //----------------------
        $this->_order->getLog()->setInitiator(Ess_M2ePro_Helper_Data::INITIATOR_EXTENSION);
        $this->_order->addSuccessLog('Amazon Order status was updated to Shipped.');

        if (empty($this->_params['order']['tracking_number']) || empty($this->_params['order']['carrier_name'])) {
            return;
        }

        $this->_order->addSuccessLog(
            'Tracking number "%num%" for "%code%" has been sent to Amazon.',
            array(
                '!num' => $this->_params['order']['tracking_number'],
                'code' => $this->_params['order']['carrier_name']
            )
        );
        //----------------------
    }

    //########################################
}
