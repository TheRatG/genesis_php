<?php

namespace spec\Genesis\API\Request\Financial\Alternatives;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class P24Spec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Genesis\API\Request\Financial\Alternatives\P24');
    }

    public function it_can_build_structure()
    {
        $this->setRequestParameters();
        $this->getDocument()->shouldNotBeEmpty();
    }

    public function it_should_fail_when_no_parameters()
    {
        $this->shouldThrow()->during('getDocument');
    }

    public function it_should_fail_when_missing_email_parameters()
    {
        $this->setRequestParameters();
        $this->shouldThrow()->during('setCustomerEmail', [ null ]);
    }

    public function it_should_fail_when_missing_currency_parameters()
    {
        $this->setRequestParameters();
        $this->setCurrency(null);
        $this->shouldThrow()->during('getDocument');
    }

    public function it_should_fail_when_missing_billing_country_parameters()
    {
        $this->setRequestParameters();
        $this->setBillingCountry(null);
        $this->shouldThrow()->during('getDocument');
    }

    public function it_should_fail_when_wrong_billing_country_parameters()
    {
        $this->setRequestParameters();
        $this->setBillingCountry('BG');
        $this->shouldThrow()->during('getDocument');
    }

    protected function setRequestParameters()
    {
        $faker = \Faker\Factory::create();

        $faker->addProvider(new \Faker\Provider\en_US\Person($faker));
        $faker->addProvider(new \Faker\Provider\Payment($faker));
        $faker->addProvider(new \Faker\Provider\en_US\Address($faker));
        $faker->addProvider(new \Faker\Provider\en_US\PhoneNumber($faker));
        $faker->addProvider(new \Faker\Provider\Internet($faker));

        $this->setTransactionId($faker->numberBetween(1, PHP_INT_MAX));

        $this->setUsage('Genesis Automated PHP Request');
        $this->setRemoteIp($faker->ipv4);

        $this->setCurrency(
            $faker->randomElement(
                \Genesis\Utils\Currency::getList()
            )
        );

        $this->setAmount($faker->numberBetween(1, PHP_INT_MAX));

        $this->setCustomerEmail($faker->email);

        $this->setBillingCountry('NL');

        $this->setReturnSuccessUrl($faker->url);
        $this->setReturnFailureUrl($faker->url);
    }

    public function getMatchers()
    {
        return array(
            'beEmpty'       => function ($subject) {
                return empty($subject);
            },
            'contain'       => function ($subject, $arg) {
                return (stripos($subject, $arg) !== false);
            },
        );
    }
}
