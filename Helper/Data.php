<?php

namespace Kodbruket\Gtm\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    public function getContainerId()
    {
        $containerId = $this->scopeConfig->getValue(
            'google/gtm/container_id',
            ScopeInterface::SCOPE_STORE
        );

        return $containerId;
    }

    public function useQuote()
    {
    	$useQuote = $this->scopeConfig->getValue(
            'google/gtm/use_quote_for_transactions',
            ScopeInterface::SCOPE_STORE
        );

        return (bool) $useQuote;
    }
}
