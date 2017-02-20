<?php

namespace Kodbruket\Gtm\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\LayoutInterface;

class SaveQuoteIdOnCheckoutQuoteDestroy implements ObserverInterface
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
        $quoteId = $observer->getEvent()->getQuote()->getId();
        $session = $this->session;

        if (!$quoteId) {
            return;
        }

        if ($session) {
            $session->setLastQuoteId($quoteId)->save();
        }
    }
}
