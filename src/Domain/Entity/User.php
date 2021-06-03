<?php


namespace App\Domain\Entity;


class User
{
	private $id;
	private $name;
	private $secondName;
	private $email;
	private $phone;

	public function __construct(string $name, string $secondName, string $email, string $phone)
	{
		$this->name = $name;
		$this->secondName = $secondName;
		$this->email = $email;
		$this->phone = $phone;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function setId(int $id)
	{
		$this->id = $id;
	}

	public function setName(string $name)
	{
		$this->name = $name;
	}

	public function setSecondName(string $secondName)
	{
		$this->secondName = $secondName;
	}

	public function setEmail(string $email)
	{
		$this->email = $email;
	}

	public function setPhone(string $phone)
	{
		$this->phone = $phone;
	}

	public function toArray()
	{
		return [
			"id" => $this->id,
			"name" => $this->name,
			"second_name" => $this->secondName,
			"email" => $this->email,
			"phone" => $this->phone
		];
	}
}