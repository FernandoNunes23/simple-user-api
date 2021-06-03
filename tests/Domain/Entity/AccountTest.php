<?php


namespace Tests\Domain\Entity;


use App\Domain\Entity\Account;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
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
    public function testGetters($id, $balance)
    {
        $account = new Account($id, $balance);

        $this->assertEquals($id, $account->getId());
        $this->assertEquals($balance, $account->getBalance());
    }

    /**
     * @dataProvider accountProvider
     * @param $id
     * @param $balance
     */
    public function testDepositSuccess($id, $balance)
    {
        $account = new Account($id, $balance);

        $depositAmount = 20;
        $balanceAfterDeposit = $account->getBalance() + $depositAmount;

        $account->deposit($depositAmount);

        $this->assertEquals($balanceAfterDeposit, $account->getBalance());
    }

    /**
     * @dataProvider accountProvider
     * @param $id
     * @param $balance
     *
     * @throws \Exception
     */
    public function testWithdrawSuccess($id, $balance)
    {
        $account = new Account($id, $balance);

        $withdrawAmount = 20;
        $balanceAfterWithdraw = $account->getBalance() - $withdrawAmount;

        $account->withdraw($withdrawAmount);

        $this->assertEquals($balanceAfterWithdraw, $account->getBalance());
    }

    /**
     * @dataProvider accountProvider
     * @param $id
     * @param $balance
     *
     * @throws \Exception
     */
    public function testWithdrawFailing($id, $balance)
    {
        $this->expectException(\Exception::class);
        $account = new Account($id, $balance);

        $withdrawAmount = 150;

        $account->withdraw($withdrawAmount);
    }

    /**
     * @dataProvider accountProvider
     * @param $id
     * @param $balance
     *
     * @throws \Exception
     */
    public function testTransferSuccess($id, $balance)
    {
        $originAccount      = new Account($id, $balance);
        $destinationAccount = new Account($id, $balance);

        $transferAmount = 20;
        $originAccountBalanceAfterTransfer = $originAccount->getBalance() - $transferAmount;
        $destinationAccountBalanceAfterTransfer = $originAccount->getBalance() + $transferAmount;

        $updatedDestinationAccount = $originAccount->transfer($transferAmount, $destinationAccount);

        $this->assertEquals($originAccountBalanceAfterTransfer, $originAccount->getBalance());
        $this->assertEquals($destinationAccountBalanceAfterTransfer, $updatedDestinationAccount->getBalance());
    }

    /**
     * @dataProvider accountProvider
     * @param $id
     * @param $balance
     *
     * @throws \Exception
     */
    public function testTransferFailing($id, $balance)
    {
        $this->expectException(\Exception::class);

        $originAccount      = new Account($id, $balance);
        $destinationAccount = new Account($id, $balance);

        $transferAmount = 150;

        $originAccount->transfer($transferAmount, $destinationAccount);
    }

    /**
     * @dataProvider accountProvider
     * @param $id
     * @param $balance
     */
    public function testJsonSerialize($id, $balance)
    {
        $account = new Account($id, $balance);

        $expectedPayload = json_encode([
            'id' => $id,
            'balance' => $balance
        ]);

        $this->assertEquals($expectedPayload, json_encode($account));
    }
}