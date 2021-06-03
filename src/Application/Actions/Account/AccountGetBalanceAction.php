<?php


namespace App\Application\Actions\Account;


use App\Application\Validators\AccountGetBalanceValidator;
use App\Domain\Persister\AccountPersister;
use App\Domain\Repository\AccountRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class AccountGetBalanceAction extends AccountAction
{
    /**
     * @var AccountGetBalanceValidator
     */
    private $accountGetBalanceValidator;

    public function __construct(
        LoggerInterface $logger,
        AccountRepository $accountRepository,
        AccountPersister $accountPersister,
        AccountGetBalanceValidator  $accountGetBalanceValidator
    )
    {
        parent::__construct($logger, $accountRepository, $accountPersister);

        $this->accountGetBalanceValidator = $accountGetBalanceValidator;
    }

    /**
     * @return Response
     *
     * @throws \Exception
     */
    protected function action(): Response
    {
        $this->accountGetBalanceValidator->validate($this->request->getQueryParams());

        $id = $this->request->getQueryParams()["account_id"];

        $this->logger->info(sprintf("Buscando saldo da conta de id {%s}",$id));

        $account = $this->accountRepository->find($id);

        if ($account == null) {
            $this->logger->warning(sprintf("Conta de id{%s} não encontrada", $id));

            return $this->respondNotFound();
        }

        $this->logger->info(sprintf("Retornando saldo %s da conta id{%f}", $id, $account->getBalance()));

        return $this->respondOk($account->getBalance());
    }
}