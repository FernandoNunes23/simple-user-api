<?php


namespace App\Domain\Persister;


use App\Domain\Entity\Account;
use App\Infrastructure\Persistence\CachePersistence;

class AccountPersister
{
    private $persistence;

    public function __construct(CachePersistence $persistence)
    {
        $this->persistence = $persistence;
    }

    public function persist(Account $account)
    {
        $this->persistence->save($account->getId(), $account);

        return $account;
    }
}