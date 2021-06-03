<?php


namespace App\Application\Actions\Account;


use App\Application\Actions\Action;
use App\Domain\Persister\AccountPersister;
use App\Domain\Repository\AccountRepository;
use Psr\Log\LoggerInterface;

abstract class AccountAction extends Action
{
    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @var AccountPersister
     */
    protected $accountPersister;

    /**
     * AccountAction constructor.
     *
     * @param LoggerInterface $logger
     * @param AccountRepository $accountRepository
     * @param AccountPersister $accountPersister
     */
    public function __construct(
        LoggerInterface $logger,
        AccountRepository $accountRepository,
        AccountPersister $accountPersister
    )
    {
        parent::__construct($logger);
        $this->accountRepository = $accountRepository;
        $this->accountPersister  = $accountPersister;
    }
}