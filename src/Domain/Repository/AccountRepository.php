<?php


namespace App\Domain\Repository;


use App\Infrastructure\Persistence\CachePersistence;

class AccountRepository
{
    private $persistence;

    public function __construct(CachePersistence $persistence)
    {
        $this->persistence = $persistence;
    }

    public function find(string $id)
    {
        return $this->persistence->get($id);
    }
}