<?php
/**
 * Created by PhpStorm.
 * User: ericksonreyes
 * Date: 2019-06-24
 * Time: 10:23
 */

namespace Acme\Banking\Account\Domain;

/**
 * Interface AccountFactory
 * @package Acme\Banking\Account\Domain
 */
interface AccountFactory
{
    /**
     * @param string $accountNumber
     * @return AccountInterface
     */
    public function create(string $accountNumber): AccountInterface;
}
