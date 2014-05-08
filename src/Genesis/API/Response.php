<?php

namespace Genesis\API;

use Genesis\Exceptions;
use Genesis\Network\Request as Network;

class Response
{
    public $responseObj;

    private $networkContext;

    public function __construct(Network $networkContext)
    {
        $this->networkContext = $networkContext;

        if ( strlen($this->networkContext->getResponseBody()) > 0 )
            $this->parseResponse($this->networkContext->getResponseBody());
    }

    /**
     * Check whether the request was successful
     *
     * @return bool
     */
    public function isSuccess()
    {
        $status = false;

        if (isset($this->responseObj->status) && in_array($this->responseObj->status, array('approved', 'pending'))) {

            $code = ($this->responseObj->response_code) ? intval($this->responseObj->response_code) : null;

            if (Errors::SUCCESS === $code) {
                $status = true;
            }
        }

        return $status;
    }

    /**
     * Try to fetch a description of the received Error Code
     *
     * @return bool|string
     */
    public function getErrorDescription()
    {
        if (isset($this->responseObj->code)) {
            return Errors::getErrorDescription($this->responseObj->code);
        }
        else {
            return Errors::getIssuerResponseCode($this->responseObj->response_code);
        }
    }

    /**
     * Return the parsed response
     *
     * @return mixed
     */
    public function getResponseObject()
    {
        return $this->responseObj;
    }

    /**
     * Parse Genesis response to SimpleXMLElement
     *
     * @param $response \SimpleXMLElement
     * @throws \Genesis\Exceptions\InvalidArgument
     */
    public function parseResponse($response)
    {
        if (empty($response)) {
            throw new Exceptions\InvalidArgument();
        }

        $this->responseObj = simplexml_load_string($response);
    }
}