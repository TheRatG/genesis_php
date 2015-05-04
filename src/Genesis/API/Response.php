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
namespace Genesis\API;

/**
 * Response - process/format an incoming Genesis response
 *
 * @package    Genesis
 * @subpackage API
 */
class Response
{
    /**
     * Store parsed, response object
     *
     * @var \stdClass
     */
    public $responseObj;

    /**
     * Store the response raw data
     *
     * @var String
     */
    public $responseRaw;

    /**
     * Initialize with NetworkContext (if available)
     *
     * @param \Genesis\Network|null $networkContext
     *
     * @throws \Genesis\Exceptions\InvalidArgument
     */
    public function __construct($networkContext = null)
    {
        if (is_a($networkContext, '\Genesis\Network')) {
            $this->parseResponse($networkContext->getResponseBody());
        }
    }

    /**
     * Parse Genesis response to stdClass and
     * apply transformation to known fields
     *
     * @param string $response
     *
     * @throws \Genesis\Exceptions\ErrorAPI
     * @throws \Genesis\Exceptions\InvalidArgument
     * @throws \Genesis\Exceptions\InvalidResponse
     */
    public function parseResponse($response)
    {
        // Save a copy of the incoming response
        $this->responseRaw = $response;

        // Try to parse the incoming response
        try {
            $this->responseObj = \Genesis\Utils\Common::xmlToObj($response);
        }
        catch (\Exception $e) {
            throw new \Genesis\Exceptions\InvalidResponse();
        }

        // Check if there is an API error
        if ($this->isSuccessful() === false) {
            throw new \Genesis\Exceptions\ErrorAPI(
                $this->responseObj->technical_message ?: 'Unknown API Error!'
            );
        }

        // Transform Amount from Major to Minor unit
        $this->transformAmountUnit();
        // Transform Timestamp from String to DateTime
        $this->transformTimestamp();
    }

    /**
     * Check whether the request was successful
     *
     * Note: You should consult with the documentation
     * which transaction responses have status available.
     *
     * @return bool | null (on missing status)
     */
    public function isSuccessful()
    {
        $statuses = array(
            \Genesis\API\Constants\Transaction\States::APPROVED,
            \Genesis\API\Constants\Transaction\States::PENDING_ASYNC,
            \Genesis\API\Constants\Transaction\States::NEW_STATUS,
        );

        if (isset($this->responseObj->status)) {
            if (in_array($this->responseObj->status, $statuses)) {
                $status = true;
            } else {
                $status = false;
            }
        } else {
            // return null if status is inapplicable
            $status = null;
        }

        return $status;
    }

    /**
     * Check whether the transaction was partially approved
     *
     * @see Genesis_API_Documentation for more information
     *
     * @return bool | null (if inapplicable)
     */
    public function isPartiallyApproved()
    {
        if (isset($this->responseObj->partial_approval)) {
            return \Genesis\Utils\Common::stringToBoolean($this->responseObj->partial_approval);
        }

        return null;
    }

    /**
     * Try to fetch a description of the received Error Code
     *
     * @return string | null (if inapplicable)
     */
    public function getErrorDescription()
    {
        if (isset($this->responseObj->code) && !empty($this->responseObj->code)) {
            return Constants\Errors::getErrorDescription($this->responseObj->code);
        }

        if (isset($this->responseObj->response_code) && !empty($this->responseObj->response_code)) {
            return Constants\Errors::getIssuerResponseCode($this->responseObj->response_code);
        }

        return null;
    }

    /**
     * Get the raw Genesis output
     *
     * @return String
     */
    public function getResponseRaw()
    {
        return $this->responseRaw;
    }

    /**
     * Get the parsed response
     *
     * @return \stdClass
     */
    public function getResponseObject()
    {
        return $this->responseObj;
    }

    /**
     * Get formatted amount (instead of ISO4217, return in float)
     *
     * @return String | null (if amount/currency are unavailable)
     */
    private function transformAmountUnit()
    {
        if (isset($this->responseObj->currency) && !empty($this->responseObj->currency)) {
            if (isset($this->responseObj->amount) && !empty($this->responseObj->amount)) {
                $this->responseObj->amount = \Genesis\Utils\Currency::exponentToAmount(
                    $this->responseObj->amount,
                    $this->responseObj->currency
                );
            }
        }

        return null;
    }

    /**
     * Get DateTime object from the timestamp inside the response
     *
     * @return \DateTime|null (if invalid timestamp)
     */
    private function transformTimestamp()
    {
        if (isset($this->responseObj->timestamp) && !empty($this->responseObj->timestamp)) {
            try {
                $this->responseObj->timestamp = new \DateTime($this->responseObj->timestamp);
            }
            catch (\Exception $e) {
                // Just log the attempt
                error_log($e->getMessage());
            }
        }

        return null;
    }
}
