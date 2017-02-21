# Google Tag Manager for Magento 2

## Installation

The easiest way to install the extension is to use [Composer](https://getcomposer.org), just run the following commands:

`$ composer require kodbruket/magento2-gtm`

`$ bin/magento module:enable Kodbruket_Gtm`

`$ bin/magento setup:upgrade`

## Configuration

Enable the module under _Stores_ / _Configuration_ / _Sales_ / _Google API_. Enter your container ID from [Google Tag Manager](https://www.google.com/analytics/tag-manager/) and specify whether to use quote or order when tracking the transaction on the success page. When the module is set to track by quote it will try to set `transactionID` to the reserved order ID from the quote. If no order ID has been reserved it will fall back to the entity ID of the quote.

That's all there is to it.

## Authors

[Andreas Karlsson](https://github.com/indiebytes)
