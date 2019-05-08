<?php
namespace Techeniac\UpdatePayment\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }
    /**
     * Get Customer group selected list
     */
    public function getCustomerGroupList() {
        $list = $this->scopeConfig->getValue("payment/banktransfer/allowed_customer_group",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $list !== null ? explode(',', $list) : [];
    }
}