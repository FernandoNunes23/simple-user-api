<?php


namespace App\Domain\Repository;


use App\Infrastructure\Persistence\FilePersistence;

class UserRepository
{
	private $persistence;

	public function __construct(FilePersistence $persistence)
	{
		$this->persistence = $persistence;
	}

	public function findAll()
	{
		return $this->persistence->getAll();
	}

	public function find($id)
	{
		return $this->persistence->get($id);
	}
}