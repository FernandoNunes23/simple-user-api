<?php


namespace App\Domain\Entity;

use JsonSerializable;

class Account implements JsonSerializable
{
    /**
     * @var
     */
    private $id;

    /**
     * @var
     */
    private $balance;

    /**
     * Account constructor.
     *
     * @param string|null $id
     * @param float $balance
     */
    public function __construct(?string $id, float $balance)
    {
        $this->id = $id;
        $this->balance = $balance;
    }

    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Método responsável por depositar valores na conta
     *
     * @param float $value
     */
    public function deposit(float $value)
    {
        $this->balance += $value;
    }

    /**
     * Método responsável por sacar valores da conta
     *
     * @param float $value
     * @throws \Exception
     */
    public function withdraw(float $value)
    {
        if ($value > $this->balance) {
            throw new \Exception("Valor solicitado para operação é maior que o valor disponível na conta.");
        }

        $this->balance -= $value;
    }

    /**
     * Método responsável por transferir valores entre contas
     *
     * @param float $value
     * @param Account $receiverAccount
     * @return Account
     *
     * @throws \Exception
     */
    public function transfer(float $value, Account $receiverAccount)
    {
        $this->withdraw($value);
        $receiverAccount->deposit($value);

        return $receiverAccount;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            "id"      => (string) $this->id,
            "balance" => $this->balance
        ];
    }
}