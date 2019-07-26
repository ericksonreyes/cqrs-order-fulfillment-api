<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Exception\OrderNotFoundError;
use App\Models\Query\Order;
use App\Models\Query\OrderItem;
use DateTime;
use Exception;
use Fulfillment\Application\AcceptOrder;
use Fulfillment\Application\CancelOrder;
use Fulfillment\Application\CloseOrder;
use Fulfillment\Application\ShipOrder;
use Illuminate\Http\Request;

class OrdersController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \ReflectionException
     */
    public function findAll(Request $request)
    {
        try {
            $page = $request->has('page') ? $request->get('page') : 1;
            $size = $request->has('size') ? $request->get('size') : 10;
            $offset = ($page > 0 ? $page - 1 : 0) * $size;

            $orders = Order::skip($offset)->take($size)->get();

            $collection = [];
            foreach ($orders as $orderIndex => $order) {
                $collection[$orderIndex] = [
                    'id' => $order->id,
                    'status' => $order->status,
                    'customerId' => $order->customerId,
                    'postedOn' => $order->postedOn,
                    'lastUpdatedOn' => $order->lastUpdatedOn,
                    'items' => []
                ];

                $items = OrderItem::where('orderId', $order->id)->get();
                foreach ($items as $item) {
                    $collection[$orderIndex]['items'][] = [
                        'id' => $item->id,
                        'productId' => $item->productId,
                        'price' => $item->price,
                        'quantity' => $item->quantity
                    ];
                }
            }

            return $this->response(
                $collection,
                200
            );
        } catch (Exception $exception) {
            return $this->exception($exception);
        }
    }

    /**
     * @param string $id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \ReflectionException
     */
    public function findOne(string $id)
    {
        try {
            $order = Order::where('id', $id)->first();

            if (!$order) {
                throw new OrderNotFoundError('Order not found.');
            }

            $order = [
                'id' => $order->id,
                'status' => $order->status,
                'customerId' => $order->customerId,
                'postedOn' => $order->postedOn,
                'lastUpdatedOn' => $order->lastUpdatedOn,
                'items' => []
            ];

            $items = OrderItem::where('orderId', $id)->get();
            foreach ($items as $item) {
                $order['items'][] = [
                    'id' => $item->id,
                    'productId' => $item->productId,
                    'price' => $item->price,
                    'quantity' => $item->quantity
                ];
            }

            return $this->response(
                $order,
                200
            );
        } catch (Exception $exception) {
            return $this->exception($exception);
        }
    }

    public function accept(string $id)
    {
        try {
            $command = new AcceptOrder(
                $this->currentlyLoggedInUser(),
                $id
            );
            $this->handler()->execute($command);

            return $this->response(
                [],
                204
            );
        } catch (Exception $exception) {
            return $this->exception($exception);
        }
    }

    public function ship(Request $request, string $id)
    {
        try {
            $command = new ShipOrder(
                $this->currentlyLoggedInUser(),
                $id,
                $request->get('shipper'),
                $request->get('trackingId'),
                new DateTime('@' . $request->get('dateShipped'))
            );
            $this->handler()->execute($command);

            return $this->response(
                [],
                204
            );
        } catch (Exception $exception) {
            return $this->exception($exception);
        }
    }


    public function cancel(Request $request, string $id)
    {
        try {
            $command = new CancelOrder(
                $this->currentlyLoggedInUser(),
                $id,
                $request->get('reason')
            );
            $this->handler()->execute($command);

            return $this->response(
                [],
                204
            );
        } catch (Exception $exception) {
            return $this->exception($exception);
        }
    }

    /**
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \ReflectionException
     */
    public function complete(string $id)
    {
        try {
            $command = new CloseOrder(
                $this->currentlyLoggedInUser(),
                $id
            );
            $this->handler()->execute($command);

            return $this->response(
                [],
                204
            );
        } catch (Exception $exception) {
            return $this->exception($exception);
        }
    }
}
