<?php

namespace spec\Fulfillment\Domain\Model\Order;

use DateTimeImmutable;
use DateTimeInterface;
use EricksonReyes\DomainDrivenDesign\EventSourcedEntity;
use Faker\Factory;
use Faker\Generator;
use Fulfillment\Domain\Model\Order\Event\OrderWasAccepted;
use Fulfillment\Domain\Model\Order\Event\OrderWasCancelled;
use Fulfillment\Domain\Model\Order\Event\OrderWasCompleted;
use Fulfillment\Domain\Model\Order\Event\OrderWasPlaced;
use Fulfillment\Domain\Model\Order\Event\OrderWasShipped;
use Fulfillment\Domain\Model\Order\Exceptions\AnonymousOrderCommandError;
use Fulfillment\Domain\Model\Order\Exceptions\EmptyOrderError;
use Fulfillment\Domain\Model\Order\Exceptions\MissingCustomerIdError;
use Fulfillment\Domain\Model\Order\Exceptions\MissingShipperError;
use Fulfillment\Domain\Model\Order\Exceptions\MissingTrackingIdError;
use Fulfillment\Domain\Model\Order\Order;
use Fulfillment\Domain\Model\Order\OrderInterface;
use InvalidArgumentException;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;

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


    public function __construct()
    {
        $this->seeder = Factory::create();
    }

    public function let()
    {
        $identifier = $this->seeder->uuid;
        $this->beConstructedWith($this->expectedEntityId = $identifier);
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

    public function it_can_be_created()
    {
        $this->create(
            $createdBy = $this->seeder->uuid,
            $customerId = $this->seeder->uuid,
            $items = $this->seeder->paragraphs
        )->shouldBeNull();

        $this->customerId()->shouldReturn($customerId);
        $this->items()->shouldReturn($items);
        $this->postedOn()->shouldHaveType(DateTimeInterface::class);
        $this->items()->shouldReturn($items);

        $this->status()->shouldReturn(OrderInterface::ORDER_STATUS_PENDING);
        $this->storedEvents()->shouldHaveInstanceOf(OrderWasPlaced::class);
    }

    public function it_prevents_missing_created_by()
    {
        $this->shouldThrow(
            AnonymousOrderCommandError::class
        )->during(
            'create',
            [
                $createdBy = str_repeat(' ', random_int(0, 3)),
                $customerId = $this->seeder->uuid,
                $items = $this->seeder->paragraphs
            ]
        );
    }

    public function it_prevents_missing_customer_id()
    {
        $this->shouldThrow(
            MissingCustomerIdError::class
        )->during(
            'create',
            [
                $createdBy = $this->seeder->uuid,
                $customerId = str_repeat(' ', random_int(0, 3)),
                $items = $this->seeder->paragraphs
            ]
        );
    }

    public function it_prevents_empty_orders()
    {
        $this->shouldThrow(
            EmptyOrderError::class
        )->during(
            'create',
            [
                $createdBy = $this->seeder->uuid,
                $customerId = $this->seeder->uuid,
                $items = []
            ]
        );
    }

    public function it_can_be_accepted()
    {
        $acceptedBy = $this->seeder->uuid;
        $this->accept($acceptedBy)->shouldBeNull();

        $this->status()->shouldReturn(OrderInterface::ORDER_STATUS_ACCEPTED);
        $this->storedEvents()->shouldHaveInstanceOf(OrderWasAccepted::class);
    }

    public function it_prevents_anonymous_acceptance_of_orders()
    {
        $this->shouldThrow(
            AnonymousOrderCommandError::class
        )->during(
            'accept',
            [
                $acceptedBy = str_repeat(' ', random_int(0, 3))
            ]
        );
    }

    public function it_can_be_shipped()
    {
        $shippedBy = $this->seeder->uuid;
        $shipper = $this->seeder->uuid;
        $trackingId = $this->seeder->uuid;
        $dateShipped = new DateTimeImmutable();
        $this->ship($shippedBy, $shipper, $trackingId, $dateShipped)->shouldBeNull();

        $this->status()->shouldReturn(OrderInterface::ORDER_STATUS_SHIPPED);
        $this->storedEvents()->shouldHaveInstanceOf(OrderWasShipped::class);
    }

    public function it_prevents_anonymous_shipping_of_orders()
    {
        $this->shouldThrow(
            AnonymousOrderCommandError::class
        )->during(
            'ship',
            [
                $shipping = str_repeat(' ', random_int(0, 3)),
                $shipper = $this->seeder->uuid,
                $trackingId = $this->seeder->uuid,
                $dateShipped = new DateTimeImmutable()
            ]
        );
    }

    public function it_prevents_shipping_without_a_shipper()
    {
        $this->shouldThrow(
            MissingShipperError::class
        )->during(
            'ship',
            [
                $shipping = $this->seeder->uuid,
                $shipper = str_repeat(' ', random_int(0, 3)),
                $trackingId = $this->seeder->uuid,
                $dateShipped = new DateTimeImmutable()
            ]
        );
    }

    public function it_prevents_shipping_without_a_tracking_id()
    {
        $this->shouldThrow(
            MissingTrackingIdError::class
        )->during(
            'ship',
            [
                $shipping = $this->seeder->uuid,
                $shipper = $this->seeder->uuid,
                $trackingId = str_repeat(' ', random_int(0, 3)),
                $dateShipped = new DateTimeImmutable()
            ]
        );
    }

    public function it_prevents_anonymous_cancellations()
    {
        $this->shouldThrow(
            AnonymousOrderCommandError::class
        )->during(
            'cancel',
            [
                $cancelledBy = str_repeat(' ', random_int(0, 3)),
                $reason = $this->seeder->sentence
            ]
        );
    }

    public function it_prevents_anonymous_closures()
    {
        $this->shouldThrow(
            AnonymousOrderCommandError::class
        )->during(
            'close',
            [
                $closedBy = str_repeat(' ', random_int(0, 3))
            ]
        );
    }

    public function it_can_be_cancelled()
    {
        $cancelledBy = $this->seeder->uuid;
        $reason = $this->seeder->paragraph;
        $this->cancel($cancelledBy, $reason)->shouldBeNull();

        $this->status()->shouldReturn(OrderInterface::ORDER_STATUS_CANCELLED);
        $this->storedEvents()->shouldHaveInstanceOf(OrderWasCancelled::class);
    }

    public function it_can_be_completed()
    {
        $closedBy = $this->seeder->uuid;
        $this->close($closedBy)->shouldBeNull();

        $this->status()->shouldReturn(OrderInterface::ORDER_STATUS_COMPLETED);
        $this->storedEvents()->shouldHaveInstanceOf(OrderWasCompleted::class);
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