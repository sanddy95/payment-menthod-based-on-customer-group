<?php
namespace Techeniac\UpdatePayment\Observer;
 
use Magento\Framework\Event\ObserverInterface;
 
class OrderObserver implements ObserverInterface
{
	protected $_invoiceService;
    protected $_transactionFactory;

    public function __construct(
      \Magento\Sales\Model\Service\InvoiceService $invoiceService,
      \Magento\Framework\DB\TransactionFactory $transactionFactory
    ) {
       $this->_invoiceService = $invoiceService;
       $this->_transactionFactory = $transactionFactory;
    }

	public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$statuscode = $observer->getEvent()->getOrder()->getStatus();
        $order = $observer->getEvent()->getOrder();

        try {
            if(!$order->canInvoice()) {
                return null;
            }
            if(!$order->getState() == 'new') {
                return null;
            }
           	if($order->getPayment()->getMethod() == "banktransfer"){
           		if($statuscode == "pending"){
					$order->setStatus("processing");
					$order->save();
				}
           		$invoice = $this->_invoiceService->prepareInvoice($order);
	            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
	            $invoice->register();

	            $transaction = $this->_transactionFactory->create()
	              ->addObject($invoice)
	              ->addObject($invoice->getOrder());

	            $transaction->save();
           	}

        } catch (\Exception $e) {
            $order->addStatusHistoryComment('Exception message: '.$e->getMessage(), false);
            $order->save();
            return null;
        }
    }
}