<?php


namespace App\Application\Actions\User;


use Psr\Http\Message\ResponseInterface as Response;

class UserPutAction extends UserAction
{

	protected function action(): Response
	{
		$data = $this->request->getParsedBody();
		$id = $this->request->getAttribute("id");

		$user = $this->userRepository->find($id);

		if (empty($user)) {
			return $this->respondNotFound("User not found.");
		}

		if (!empty($data["name"])) {
			$user->setName($data["name"]);
		}

		if (!empty($data["second_name"])) {
			$user->setSecondName($data["second_name"]);
		}

		if (!empty($data["email"])) {
			$user->setEmail($data["email"]);
		}

		if (!empty($data["phone"])) {
			$user->setPhone($data["phone"]);
		}

		$user = $this->userPersister->persist($user, "update");

		return $this->respondWithData($user->toArray());
	}
}