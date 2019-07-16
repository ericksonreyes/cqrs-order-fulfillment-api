<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class AccountController extends Controller
{

    /**
     * @param Request $request
     * @param string $customerId
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \ReflectionException
     */
    public function createAction(Request $request, string $customerId)
    {
        try {
            // Respond
            return $this->response(
                [],
                201
            );
        } catch (Exception $exception) {
            return $this->exception($exception);
        }
    }


    /**
     * @param Request $request
     * @param string $customerId
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \ReflectionException
     */
    public function updateAction(Request $request, string $customerId)
    {
        try {
            // Respond
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
     * @param string $customerId
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \ReflectionException
     */
    public function deleteAction(Request $request, string $customerId)
    {
        try {
            // Respond
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
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \ReflectionException
     */
    public function findAction(Request $request)
    {
        try {
            return $this->response(
                [],
                200
            );
        } catch (Exception $exception) {
            return $this->exception($exception);
        }
    }


    /**
     * @param string $customerId
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \ReflectionException
     */
    public function findOneAction(string $customerId)
    {
        try {
            return $this->response(
                [],
                200
            );
        } catch (Exception $exception) {
            return $this->exception($exception);
        }
    }
}
