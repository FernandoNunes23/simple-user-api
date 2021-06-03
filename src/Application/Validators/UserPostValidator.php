<?php


namespace App\Application\Validators;


class UserPostValidator
{
	/**
	 * @param array $data
	 * @return bool
	 * @throws \Exception
	 */
	public function validate(array $data)
	{
		if (empty($data["name"])) {
			throw new \Exception("Parameter 'name' cannot be null.");
		}

		if (empty($data["second_name"])) {
			throw new \Exception("Parameter 'second_name' cannot be null.");
		}

		if (empty($data["email"])) {
			throw new \Exception("Parameter 'email' cannot be null.");
		}

		if (empty($data["phone"])) {
			throw new \Exception("Parameter 'phone' cannot be null.");
		}

		return true;
	}
}