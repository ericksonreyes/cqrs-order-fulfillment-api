<?php

namespace spec\Fulfillment\Application\Handler;

use Faker\Factory;
use Faker\Generator;
use Fulfillment\Application\CreateOrder;
use Fulfillment\Application\Handler\CreateOrderHandler;
use Fulfillment\Domain\Model\Order\Exceptions\DuplicateOrderIdError;
use Fulfillment\Domain\Model\Order\OrderInterface;
use Fulfillment\Domain\Model\Order\Repository\OrderRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CreateOrderHandlerSpec extends ObjectBehavior
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
        $this->shouldHaveType(CreateOrderHandler::class);
    }

    public function it_handles_commands(CreateOrder $command)
    {
        $command->orderId()->shouldBeCalled()->willReturn($orderId = $this->seeder->uuid);
        $command->invoker()->shouldBeCalled()->willReturn($invoker = $this->seeder->uuid);
        $command->customerId()->shouldBeCalled()->willReturn($customerId = $this->seeder->uuid);
        $command->items()->shouldBeCalled()->willReturn($items = $this->seeder->paragraphs);

        $this->expectedRepository->findById($orderId)->shouldBeCalled()->willReturn(null);
        $this->expectedRepository->store(Argument::type(OrderInterface::class))->shouldBeCalledTimes(1);
        $this->handleThis($command)->shouldBeNull();
    }

    public function it_stops_when_the_order_does_not_exist(CreateOrder $command, OrderInterface $duplicateOrder)
    {
        $command->orderId()->shouldBeCalled()->willReturn($orderId = $this->seeder->uuid);
        $this->expectedRepository->findById($orderId)->shouldBeCalled()->willReturn($duplicateOrder);
        $this->shouldThrow(DuplicateOrderIdError::class)->during('handleThis', [$command]);
    }

    public function it_has_repository()
    {
        $this->repository()->shouldReturn($this->expectedRepository);
    }

}