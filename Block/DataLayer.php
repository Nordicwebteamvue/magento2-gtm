<?php

namespace Kodbruket\Gtm\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Kodbruket\Gtm\Model\DataLayer;

class DataLayer extends Template
{
    public function __construct(Context $context, DataLayer $dataLayer, array $data = [])
    {
        $this->dataLayer = $dataLayer;

        parent::__construct($context, $data);
    }

    public function getDataLayer()
    {
        $orderIds = $this->getOrderIds();
        $this->dataLayer->setOrderIds($orderIds);

        $quoteId = $this->getQuoteId();
        $this->dataLayer->setQuoteId($quoteId);

        $this->dataLayer->set();

        return $this->dataLayer->get();
    }
}
