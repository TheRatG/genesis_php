<?php
/*
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @license     http://opensource.org/licenses/MIT The MIT License
 */

namespace Genesis\API\Request\Financial\OnlineBankingPayments\Citadel;

use Genesis\API\Traits\Request\AddressInfoAttributes;
use Genesis\API\Traits\Request\Financial\AsyncAttributes;
use Genesis\API\Traits\Request\Financial\NotificationAttributes;
use Genesis\API\Traits\Request\Financial\PaymentAttributes;

/**
 * Class Payin
 *
 * Citadel Payin - oBeP-style alternative payment method
 *
 * @package Genesis\API\Request\Financial\OnlineBankingPayments\Citadel
 *
 * @method Payin setMerchantCustomerId($value) Set Unique consumer account ID
 */
class Payin extends \Genesis\API\Request\Base\Financial
{
    use AsyncAttributes, NotificationAttributes, PaymentAttributes, AddressInfoAttributes;

    /**
     * Unique consumer account ID
     *
     * Identifier provided by the merchant that uniquely identifies the customer in their system
     *
     * @var string
     */
    protected $merchant_customer_id;

    /**
     * Returns the Request transaction type
     * @return string
     */
    protected function getTransactionType()
    {
        return \Genesis\API\Constants\Transaction\Types::CITADEL_PAYIN;
    }

    /**
     * Set the required fields
     *
     * @return void
     */
    protected function setRequiredFields()
    {
        $requiredFields = [
            'transaction_id',
            'amount',
            'return_success_url',
            'return_failure_url',
            'notification_url',
            'currency',
            'customer_email',
            'merchant_customer_id',
            'billing_first_name',
            'billing_last_name',
            'billing_country'
        ];

        $this->requiredFields = \Genesis\Utils\Common::createArrayObject($requiredFields);
    }

    /**
     * Return additional request attributes
     * @return array
     */
    protected function getPaymentTransactionStructure()
    {
        return [
            'amount'               => $this->transformAmount($this->amount, $this->currency),
            'currency'             => $this->currency,
            'return_success_url'   => $this->return_success_url,
            'return_failure_url'   => $this->return_failure_url,
            'notification_url'     => $this->notification_url,
            'customer_email'       => $this->customer_email,
            'customer_phone'       => $this->customer_phone,
            'merchant_customer_id' => $this->merchant_customer_id,
            'billing_address'      => [
                'first_name' => $this->billing_first_name,
                'last_name'  => $this->billing_last_name,
                'address1'   => $this->billing_address1,
                'address2'   => $this->billing_address2,
                'zip_code'   => $this->billing_zip_code,
                'city'       => $this->billing_city,
                'state'      => $this->billing_state,
                'country'    => $this->billing_country
            ],
            'shipping_address'     => [
                'first_name' => $this->shipping_first_name,
                'last_name'  => $this->shipping_last_name,
                'address1'   => $this->shipping_address1,
                'address2'   => $this->shipping_address2,
                'zip_code'   => $this->shipping_zip_code,
                'city'       => $this->shipping_city,
                'state'      => $this->shipping_state,
                'country'    => $this->shipping_country
            ]
        ];
    }
}