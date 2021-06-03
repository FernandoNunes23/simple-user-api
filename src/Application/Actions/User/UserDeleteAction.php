<?php


namespace App\Application\Actions\User;


use Psr\Http\Message\ResponseInterface as Response;

class UserDeleteAction extends UserAction
{

	protected function action(): Response
	{
		$data = $this->request->getParsedBody();

		if (empty($data["email"])) {
			throw new \InvalidArgumentException("Must inform field 'email'.");
		}

		$result = $this->userPersister->deleteByEmail($data["email"]);

		if (false === $result) {
			return $this->respondNotFound("User not found.");
		}

		return $this->respondOk("User deleted.");
	}
}