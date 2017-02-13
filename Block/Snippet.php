<?php

namespace Kodbruket\Gtm\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Kodbruket\Gtm\Helper\Data;

class Snippet extends Template
{
    public function __construct(Context $context, Data $helper, array $data = []) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function getContainerId()
    {
        return $this->helper->getContainerId();
    }
}
