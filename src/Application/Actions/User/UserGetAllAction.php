<?php


namespace App\Application\Actions\User;


use Psr\Http\Message\ResponseInterface as Response;

class UserGetAllAction extends UserAction
{

	protected function action(): Response
	{
		$users = $this->userRepository->findAll();

		$data = [];

		foreach ($users as $user) {
			$data[] = $user->toArray();
		}

		return $this->respondWithData($data);
	}
}