<?php


namespace App\Application\Actions\Account;

use App\Application\Validators\AccountDepositValidator;
use App\Application\Validators\AccountTransferValidator;
use App\Application\Validators\AccountWithdrawValidator;
use App\Domain\Entity\Factory\AccountEntityFactory;
use App\Domain\Persister\AccountPersister;
use App\Domain\Repository\AccountRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

/**
 * Class AccountEventAction
 *
 * @package App\Application\Actions\Account
 */
class AccountEventAction extends AccountAction
{

    /**
     * @var AccountEntityFactory
     */
    private $accountEntityFactory;

    /**
     * @var AccountDepositValidator
     */
    private $accountDepositValidator;

    /**
     * @var AccountWithdrawValidator
     */
    private $accountWithdrawValidator;

    /**
     * @var AccountTransferValidator
     */
    private $accountTransferValidator;

    /**
     * AccountEventAction constructor.
     *
     * @param LoggerInterface $logger
     * @param AccountRepository $accountRepository
     * @param AccountPersister $accountPersister
     * @param AccountEntityFactory $accountEntityFactory
     * @param AccountDepositValidator $accountDepositValidator
     * @param AccountWithdrawValidator $accountWithdrawValidator
     * @param AccountTransferValidator $accountTransferValidator
     */
    public function __construct(
        LoggerInterface $logger,
        AccountRepository $accountRepository,
        AccountPersister $accountPersister,
        AccountEntityFactory $accountEntityFactory,
        AccountDepositValidator $accountDepositValidator,
        AccountWithdrawValidator $accountWithdrawValidator,
        AccountTransferValidator $accountTransferValidator
    )
    {
        parent::__construct($logger, $accountRepository, $accountPersister);

        $this->accountEntityFactory     = $accountEntityFactory;
        $this->accountDepositValidator  = $accountDepositValidator;
        $this->accountWithdrawValidator = $accountWithdrawValidator;
        $this->accountTransferValidator = $accountTransferValidator;
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        $data = $this->request->getParsedBody();

        if ($data["type"] == "deposit") {
            return $this->deposit($data);
        }

        if ($data["type"] == "withdraw") {
            return $this->withdraw($data);
        }

        if ($data["type"] == "transfer") {
            return $this->transfer($data);
        }
    }

    /**
     * @param array $data
     * @return Response
     */
    private function transfer(array $data): Response
    {
        $this->accountTransferValidator->validate($data);

        $this->logger->info(
            sprintf("Iniciando processo de transferência do valor %s da conta %s para conta %s",
                $data["amount"],
                $data["origin"],
                $data["destination"]
            )
        );

        $originId = $data["origin"];
        $value = $data["amount"];
        $destinationId = $data["destination"];

        $this->logger->info("Buscando informações das contas.");
        $originAccount = $this->accountRepository->find($originId);
        $destinationAccount = $this->accountRepository->find($destinationId);

        if ($originAccount == null) {
            $this->logger->notice(sprintf("Conta de origem de id{%s} não encontrada.", $originId));

            return $this->respondNotFound();
        }

        if ($destinationAccount == null) {
            $this->logger->notice(sprintf("Conta de destino de id{%s} não encontrada.", $destinationId));

            return $this->respondNotFound();
        }

        try {
            $destinationAccount = $originAccount->transfer($value, $destinationAccount);
        } catch (\Exception $e) {
            $this->logger->warning(sprintf("Não foi possível transferir o valor, erro: %s", $e->getMessage()));

            throw $e;
        }

        $this->logger->info("Persistindo atualizações nas contas.");
        $this->accountPersister->persist($originAccount);
        $this->accountPersister->persist($destinationAccount);

        $responseData["origin"] = $originAccount->jsonSerialize();
        $responseData["destination"] = $destinationAccount->jsonSerialize();

        return $this->respondWithData($responseData,201);
    }

    /**
     * @param array $data
     * @return Response
     */
    private function withdraw(array $data): Response
    {
        $this->accountWithdrawValidator->validate($data);

        $this->logger->info(
            sprintf("Iniciando processo de saque do valor %s na conta %s",
                $data["amount"],
                $data["origin"]
            )
        );

        $id = $data["origin"];
        $value = $data["amount"];

        $this->logger->info("Buscando informações da conta.");
        $account = $this->accountRepository->find($id);

        if ($account == null) {
            $this->logger->notice(sprintf("Conta de id{%s} não encontrada.", $id));

            return $this->respondNotFound();
        }

        try {
            $account->withdraw($value);
        } catch (\Exception $e) {
            $this->logger->warning(sprintf("Não foi possível sacar o valor, erro: %s", $e->getMessage()));

            throw $e;
        }

        $this->logger->info("Persistindo dados atualizados da conta.");
        $account = $this->accountPersister->persist($account);

        $responseData["origin"] = $account->jsonSerialize();

        return $this->respondWithData($responseData, 201);
    }

    /**
     * @param array $data
     * @return Response
     */
    private function deposit(array $data): Response
    {
        $this->accountDepositValidator->validate($data);

        $this->logger->info(
            sprintf("Iniciando processo de depósito do valor %s na conta %s",
                $data["amount"],
                $data["destination"]
            )
        );

        $id = $data["destination"];
        $value = $data["amount"];

        $this->logger->info("Buscando informações da conta.");
        $account = $this->accountRepository->find($id);

        if ($account == null) {
            $this->logger->info(sprintf("Não foi encontrado uma conta com id{%s}, criando uma nova.", $id));
            $account = $this->accountEntityFactory->createAccount($id, $value);
        } else {
            $account->deposit($value);
        }

        $this->logger->info("Persistindo dados atualizados da conta.");
        $account = $this->accountPersister->persist($account);

        $responseData["destination"] = $account->jsonSerialize();

        return $this->respondWithData($responseData, 201);
    }
}