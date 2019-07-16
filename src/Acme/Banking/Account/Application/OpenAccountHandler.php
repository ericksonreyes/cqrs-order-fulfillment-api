<?php
/**
 * Created by PhpStorm.
 * User: ericksonreyes
 * Date: 2019-06-24
 * Time: 11:10
 */

namespace Acme\Banking\Account\Application;

use Acme\Banking\Account\Domain\AccountFactory;
use Acme\Banking\Account\Domain\AccountInterface;
use Acme\Banking\Account\Domain\Exception\DuplicateAccountNumberException;
use Acme\Banking\Account\Domain\Repository\AccountRepository;
use EricksonReyes\DomainDrivenDesign\Common\ValueObject\StringValue;

class OpenAccountHandler
{

    /**
     * @var AccountRepository
     */
    private $repository;

    /**
     * @var AccountFactory
     */
    private $factory;

    /**
     * OpenAccountHandler constructor.
     * @param AccountRepository $repository
     * @param AccountFactory $factory
     */
    public function __construct(AccountRepository $repository, AccountFactory $factory)
    {
        $this->repository = $repository;
        $this->factory = $factory;
    }

    /**
     * @param OpenAccount $request
     */
    public function handleThis(OpenAccount $request): void
    {
        if ($this->repository()->findByAccountNumber($request->accountNumber()) instanceof AccountInterface) {
            throw new DuplicateAccountNumberException(AccountInterface::VALIDATION_DUPLICATE_ACCOUNT_NUMBER);
        }

        $openedBy = $request->openedBy();
        $accountNumber = new StringValue($request->accountNumber());
        $accountName = new StringValue($request->accountName());
        $branchName = new StringValue($request->branchName());

        $newAccount = $this->factory()->create($request->accountNumber());
        $newAccount->open($openedBy, $accountNumber, $accountName, $branchName);

        $this->repository()->store($newAccount);
    }

    /**
     * @return AccountRepository
     */
    private function repository(): AccountRepository
    {
        return $this->repository;
    }

    /**
     * @return AccountFactory
     */
    private function factory(): AccountFactory
    {
        return $this->factory;
    }
}
