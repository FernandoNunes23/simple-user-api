<?php


namespace Tests\Domain\Entity\Factory;


use App\Domain\Entity\Account;
use App\Domain\Entity\Factory\AccountEntityFactory;
use Tests\TestCase;

class AccountEntityFactoryTest extends TestCase
{
    /**
     * @return array[]
     */
    public function accountProvider()
    {
        return [
            ["1", 100],
            ["2", 20]
        ];
    }


    /**
     * @dataProvider accountProvider
     * @param $id
     * @param $balance
     */
    public function testCreateAccount($id, $balance)
    {
        $accountEntityFactory = new AccountEntityFactory();

        $account = $accountEntityFactory->createAccount($id, $balance);

        $this->assertInstanceOf(Account::class, $account);
        $this->assertEquals($id, $account->getId());
        $this->assertEquals($balance, $account->getBalance());
    }
}