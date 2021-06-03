<?php


namespace Tests\Application\Actions\Account;


use App\Application\Validators\AccountDepositValidator;
use App\Application\Validators\AccountTransferValidator;
use App\Application\Validators\AccountWithdrawValidator;
use App\Domain\Entity\Account;
use App\Domain\Persister\AccountPersister;
use App\Domain\Repository\AccountRepository;
use DI\Container;
use Prophecy\Argument;
use Tests\TestCase;

class AccountEventActionTest extends TestCase
{
    public function testActionTypeDeposit()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $account = new Account("10", 20);

        $accountValidatorProphecy = $this->prophesize(AccountDepositValidator::class);

        $accountValidatorProphecy
            ->validate(Argument::type("array"))
            ->willReturn(true);

        $container->set(AccountDepositValidator::class, $accountValidatorProphecy->reveal());

        $accountRepositoryProphecy = $this->prophesize(AccountRepository::class);
        $accountRepositoryProphecy
            ->find(Argument::type("string"))
            ->willReturn($account)
            ->shouldBeCalledOnce();

        $container->set(AccountRepository::class, $accountRepositoryProphecy->reveal());

        $accountPersisterProphecy = $this->prophesize(AccountPersister::class);

        $accountPersisterProphecy
            ->persist(Argument::type(Account::class));

        $request = $this->createRequest('POST', '/event');
        $request = $request->withParsedBody(["type" => "deposit", "destination" => 10, "amount" => 20]);

        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = '{"destination":{"id":"10", "balance":40}}';

        $this->assertEquals($expectedPayload, $payload);
    }

    public function testActionTypeWithdraw()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $account = new Account("10", 20);

        $accountValidatorProphecy = $this->prophesize(AccountWithdrawValidator::class);

        $accountValidatorProphecy
            ->validate(Argument::type("array"))
            ->willReturn(true);

        $container->set(AccountWithdrawValidator::class, $accountValidatorProphecy->reveal());

        $accountRepositoryProphecy = $this->prophesize(AccountRepository::class);
        $accountRepositoryProphecy
            ->find(Argument::type("string"))
            ->willReturn($account)
            ->shouldBeCalledOnce();

        $container->set(AccountRepository::class, $accountRepositoryProphecy->reveal());

        $accountPersisterProphecy = $this->prophesize(AccountPersister::class);

        $accountPersisterProphecy
            ->persist(Argument::type(Account::class));

        $request = $this->createRequest('POST', '/event');
        $request = $request->withParsedBody(["type" => "withdraw", "origin" => 10, "amount" => 20]);

        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = '{"origin":{"id":"10", "balance":0}}';

        $this->assertEquals($expectedPayload, $payload);
    }

    public function testActionTypeTransfer()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $originAccount = new Account("10", 20);
        $destinationAccount = new Account("20", 10);

        $accountValidatorProphecy = $this->prophesize(AccountTransferValidator::class);

        $accountValidatorProphecy
            ->validate(Argument::type("array"))
            ->willReturn(true);

        $container->set(AccountTransferValidator::class, $accountValidatorProphecy->reveal());

        $accountRepositoryProphecy = $this->prophesize(AccountRepository::class);
        $accountRepositoryProphecy
            ->find(Argument::type("string"))
            ->willReturn(
                $originAccount,
                $destinationAccount
            );

        $container->set(AccountRepository::class, $accountRepositoryProphecy->reveal());

        $accountPersisterProphecy = $this->prophesize(AccountPersister::class);

        $accountPersisterProphecy
            ->persist(Argument::type(Account::class));

        $request = $this->createRequest('POST', '/event');
        $request = $request->withParsedBody(["type" => "transfer", "origin" => 10, "destination" => 20, "amount" => 20]);

        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = '{"origin":{"id":"10", "balance":0}, "destination":{"id":"20", "balance":30}}';

        $this->assertEquals($expectedPayload, $payload);
    }
}