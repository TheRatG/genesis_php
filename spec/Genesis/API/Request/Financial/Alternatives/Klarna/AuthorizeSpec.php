<?php

namespace spec\Genesis\API\Request\Financial\Alternatives\Klarna;

use Genesis\API\Request\Financial\Alternatives\Klarna\Authorize;
use PhpSpec\ObjectBehavior;
use \Genesis\API\Request\Financial\Alternatives\Klarna\Items as KlarnaItems;
use \Genesis\API\Request\Financial\Alternatives\Klarna\Item as KlarnaItem;

class AuthorizeSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(\Genesis\API\Request\Financial\Alternatives\Klarna\Authorize::class);
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

    public function it_should_fail_when_missing_required_currency_param()
    {
        $this->setRequestParameters();
        $this->setCurrency(null);
        $this->shouldThrow()->during('getDocument');
    }

    public function it_should_fail_when_missing_billing_country_parameter()
    {
        $this->setRequestParameters();
        $this->setBillingCountry(null);
        $this->shouldThrow()->during('getDocument');
    }

    public function it_should_fail_when_wrong_billing_country_parameter()
    {
        $this->setRequestParameters();
        $this->setBillingCountry('TR');
        $this->shouldThrow()->during('getDocument');
    }

    public function it_should_fail_when_missing_shipping_country_parameter()
    {
        $this->setRequestParameters();
        $this->setShippingCountry(null);
        $this->shouldThrow()->during('getDocument');
    }

    public function it_should_fail_when_wrong_shipping_country_parameter()
    {
        $this->setRequestParameters();
        $this->setShippingCountry('TR');
        $this->shouldThrow()->during('getDocument');
    }

    public function it_should_fail_when_missing_return_success_url_parameter()
    {
        $this->setRequestParameters();
        $this->setReturnSuccessUrl(null);
        $this->shouldThrow()->during('getDocument');
    }

    public function it_should_fail_when_missing_return_failure_url_parameter()
    {
        $this->setRequestParameters();
        $this->setReturnFailureUrl(null);
        $this->shouldThrow()->during('getDocument');
    }

    public function it_should_fail_when_missing_remote_ip_parameter()
    {
        $this->setRequestParameters();
        $this->setRemoteIp(null);
        $this->shouldThrow()->during('getDocument');
    }

    public function it_should_fail_when_missing_required_items_param()
    {
        $this->setRequestParameters();
        $this->setItems(new KlarnaItems('EUR'));
        $this->shouldThrow()->during('getDocument');
    }

    public function it_should_fail_when_missing_required_payment_method_category_param()
    {
        $this->setRequestParameters();
        $this->setPaymentMethodCategory(null);
        $this->shouldThrow()->during('getDocument');
    }

    public function it_should_fail_when_wrong_payment_method_category_param()
    {
        $this->setRequestParameters();
        $this->setPaymentMethodCategory('NOT_VALID_PAYMENT_METHOD_CATEGORY');
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

        $item  = new KlarnaItem(
            $faker->name,
            KlarnaItem::ITEM_TYPE_PHYSICAL,
            1,
            10,
            25
        );

        $items = new KlarnaItems('EUR');
        $items->addItem($item);

        $this->setTransactionId($faker->numberBetween(1, PHP_INT_MAX));

        $this->setUsage('Genesis PHP Client Automated Request');
        $this->setRemoteIp($faker->ipv4);
        $this->setReturnSuccessUrl($faker->url);
        $this->setReturnFailureUrl($faker->url);
        $this->setPaymentMethodCategory(Authorize::PAYMENT_METHOD_CATEGORY_PAY_LATER);
        $this->setAmount($items->getAmount());
        $this->setCurrency('EUR');

        $this->setBillingFirstName($faker->firstName);
        $this->setBillingLastName($faker->lastName);
        $this->setBillingAddress1($faker->streetAddress);
        $this->setBillingZipCode($faker->postcode);
        $this->setBillingCity($faker->city);
        $this->setBillingState($faker->state);
        $this->setBillingCountry('NL');

        $this->setShippingFirstName($faker->firstName);
        $this->setShippingLastName($faker->lastName);
        $this->setShippingAddress1($faker->streetAddress);
        $this->setShippingZipCode($faker->postcode);
        $this->setShippingCity($faker->city);
        $this->setShippingState($faker->state);
        $this->setShippingCountry('NL');

        $this->setItems($items);
    }

    public function getMatchers()
    {
        return [
            'beEmpty' => function ($subject) {
                return empty($subject);
            },
        ];
    }
}
