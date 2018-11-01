<?php

namespace spec\Genesis;

use Genesis\API\Constants\Transaction\Types;
use PhpSpec\ObjectBehavior;

class GenesisSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedWith('NonFinancial\Blacklist');
        $this->shouldHaveType('Genesis\Genesis');
    }

    public function it_can_load_request()
    {
        $this->beConstructedWith('NonFinancial\Blacklist');

        $this->request()->shouldHaveType('\Genesis\API\Request\NonFinancial\Blacklist');
    }

    public function it_can_load_financial_request_from_factory()
    {
        $params = [
            'card_number' => '420000',
            'card_holder' => 'Card Holder'
        ];

        $this->beConstructedThrough(
            'financialFactory',
            [
                Types::SALE,
                $params
            ]
        );
        $this->shouldHaveType('\Genesis\Genesis');

        $this->request()->shouldHaveType('\Genesis\API\Request\Financial\Cards\Sale');
        $this->request()->getCardNumber()->shouldBe('420000');
        $this->request()->getCardHolder()->shouldBe('Card Holder');
    }

    public function it_should_fail_loading_financial_request_from_factory_with_wrong_type()
    {
        $this->beConstructedThrough(
            'financialFactory',
            [
                'wrong_type'
            ]
        );

        $this->shouldThrow('\Genesis\Exceptions\InvalidArgument')->duringInstantiation();
    }

    public function it_should_fail_loading_financial_request_from_factory_with_wrong_params()
    {
        $params = [
            'wrong' => 'parameter'
        ];

        $this->beConstructedThrough(
            'financialFactory',
            [
                Types::SALE,
                $params
            ]
        );

        $this->shouldThrow('\Genesis\Exceptions\InvalidMethod')->duringInstantiation();
    }

    public function it_can_load_deprecated_void_request()
    {
        $this->beConstructedWith('Financial\Void');

        $this->request()->shouldHaveType('\Genesis\API\Request\Financial\Cancel');
    }

    public function it_can_set_request_property()
    {
        $this->beConstructedWith('NonFinancial\Blacklist');

        $this->request()->shouldHaveType('\Genesis\API\Request\NonFinancial\Blacklist');

        $this->request()->setCardNumber('420000');

        $this->request()->getCardNumber()->shouldBe('420000');
    }

    public function it_can_send_request()
    {
        $this->beConstructedWith('NonFinancial\Blacklist');

        $this->request()->setCardNumber('4200000000000000');

        $this->shouldThrow('\Genesis\Exceptions\ErrorNetwork')->during('execute');

        $this->response()->getResponseObject()->shouldBe(null);
    }

    public function it_fails_on_deprecated_request()
    {
        $this->beConstructedWith('NonFinancial\AVS');

        $this->shouldThrow('\Genesis\Exceptions\DeprecatedMethod')->duringInstantiation();
    }

    public function it_fails_on_non_existing_transaction_request()
    {
        $this->beConstructedWith('Non\Existing\Transaction');

        $this->shouldThrow('\Genesis\Exceptions\InvalidMethod')->duringInstantiation();
    }
}
