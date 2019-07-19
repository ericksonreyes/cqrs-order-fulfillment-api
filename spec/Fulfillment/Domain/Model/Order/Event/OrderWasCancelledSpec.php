<?php
namespace spec\Fulfillment\Domain\Model\Order\Event;

use EricksonReyes\DomainDrivenDesign\Domain\Event;
use Fulfillment\Domain\Model\Order\Event\OrderWasCancelled;
use DateTimeImmutable;
use Faker\Factory;
use Faker\Generator;
use PhpSpec\ObjectBehavior;

class OrderWasCancelledSpec extends ObjectBehavior
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

    

    public function __construct()
    {
        $this->seeder = Factory::create();
    }


    public function let()
    {
        $this->beConstructedThrough('raise', [
            $this->expectedRaisedBy = $this->seeder->uuid,
            $this->expectedEntityId = $this->seeder->uuid,
        ]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(OrderWasCancelled::class);
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
        $this::staticEventName()->shouldReturn('OrderWasCancelled');
        $this->eventName()->shouldReturn('OrderWasCancelled');
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
            ]
        ];

        $this::fromArray($array)->shouldHaveType(OrderWasCancelled::class);
        $this::fromArray($array)->happenedOn()->shouldHaveType(DateTimeImmutable::class);
        $this::fromArray($array)->entityId()->shouldReturn($expectedEntityId);
        
    }

}