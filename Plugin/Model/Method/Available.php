<?php

namespace Techeniac\UpdatePayment\Plugin\Model\Method;

class Available
{
    protected $customerSession;
    protected $customerRepository;    
    protected $_objectManager;    
    protected $_storeManager;
    protected $_currencyFactory;
    protected $_cart;
    protected $_checkoutSession;
    protected $_helper;
    
    public function __construct(
     \Magento\Directory\Model\CurrencyFactory $currencyFactory, \Magento\Store\Model\StoreManagerInterface $storeManager,\Magento\Customer\Model\Session $customerSession, \Magento\Framework\ObjectManagerInterface $objectmanager, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,\Magento\Checkout\Model\Cart $cart, \Magento\Checkout\Model\Session $checkoutSession,\Techeniac\UpdatePayment\Helper\Data $_helper
    ) {
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->_objectManager = $objectmanager; 
        $this->_storeManager = $storeManager; 
        $this->_currencyFactory = $currencyFactory;
        $this->_cart = $cart;
        $this->_checkoutSession = $checkoutSession;
        $this->_helper = $_helper;
    }

    public function afterGetAvailableMethods($subject, $result)
    {


        if ($this->customerSession->isLoggedIn()){
            $customer=$this->customerRepository->getById($this->customerSession->getCustomerId());

            foreach ($result as $key=>$_result) {
                
                    if(in_array($customer->getGroupId(),$this->_helper->getCustomerGroupList())){
                        if($_result->getCode() == "banktransfer"){
                            $isAllowed =  true; 
                            if (!$isAllowed) {
                                unset($result[$key]);
                            }
                        }
                    }else{
                        if($_result->getCode() == "banktransfer"){
                            $isAllowed =  false; 
                            if (!$isAllowed) {
                                unset($result[$key]);
                            }
                        }
                    }
                
            }
        }

        return $result;
    }
    
}