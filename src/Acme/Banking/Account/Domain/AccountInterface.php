<?php
/**
 * Created by PhpStorm.
 * User: ericksonreyes
 * Date: 2019-06-24
 * Time: 10:14
 */

namespace Acme\Banking\Account\Domain;

use EricksonReyes\DomainDrivenDesign\Common\ValueObject\IntegerValue;
use EricksonReyes\DomainDrivenDesign\Common\ValueObject\PersonName;
use EricksonReyes\DomainDrivenDesign\Common\ValueObject\StringValue;

/**
 * Interface Account
 * @package Acme\Banking\Account\Domain
 */
interface AccountInterface
{

    public const VALIDATION_DUPLICATE_ACCOUNT_NUMBER = 'Duplicate account number.';

    /**
     * @return StringValue
     */
    public function accountNumber(): StringValue;

    /**
     * @return PersonName
     */
    public function accountName(): PersonName;

    /**
     * @return StringValue
     */
    public function branchName(): StringValue;

    /**
     * @return IntegerValue
     */
    public function remainingBalance(): IntegerValue;

    /**
     * @param StringValue $openedBy
     * @param StringValue $accountNumber
     * @param PersonName $accountName
     * @param StringValue $branchName
     */
    public function open(
        StringValue $openedBy,
        StringValue $accountNumber,
        PersonName $accountName,
        StringValue $branchName
    ): void;

    /**
     * @param StringValue $depositedBy
     * @param IntegerValue $amountDeposited
     */
    public function deposit(StringValue $depositedBy, IntegerValue $amountDeposited): void;

    /**
     * @param StringValue $withdrawnBy
     * @param IntegerValue $amountWithdrawn
     */
    public function withdraw(StringValue $withdrawnBy, IntegerValue $amountWithdrawn): void;

    /**
     * @param StringValue $closedBy
     * @return mixed
     */
    public function close(StringValue $closedBy);
}
