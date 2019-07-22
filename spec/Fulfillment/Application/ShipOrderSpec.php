<?php

namespace spec\Fulfillment\Application;

use DateTimeImmutable;
use DateTimeInterface;
use Faker\Factory;
use Faker\Generator;
use Fulfillment\Application\ShipOrder;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;

class ShipOrderSpec extends ObjectBehavior
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
    protected $expectedShipper;

    /**
     * @var string
     */
    protected $expectedTrackingId;

    /**
     * @var DateTimeInterface
     */
    protected $expectedDateShipped;


    public function __construct()
    {
        $this->seeder = Factory::create();
    }

    public function let(DateTimeImmutable $dateShipped)
    {
        $invoker = $this->seeder->uuid;
        $this->beConstructedWith(
            $this->expectedInvoker = $invoker,
            $this->expectedOrderId = $this->seeder->word,
            $this->expectedShipper = $this->seeder->word,
            $this->expectedTrackingId = $this->seeder->word,
            $this->expectedDateShipped = $dateShipped
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ShipOrder::class);
    }

    public function it_requires_a_command_invoker()
    {
        $invoker = str_repeat(' ', mt_rand(1, 10));
        $this->shouldThrow(InvalidArgumentException::class)->during('__construct', [
            $invoker,
            $this->expectedOrderId = $this->seeder->word,
            $this->expectedShipper = $this->seeder->word,
            $this->expectedTrackingId = $this->seeder->word,
            $this->expectedDateShipped = $this->expectedDateShipped
        ]);
    }

    public function it_has_a_command_invoker()
    {
        $this->invoker()->shouldReturn($this->expectedInvoker);
    }


    public function it_has_orderId()
    {
        $this->orderId()->shouldReturn($this->expectedOrderId);
    }


    public function it_has_shipper()
    {
        $this->shipper()->shouldReturn($this->expectedShipper);
    }


    public function it_has_trackingId()
    {
        $this->trackingId()->shouldReturn($this->expectedTrackingId);
    }


    public function it_has_dateShipped()
    {
        $this->dateShipped()->shouldReturn($this->expectedDateShipped);
    }

}