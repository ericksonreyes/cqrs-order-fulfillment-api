<?php

namespace spec\Fulfillment\Application\Handler;

use Faker\Factory;
use Faker\Generator;
use Fulfillment\Application\AcceptOrder;
use Fulfillment\Application\Handler\AcceptOrderHandler;
use Fulfillment\Domain\Model\Order\Exceptions\OrderNotFoundError;
use Fulfillment\Domain\Model\Order\OrderInterface;
use Fulfillment\Domain\Model\Order\Repository\OrderRepository;
use PhpSpec\ObjectBehavior;

class AcceptOrderHandlerSpec extends ObjectBehavior
{
    /**
     * @var Generator
     */
    protected $seeder;

    /**
     * @var OrderRepository
     */
    protected $expectedRepository;


    public function __construct()
    {
        $this->seeder = Factory::create();
    }

    public function let(OrderRepository $repository)
    {
        $this->beConstructedWith(

            $this->expectedRepository = $repository
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(AcceptOrderHandler::class);
    }

    public function it_handles_commands(AcceptOrder $command, OrderInterface $existingOrder)
    {
        $command->orderId()->shouldBeCalled()->willReturn($orderId = $this->seeder->uuid);
        $command->invoker()->shouldBeCalled()->willReturn($invoker = $this->seeder->uuid);

        $existingOrder->accept($invoker)->shouldBeCalled();

        $this->expectedRepository->findById($orderId)->shouldBeCalled()->willReturn($existingOrder);
        $this->expectedRepository->store($existingOrder)->shouldBeCalledTimes(1);
        $this->handleThis($command)->shouldBeNull();
    }

    public function it_stops_when_the_order_does_not_exist(AcceptOrder $command)
    {
        $command->orderId()->shouldBeCalled()->willReturn($orderId = $this->seeder->uuid);
        $this->expectedRepository->findById($orderId)->shouldBeCalled()->willReturn(null);
        $this->shouldThrow(OrderNotFoundError::class)->during('handleThis', [$command]);
    }

    public function it_has_repository()
    {
        $this->repository()->shouldReturn($this->expectedRepository);
    }

}