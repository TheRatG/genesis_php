<?php

namespace spec\Genesis\API\Request\Financial\Cards;

use Genesis\API\Request\Financial\Cards\Authorize;
use PhpSpec\ObjectBehavior;
use spec\SharedExamples\Genesis\API\Request\Financial\DescriptorAttributesExample;
use spec\SharedExamples\Genesis\API\Request\Financial\FxRateAttributesExamples;
use spec\SharedExamples\Genesis\API\Request\RequestExamples;

class AuthorizeSpec extends ObjectBehavior
{
    use RequestExamples, FxRateAttributesExamples, DescriptorAttributesExample;

    public function it_is_initializable()
    {
        $this->shouldHaveType(Authorize::class);
    }

    public function it_should_fail_when_missing_required_params()
    {
        $this->testMissingRequiredParameters([
            'card_holder',
            'card_number'
        ]);
    }

    public function it_should_fail_when_missing_cc_holder_last_name_parameter()
    {
        $this->setRequestParameters();
        $this->setCardHolder('First');

        $this->shouldThrow(
            $this->getExpectedFieldValueException('card_holder')
        )->during('getDocument');
    }

    public function it_should_fail_when_invalid_cc_holder_parameter()
    {
        $this->setRequestParameters();
        $this->setCardHolder('First$% Last');

        $this->shouldThrow(
            $this->getExpectedFieldValueException('card_holder')
        )->during('getDocument');
    }

    public function it_can_set_cc_holder_parameter_with_special_chars()
    {
        $this->setRequestParameters();
        $this->setCardHolder('Müller O\'Conner');

        $this->getDocument()->shouldNotBeEmpty();
    }

    public function it_should_fail_when_invalid_credit_card_parameter()
    {
        $this->setRequestParameters();
        $this->setCardNumber('47123');

        $this->shouldThrow(
            $this->getExpectedFieldValueException('card_number')
        )->during('getDocument');
    }

    public function it_should_fail_when_invalid_cc_exp_month_parameter()
    {
        $this->setRequestParameters();
        $this->setExpirationMonth('13');

        $this->shouldThrow(
            $this->getExpectedFieldValueException('expiration_month')
        )->during('getDocument');
    }

    public function it_should_fail_when_invalid_cc_exp_year_parameter()
    {
        $this->setRequestParameters();
        $this->setExpirationYear('201');

        $this->shouldThrow(
            $this->getExpectedFieldValueException('expiration_year')
        )->during('getDocument');
    }

    public function it_should_fail_when_unsupported_currency_parameter()
    {
        $this->setRequestParameters();
        $this->setCurrency('ABC');

        $this->shouldThrow()->during('getDocument');
    }

    protected function setRequestParameters()
    {
        $faker = $this->getFaker();

        $this->setTransactionId($faker->numberBetween(1, PHP_INT_MAX));
        $this->setCurrency(
            $faker->randomElement(
                \Genesis\Utils\Currency::getList()
            )
        );
        $this->setAmount($faker->numberBetween(1, PHP_INT_MAX));
        $this->setUsage('Genesis PHP Client Automated Request');
        $this->setRemoteIp($faker->ipv4);
        $this->setCardHolder($faker->name);
        $this->setCardNumber('4200000000000000');
        $this->setCvv(sprintf("%03s", $faker->numberBetween(1, 999)));
        $this->setExpirationMonth($faker->numberBetween(1, 12));
        $this->setExpirationYear($faker->numberBetween(date('Y'), date('Y') + 5));
        $this->setCustomerEmail($faker->email);
        $this->setCustomerPhone($faker->phoneNumber);
        $this->setBillingFirstName($faker->firstName);
        $this->setBillingLastName($faker->lastName);
        $this->setBillingAddress1($faker->streetAddress);
        $this->setBillingZipCode($faker->postcode);
        $this->setBillingCity($faker->city);
        $this->setBillingState($faker->state);
        $this->setBillingCountry($faker->countryCode);
    }
}
