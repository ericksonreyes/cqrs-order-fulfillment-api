<?php

namespace spec\Fulfillment\Application;

use Faker\Factory;
use Faker\Generator;
use Fulfillment\Application\CreateOrder;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;


class CreateOrderSpec extends ObjectBehavior
{
    /**
     * @var Generator
     */
    protected $seeder;

    /**
     * @var string
     */
    protected $expectedInvoker;

    /**
     * @var string
     */
    protected $expectedOrderId;

    /**
     * @var string
     */
    protected $expectedCustomerId;

    /**
     * @var array
     */
    protected $expectedItems;


    public function __construct()
    {
        $this->seeder = Factory::create();
    }

    public function let()
    {
        $invoker = $this->seeder->uuid;
        $this->beConstructedWith(
            $this->expectedInvoker = $invoker,
            $this->expectedOrderId = $this->seeder->word,
            $this->expectedCustomerId = $this->seeder->word,
            $this->expectedItems = $this->seeder->paragraphs
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CreateOrder::class);
    }

    public function it_requires_a_command_invoker()
    {
        $invoker = str_repeat(' ', mt_rand(1, 10));
        $this->shouldThrow(InvalidArgumentException::class)->during('__construct', [
            $invoker,
            $this->expectedOrderId = $this->seeder->word,
            $this->expectedCustomerId = $this->seeder->word,
            $this->expectedItems = $this->seeder->paragraphs
        ]);
    }

    public function it_has_a_command_invoker()
    {
        $this->invoker()->shouldReturn($this->expectedInvoker);
    }


    public function it_has_an_order_identifier()
    {
        $this->orderId()->shouldReturn($this->expectedOrderId);
    }


    public function it_has_a_customer_id()
    {
        $this->customerId()->shouldReturn($this->expectedCustomerId);
    }


    public function it_has_order_items()
    {
        $this->items()->shouldReturn($this->expectedItems);
    }

}