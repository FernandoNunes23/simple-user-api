<?php


namespace App\Domain\Persister;


use App\Domain\Entity\User;
use App\Infrastructure\Persistence\FilePersistence;

class UserPersister
{
	private $persistence;

	public function __construct(FilePersistence $persistence)
	{
		$this->persistence = $persistence;
	}

	public function persist(User $user, $operation = "create")
	{
		if ($operation == "create") {
			$user = $this->persistence->save($user);
		}

		if ($operation == "update") {
			$this->persistence->update($user);
		}

		return $user;
	}

	public function deleteByEmail(string $email)
	{
		$user = $this->persistence->getByEmail($email);

		if (empty($user)) {
			return false;
		}

		return $this->persistence->delete($user, "email");
	}
}