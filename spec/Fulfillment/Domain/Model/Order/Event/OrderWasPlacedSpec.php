<?php
namespace spec\Fulfillment\Domain\Model\Order\Event;

use EricksonReyes\DomainDrivenDesign\Domain\Event;
use Fulfillment\Domain\Model\Order\Event\OrderWasPlaced;
use DateTimeImmutable;
use Faker\Factory;
use Faker\Generator;
use PhpSpec\ObjectBehavior;

class OrderWasPlacedSpec extends ObjectBehavior
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
        $this->beConstructedThrough('raise', [
            $this->expectedRaisedBy = $this->seeder->uuid,
            $this->expectedEntityId = $this->seeder->uuid,
			$this->expectedCustomerId = $this->seeder->word, 
			$this->expectedItems = $this->seeder->paragraphs
        ]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(OrderWasPlaced::class);
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
        $this::staticEventName()->shouldReturn('OrderWasPlaced');
        $this->eventName()->shouldReturn('OrderWasPlaced');
    }

    
    public function it_has_customerId()
    {
        $this->customerId()->shouldReturn($this->expectedCustomerId);
    }


    public function it_has_items()
    {
        $this->items()->shouldReturn($this->expectedItems);
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
				'customerId' => $expectedCustomerId = $this->seeder->word, 
				'items' => $expectedItems = $this->seeder->paragraphs
            ]
        ];

        $this::fromArray($array)->shouldHaveType(OrderWasPlaced::class);
        $this::fromArray($array)->happenedOn()->shouldHaveType(DateTimeImmutable::class);
        $this::fromArray($array)->entityId()->shouldReturn($expectedEntityId);
        $this::fromArray($array)->customerId()->shouldReturn($expectedCustomerId);
		$this::fromArray($array)->items()->shouldReturn($expectedItems);
    }

}