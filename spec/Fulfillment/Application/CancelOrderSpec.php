<?php

namespace spec\Fulfillment\Application;

use Faker\Factory;
use Faker\Generator;
use Fulfillment\Application\CancelOrder;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;


class CancelOrderSpec extends ObjectBehavior
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
    protected $expectedReason;


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
            $this->expectedReason = $this->seeder->word
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CancelOrder::class);
    }

    public function it_requires_a_command_invoker()
    {
        $invoker = str_repeat(' ', mt_rand(1, 10));
        $this->shouldThrow(InvalidArgumentException::class)->during('__construct', [
            $invoker,
            $this->expectedOrderId = $this->seeder->word,
            $this->expectedReason = $this->seeder->word
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


    public function it_has_cancellation_reason()
    {
        $this->reason()->shouldReturn($this->expectedReason);
    }

}