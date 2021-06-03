<?php


namespace App\Application\Validators;


class AccountGetBalanceValidator
{
    /**
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function validate(array $data)
    {
        if (empty($data["account_id"])) {
            throw new \Exception("Parametro de consulta 'account_id' nao definido.");
        }

        return true;
    }
}