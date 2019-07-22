<?php

namespace spec\Fulfillment\Application\Handler;

use DateTimeImmutable;
use Faker\Factory;
use Faker\Generator;
use Fulfillment\Application\Handler\ShipOrderHandler;
use Fulfillment\Application\ShipOrder;
use Fulfillment\Domain\Model\Order\OrderInterface;
use Fulfillment\Domain\Model\Order\Repository\OrderRepository;
use PhpSpec\ObjectBehavior;

class ShipOrderHandlerSpec extends ObjectBehavior
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
        $this->shouldHaveType(ShipOrderHandler::class);
    }

    public function it_handles_commands(ShipOrder $command, OrderInterface $existingOrder)
    {
        $command->orderId()->shouldBeCalled()->willReturn($orderId = $this->seeder->uuid);
        $command->invoker()->shouldBeCalled()->willReturn($invoker = $this->seeder->uuid);
        $command->shipper()->shouldBeCalled()->willReturn($shipper = $this->seeder->company);
        $command->trackingId()->shouldBeCalled()->willReturn($trackingId = $this->seeder->uuid);
        $command->dateShipped()->shouldBeCalled()->willReturn($dateShipped = new DateTimeImmutable());

        $existingOrder->ship($invoker, $shipper, $trackingId, $dateShipped)->shouldBeCalled();

        $this->expectedRepository->findById($orderId)->shouldBeCalled()->willReturn($existingOrder);
        $this->expectedRepository->store($existingOrder)->shouldBeCalledTimes(1);
        $this->handleThis($command)->shouldBeNull();
    }


    public function it_has_repository()
    {
        $this->repository()->shouldReturn($this->expectedRepository);
    }

}