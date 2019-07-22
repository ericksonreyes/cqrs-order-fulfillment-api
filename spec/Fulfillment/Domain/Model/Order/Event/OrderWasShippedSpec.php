<?php

namespace spec\Fulfillment\Domain\Model\Order\Event;

use DateTimeImmutable;
use DateTimeInterface;
use EricksonReyes\DomainDrivenDesign\Domain\Event;
use Faker\Factory;
use Faker\Generator;
use Fulfillment\Domain\Model\Order\Event\OrderWasShipped;
use PhpSpec\ObjectBehavior;

class OrderWasShippedSpec extends ObjectBehavior
{
    /**
     * @var Generator
     */
    protected $seeder;

    /**
     * @var string
     */
    protected $expectedRaisedBy;

    /**
     * @var string
     */
    protected $expectedEntityId;

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


    public function let()
    {
        $this->beConstructedThrough('raise', [
            $this->expectedRaisedBy = $this->seeder->uuid,
            $this->expectedEntityId = $this->seeder->uuid,
            $this->expectedShipper = $this->seeder->word,
            $this->expectedTrackingId = $this->seeder->word,
            $this->expectedDateShipped = new DateTimeImmutable()
        ]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(OrderWasShipped::class);
        $this->shouldHaveType(Event::class);
    }

    public function it_has_entity_id()
    {
        $this->entityId()->shouldReturn($this->expectedEntityId);
    }

    public function it_has_entity_details()
    {
        $this::staticEntityContext()->shouldReturn('Fulfillment');
        $this::staticEntityType()->shouldReturn('Order');
        $this->entityContext()->shouldReturn('Fulfillment');
        $this->entityType()->shouldReturn('Order');
    }

    public function it_has_event_date()
    {
        $this->happenedOn()->shouldHaveType(DateTimeImmutable::class);
    }

    public function it_has_event_name()
    {
        $this::staticEventName()->shouldReturn('OrderWasShipped');
        $this->eventName()->shouldReturn('OrderWasShipped');
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


    public function it_has_array_representation()
    {
        $this->toArray()->shouldBeArray();
    }

    public function it_can_be_restored_from_array()
    {
        $array = [
            'happenedOn' => time(),
            'data' => [
                'raisedBy' => $expectedRaisedBy = $this->seeder->uuid,
                'entityId' => $expectedEntityId = $this->seeder->uuid,
                'shipper' => $expectedShipper = $this->seeder->word,
                'trackingId' => $expectedTrackingId = $this->seeder->word,
                'dateShipped' => $expectedDateShipped = (new DateTimeImmutable())->getTimestamp()
            ]
        ];

        $this::fromArray($array)->shouldHaveType(OrderWasShipped::class);
        $this::fromArray($array)->happenedOn()->shouldHaveType(DateTimeImmutable::class);
        $this::fromArray($array)->entityId()->shouldReturn($expectedEntityId);
        $this::fromArray($array)->shipper()->shouldReturn($expectedShipper);
        $this::fromArray($array)->trackingId()->shouldReturn($expectedTrackingId);
        $this::fromArray($array)->dateShipped()->shouldHaveType(DateTimeInterface::class);
    }

}