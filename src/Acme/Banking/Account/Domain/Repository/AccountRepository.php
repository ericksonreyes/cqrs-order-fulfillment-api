<?php
/**
 * Created by PhpStorm.
 * User: ericksonreyes
 * Date: 2019-06-24
 * Time: 10:13
 */

namespace Acme\Banking\Account\Domain\Repository;

use Acme\Banking\Account\Domain\AccountInterface;

/**
 * Interface AccountRepository
 * @package Acme\Banking\Account\Domain\Repository
 */
interface AccountRepository
{

    /**
     * @param AccountInterface $account
     */
    public function store(AccountInterface $account): void;

    /**
     * @param string $accountNumber
     * @return AccountInterface|null
     */
    public function findByAccountNumber(string $accountNumber): ?AccountInterface;
}
