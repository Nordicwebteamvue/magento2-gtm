<?php

namespace Kodbruket\Gtm\Model;

use Magento\Framework\DataObject;
use Magento\Framework\App\Action\Context; 
use Magento\Framework\Registry;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Quote\Model\QuoteFactory as QuoteFactory;
use Kodbruket\Gtm\Helper\Data;

class DataLayer extends DataObject
{
    public $dataLayer = [];

    public function __construct(Context $context, Registry $registry, CustomerSession $customerSession, CheckoutSession $checkoutSession, OrderCollectionFactory $orderCollectionFactory, QuoteFactory $quoteFactory, Data $helper)
    {
        $this->context = $context;
        $this->registry = $registry;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->quoteFactory = $quoteFactory;
        $this->helper = $helper;

        $this->action = $this->context->getRequest()->getFullActionName();

        if ($this->helper->useCustomActionNames()) {
            $fullActionNameForProduct = explode(',', $this->helper->getFullActionNameForProduct());
            $fullActionNameForCategory = explode(',', $this->helper->getFullActionNameForCategory());
            $fullActionNameForCheckout = explode(',', $this->helper->getFullActionNameForCheckout());
        } else {
            $fullActionNameForProduct = ['catalog_product_view'];
            $fullActionNameForCategory = ['catalog_category_view'];
            $fullActionNameForCheckout = ['checkout_index_index'];
        }

        if (in_array($this->action, $fullActionNameForCategory)) {
            $this->setCategoryData();
        }

        if (in_array($this->action, $fullActionNameForProduct)) {
            $this->setProductData();
        }

        if (in_array($this->action, $fullActionNameForCheckout)) {
            $this->setCartData();
        }

        if ($this->action == 'cms_noroute_index') {
            $this->set404Data();
        }

        $this->setCustomerData();
    }

    public function get()
    {
        return $this->dataLayer;
    }

    public function set()
    {
        if ($this->getOrderIds() || $this->getQuoteId()) {
            $this->setTransactionData();
        }
    }

    protected function setCategoryData()
    {
        $category = $this->registry->registry('current_category');

        if ($category) {
            $data = [
                'event' => 'category',
                'category' => [
                    'id' => $category->getId(),
                    'name' => $category->getName()
                ]
            ];

            $this->dataLayer[] = $data;
        }
    }

    protected function setProductData()
    {
        $product = $this->registry->registry('current_product');

        if ($product) {
            $data = [
                'event' => 'product',
                'product' => [
                    'id' => $product->getId(),
                    'sku' => $product->getSku(),
                    'name' => $product->getName()
                ]
            ];

            $this->dataLayer[] = $data;
        }
    }

    protected function setCartData()
    {

        $data = [];
        $data['event'] = 'cart';

        $quote = $this->checkoutSession->getQuote();

        $data['cart']['id'] = $quote->getId();
        $data['cart']['hasItems'] = (bool) $quote->getItemsCount();

        if ($data['cart']['hasItems']) {
            $items = [];

            foreach ($quote->getAllVisibleItems() as $item) {
                $data['cart']['items'][] = [
                    'sku' => $item->getSku(),
                    'name' => $item->getName(),
                    'price' => $item->getPrice(),
                    'quantity' => $item->getQty(),
                    'currency' => $quote->getBaseCurrencyCode()
                ];
            }

            $data['cart']['total'] = $quote->getBaseGrandTotal();
            $data['cart']['currency'] = $quote->getBaseCurrencyCode();
            $data['cart']['hasCoupons'] = (bool) $quote->getCouponCode();

            if ($data['cart']['hasCoupons']) {
                $data['cart']['couponCode'] = $quote->getCouponCode();
            }
        }

        $this->dataLayer[] = $data;
    }

    protected function setTransactionData()
    {
        if ($this->helper->useQuote()) {
            if ($this->getQuoteId()) {
                $quoteId = $this->getQuoteId();
            } else if ($this->getOrderIds()) {
                $orderCollection = $this->orderCollectionFactory->create();
                $orderCollection->addFieldToFilter('entity_id', ['in' => $this->getOrderIds()]);
                $order = $orderCollection->getFirstItem();
                $quoteId = $order->getQuoteId();
            }
            $collection = [];
            $collection[] = $this->quoteFactory->create()->load($quoteId);
        } else if ($this->getOrderIds()) {
            $collection = $this->orderCollectionFactory->create();
            $collection->addFieldToFilter('entity_id', ['in' => $this->getOrderIds()]);
        } else {
            return;
        }

        $data = [];
        $data['event'] = 'transaction';

        $transactions = [];

        foreach ($collection as $entity) {

            $transactionProducts = [];

            foreach ($entity->getAllVisibleItems() as $item) {
                $product = [
                    'sku' => $item->getSku(),
                    'name' => $item->getName(),
                    'price' => $item->getPrice()
                ];

                if ($this->helper->useQuote()) {
                    $product['quantity'] = $item->getQty();
                } else {
                    $product['quantity'] = $item->getQtyOrdered();
                }

                $transactionProducts[] = $product;
            }

            $transactionShipping = 0;

            if ($this->helper->useQuote()) {
                $transactionId = $entity->getReservedOrderId();
                $transactionShipping = $entity->getShippingAddress()->getBaseShippingAmount();
                $transactionTax = $entity->getShippingAddress()->getBaseTaxAmount();
            } else {
                $transactionId = $entity->getIncrementId();
                $transactionShipping = $entity->getBaseShippingAmount();
                $transactionTax = $entity->getBaseTaxAmount();
            }

            if (!$transactionId) {
                $transactionId = $entity->getId();
            }

            $data['transactionId'] = $transactionId;
            $data['transactionAffiliation'] = '';
            $data['transactionTotal'] = $entity->getBaseGrandTotal();
            $data['transactionShipping'] = $transactionShipping;
            $data['transactionTax'] = $transactionTax;
            $data['transactionProducts'] = $transactionProducts;

            $this->dataLayer[] = $data;
        }
    }

    protected function set404Data()
    {
        $this->dataLayer[] = ['event' => 'pageNotFound'];
    }

    protected function setCustomerData()
    {
        $data['customer']['isLoggedIn'] = $this->customerSession->isLoggedIn();

        if ($data['customer']['isLoggedIn']) {
            $data['customer']['id'] = $this->customerSession->getCustomerId();
            $data['customer']['groupId'] = $this->customerSession->getCustomerGroupId();
        }

        $this->dataLayer[] = $data;
    }
}
