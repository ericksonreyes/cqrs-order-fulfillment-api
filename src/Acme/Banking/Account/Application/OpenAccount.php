<?php
/**
 * Created by PhpStorm.
 * User: ericksonreyes
 * Date: 2019-06-24
 * Time: 10:29
 */

namespace Acme\Banking\Account\Application;

/**
 * Class OpenAccount
 * @package Acme\Banking\Account\Application
 */
class OpenAccount
{
    /**
     * @var string
     */
    private $openedBy;

    /**
     * @var string
     */
    private $accountNumber;

    /**
     * @var string
     */
    private $accountName;

    /**
     * @var string
     */
    private $branchName;

    /**
     * @var integer
     */
    private $initialDeposit;

    /**
     * OpenAccount constructor.
     * @param $openedBy
     * @param string $accountNumber
     * @param string $accountName
     * @param string $branchName
     * @param int $initialDeposit
     */
    public function __construct(
        string $openedBy,
        string $accountNumber,
        string $accountName,
        string $branchName,
        int $initialDeposit
    ) {
        $this->openedBy = $openedBy;
        $this->accountNumber = $accountNumber;
        $this->accountName = $accountName;
        $this->branchName = $branchName;
        $this->initialDeposit = $initialDeposit;
    }

    /**
     * @return mixed
     */
    public function openedBy(): string
    {
        return $this->openedBy;
    }

    /**
     * @return string
     */
    public function accountNumber(): string
    {
        return $this->accountNumber;
    }

    /**
     * @return string
     */
    public function accountName(): string
    {
        return $this->accountName;
    }

    /**
     * @return string
     */
    public function branchName(): string
    {
        return $this->branchName;
    }

    /**
     * @return int
     */
    public function initialDeposit(): int
    {
        return $this->initialDeposit;
    }
}
