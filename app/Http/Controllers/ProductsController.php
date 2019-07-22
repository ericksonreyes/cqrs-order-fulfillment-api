<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Exception\ProductNotFoundError;
use App\Models\Query\Product;
use Exception;
use Illuminate\Http\Request;

class ProductsController extends Controller
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

            $products = Product::skip($offset)->take($size)->get();

            $collection = [];
            foreach ($products as $product) {
                $collection[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock
                ];
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
            $product = Product::where('id', $id)->first();
            if (!$product) {
                throw new ProductNotFoundError('Product not found.');
            }
            return $this->response(
                [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock
                ],
                200
            );
        } catch (Exception $exception) {
            return $this->exception($exception);
        }
    }
}
