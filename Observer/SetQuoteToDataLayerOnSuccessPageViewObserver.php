<?php

namespace Kodbruket\Gtm\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\LayoutInterface;

class SetQuoteToDataLayerOnSuccessPageViewObserver implements ObserverInterface
{
    public function __construct(
        StoreManagerInterface $storeManager,
        LayoutInterface $layout,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->layout = $layout;
        $this->storeManager = $storeManager;
        $this->session = $checkoutSession;
    }

    public function execute(EventObserver $observer)
    {
        $session = $this->session;
        $quoteId = $session->getLastQuoteId();

        if (!$quoteId) {
            return;
        }

        $block = $this->layout->getBlock('gtm_datalayer');

        if ($block) {
            $block->setQuoteId($quoteId);
        }
    }
}
