<?php


namespace App\Domain\Entity\Factory;


use App\Domain\Entity\Account;

class AccountEntityFactory
{
    public function createAccount(?string $id, float $balance)
    {
        return new Account($id, $balance);
    }
}