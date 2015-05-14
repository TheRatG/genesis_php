Overview
===========

Client Library for communication with Genesis Payment Processing Gateway. Its highly recommended to checkout "Genesis Payment Gateway API Documentation" first, in order to get an overview of Genesis's Payment Gateway API and functionality.

Requirements
------------

* PHP version 5.3.2 or newer
* PHP Extensions:
    * [BCMath](https://php.net/bcmath)
    * [CURL](https://php.net/curl) (if you choose to use it)
    * [XMLWriter](https://php.net/xmlwriter)
* Composer (optional)

Installation
------------

* clone this repo / download the archive
````bash
git clone http://github.com/E-ComProcessing/genesis_php genesis_php && cd genesis_php
````

Make a transaction
------------------

````php
<?php
require 'vendor/autoload.php';

// Use the Genesis Namespace
use \Genesis;

// Load the pre-configured ini file...
Config::loadSettings('/path/to/config.ini');

// ...OR, optionally, you can set the credentials manually
Config::setUsername('<enter_your_username>');
Config::setPassword('<enter_your_password>');
Config::setEnvironment('test|live');
Config::setToken('<enter_your_token>');

// Create a new Genesis instance with desired API request
$genesis = new \Genesis\Genesis('Financial\Cards\Authorize');

// Set request parameters
$genesis
    ->request()
        ->setTransactionId('43671')
        ->setUsage('40208 concert tickets')
        ->setRemoteIp('245.253.2.12')
        ->setAmount('50')
        ->setCurrency('USD')
        // Customer Details
        ->setCustomerEmail('emil@example.com')
        ->setCustomerPhone('1987987987987')
        // Credit Card Details
        ->setCardHolder('Emil Example')
        ->setCardNumber('4200000000000000')
        ->setExpirationMonth('01')
        ->setExpirationYear('2020')
        ->setCvv('123')
        // Billing/Invoice Details
        ->setBillingFirstName('Travis')
        ->setBillingLastName('Pastrana')
        ->setBillingAddress1('Muster Str. 12')
        ->setBillingZipCode('10178')
        ->setBillingCity('Los Angeles')
        ->setBillingState('CA')
        ->setBillingCountry('US');

try {
    // Send the request
    $genesis->execute();
    
    // Successfully completed the transaction - display the gateway unique id
    echo $genesis->response()->getResponseObject()->unique_id;
}
// Log/handle API errors
// Example: Declined transaction, Invalid data, Invalid configuration
catch (\Genesis\Exceptions\ErrorAPI $api) {
    echo $genesis->response()->getResponseObject()->technical_message;
}
// Log/handle invalid parameters
// Example: Empty (required) parameter
catch (\Genesis\Exceptions\ErrorParameter $parameter) {
    error_log($parameter->getMessage());
}
// Log/handle network (transport) errors
// Example: SSL verification errors, Timeouts
catch (\Genesis\Exceptions\ErrorNetwork $network) {
    error_log($network->getMessage());
}

?>
````

Note: the file ```vendor/autoload.php``` is located inside the directory where you cloned the repo and it is auto-generated by [Composer]. If the file is missing, just run ```php composer.phar update``` inside the root directory


Notifications
-------------

When using an Asynchronous workflow, you need to parse the incoming extension in order to ensure its authenticity and verify it against Genesis server.

Example:

```php
<?php
require 'vendor/autoload.php';

// Use the Genesis Namespace
use \Genesis;

try {
    $notification = new API\Notification($_POST);

    // Reconciliation is generally optional, but
    // its a recommended practice to ensure
    // that you have the latest information
    $notification->initReconciliation();
    
    // Application Logic
    // ...
    // for example, process the transaction status
    // $status = $notification->getReconciliationObject()->status;
    
    // Respond to Genesis
    $notification->renderResponse();
} 
catch (\Exception $e) {
    error_log($e->getMessage());
}

?>
```

Endpoints
---------

The current versions supports two separate endpoints: ```eMerchantPay``` and ```E-ComProcessing```

By default, the selected endpoint is: ```E-ComProcessing```

You can choose your endpoint the following way:

```php
\Genesis\Config::setEndpoint('e-comprocessing');
```

Request types
-------------

You can use the following request types to initialize the Genesis interface:

```text
// Generic transaction operations
Financial\Capture
Financial\Refund
Financial\Void

// Transactions with Alternative Payment Methods
Financial\Alternatives\CashU
Financial\Alternatives\PPRO
Financial\Alternatives\Paysafecard
Financial\Alternatives\Sofort
Financial\Alternatives\SofortiDEAL

// Transactions with BankTransfers
Financial\BankTransfers\PayByVoucher

// Transactions with Credit Cards
Financial\Cards\Authorize
Financial\Cards\Authorize3D
Financial\Cards\Credit
Financial\Cards\Payout
Financial\Cards\Recurring
Financial\Cards\Recurring\InitRecurringSale
Financial\Cards\Recurring\InitRecurringSale3D
Financial\Cards\Recurring\RecurringSale
Financial\Cards\Sale
Financial\Cards\Sale3D

// Transactions with Electronic Wallets
Financial\Wallets\eZeeWallet

// Generic (Non-Financial) requests
NonFinancial\AVS
NonFinancial\AccountVerification
NonFinancial\Blacklist

// Fraud-related requests
NonFinancial\Fraud\Chargeback\DateRange
NonFinancial\Fraud\Chargeback\Transaction
NonFinancial\Fraud\Retrieval\DateRange
NonFinancial\Fraud\Retrieval\Transaction

// Reconcile requests
NonFinancial\Reconcile\DateRange
NonFinancial\Reconcile\Transaction

// Web Payment Form (Checkout) requests
WPF\Create
WPF\Reconcile
```


More information about each one of the request types can be found in the Genesis API Documentation and the Wiki

Running Specs
--------------

The following steps are optional, however its required to run specs at least once, to ensure that everything is working as intended

* install [Composer] (if you don't have it already)
````bash
curl -sS https://getcomposer.org/installer | php
````

* fetch all required packages
````bash
php composer.phar install
````

* run phpspec
````bash
vendor/bin/phpspec run
````

API Examples
------------

You can explore Genesis's API, test parameters or get examples for different transaction types with: [Genesis Client Integration]


[Composer]: https://getcomposer.org/
[Genesis Client Integration]: https://github.com/E-ComProcessing/genesis_api_examples