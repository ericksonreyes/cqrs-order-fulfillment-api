<?php
namespace spec\Fulfillment\Application;

use Fulfillment\Application\AcceptOrder;
use Faker\Factory;
use Faker\Generator;
use PhpSpec\ObjectBehavior;
use InvalidArgumentException;


class AcceptOrderSpec extends ObjectBehavior
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
	

    public function __construct()
    {
        $this->seeder = Factory::create();
    }

    public function let()
    {
        $invoker = $this->seeder->uuid;
        $this->beConstructedWith(
            $this->expectedInvoker = $invoker, 
			$this->expectedOrderId = $this->seeder->word
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(AcceptOrder::class);
    }

    public function it_requires_a_command_invoker()
    {
        $invoker = str_repeat(' ', mt_rand(1, 10));
        $this->shouldThrow(InvalidArgumentException::class)->during('__construct', [
            $invoker, 
			$this->expectedOrderId = $this->seeder->word
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

}