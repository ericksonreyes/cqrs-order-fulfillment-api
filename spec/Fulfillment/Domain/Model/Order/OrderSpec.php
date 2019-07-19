<?php
namespace spec\Fulfillment\Domain\Model\Order;

use EricksonReyes\DomainDrivenDesign\EventSourcedEntity;
use Fulfillment\Domain\Model\Order\Order;
use PhpSpec\Exception\Example\FailureException;
use Faker\Factory;
use Faker\Generator;
use PhpSpec\ObjectBehavior;
use InvalidArgumentException;

class OrderSpec extends ObjectBehavior
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
	* @var string
	*/
	protected $expectedStatus;
	
	/**
	* @var int
	*/
	protected $expectedPostedOn;
	
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
        $identifier = $this->seeder->uuid;
        $this->beConstructedWith($this->expectedEntityId = $identifier);
        
			$this->expectedCustomerId = $this->seeder->word; 
			$this->expectedStatus = $this->seeder->word; 
			$this->expectedPostedOn = $this->seeder->numberBetween(1, 100000); 
			$this->expectedItems = $this->seeder->paragraphs;
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Order::class);
        $this->shouldHaveType(EventSourcedEntity::class);
    }

    public function it_does_not_allow_empty_user_id()
    {
        $identifier = str_repeat(' ', mt_rand(1, 10));
        $this->shouldThrow(InvalidArgumentException::class)->during('__construct', [
            $identifier
        ]);
    }

    public function it_has_entity_id()
    {
        $this->id()->shouldReturn($this->expectedEntityId);
    }

    public function it_is_not_deleted_initially()
    {
        $this->isDeleted()->shouldReturn(false);
    }

    
    public function it_has_customerId()
    {
        $this->customerId()->shouldReturn($this->expectedCustomerId);
    }


    public function it_has_status()
    {
        $this->status()->shouldReturn($this->expectedStatus);
    }


    public function it_has_postedOn()
    {
        $this->postedOn()->shouldReturn($this->expectedPostedOn);
    }


    public function it_has_items()
    {
        $this->items()->shouldReturn($this->expectedItems);
    }


    public function getMatchers(): array
    {
        return [
            'haveKey' => function ($subject, $key) {
                return array_key_exists($key, $subject);
            },
            'haveValue' => function ($subject, $value) {
                return in_array($value, $subject);
            },
            'notHaveValue' => function ($subject, $value) {
                return in_array($value, $subject);
            },
            'haveInstanceOf' => function ($subject, $expectation) {

                $found = false;
                foreach ($subject as $storedObject) {
                    if ($storedObject instanceof $expectation) {
                        $found = true;
                    }
                }
                if ($found === false) {
                    throw new FailureException(sprintf(
                        'There is no instance of "%s" in the collection.',
                        $expectation
                    ));
                }
                return true;
            },
            'haveMatchingArray' => function ($subject, $expectation) {

                $found = false;
                foreach ($subject as $storedObject) {
                    if ($storedObject = $expectation) {
                        $found = true;
                    }
                }
                if ($found === false) {
                    throw new FailureException(sprintf(
                        'There is no instance of "%s" in the collection.',
                        $expectation
                    ));
                }
                return true;
            }
        ];
    }
}