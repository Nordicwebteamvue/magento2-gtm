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

    public function useCustomActionNames()
    {
        $useCustomActionNames = $this->scopeConfig->getValue(
            'google/gtm/use_custom_action_names',
            ScopeInterface::SCOPE_STORE
        );

        return (bool) $useCustomActionNames;
    }

    public function getFullActionNameForCategory()
    {
        $value = $this->scopeConfig->getValue(
            'google/gtm/full_action_name_for_category',
            ScopeInterface::SCOPE_STORE
        );

        return $value;
    }

    public function getFullActionNameForProduct()
    {
        $value = $this->scopeConfig->getValue(
            'google/gtm/full_action_name_for_product',
            ScopeInterface::SCOPE_STORE
        );

        return $value;
    }

    public function getFullActionNameForCheckout()
    {
        $value = $this->scopeConfig->getValue(
            'google/gtm/full_action_name_for_checkout',
            ScopeInterface::SCOPE_STORE
        );

        return $value;
    }
}
