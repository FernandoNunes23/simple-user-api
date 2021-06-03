<?php


namespace Tests\Application\Actions\Account;


use App\Application\Validators\AccountGetBalanceValidator;
use App\Domain\Entity\Account;
use App\Domain\Repository\AccountRepository;
use DI\Container;
use Prophecy\Argument;
use Tests\TestCase;

class AccountGetBalanceActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $account = new Account("10", 20);

        $accountValidatorProphecy = $this->prophesize(AccountGetBalanceValidator::class);

        $accountValidatorProphecy
            ->validate(Argument::type("array"))
            ->willReturn(true);

        $container->set(AccountGetBalanceValidator::class, $accountValidatorProphecy->reveal());

        $accountRepositoryProphecy = $this->prophesize(AccountRepository::class);
        $accountRepositoryProphecy
            ->find(Argument::type("string"))
            ->willReturn($account)
            ->shouldBeCalledOnce();

        $container->set(AccountRepository::class, $accountRepositoryProphecy->reveal());

        $request = $this->createRequest('GET', '/balance');
        $request = $request->withQueryParams(["account_id" => 10]);

        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = "20";

        $this->assertEquals($expectedPayload, $payload);
    }
}