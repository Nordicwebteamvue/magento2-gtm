<?php

namespace Kodbruket\Gtm\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\LayoutInterface;

class SetTransactionToDataLayerOnSuccessPageViewObserver implements ObserverInterface
{
    public function __construct(
        StoreManagerInterface $storeManager,
        LayoutInterface $layout
    ) {
        $this->layout = $layout;
        $this->storeManager = $storeManager;
    }

    public function execute(EventObserver $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        

        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }

        $block = $this->layout->getBlock('gtm_datalayer');

        if ($block) {
            $block->setOrderIds($orderIds);
        }
    }
}
