<?php


namespace App\Application\Actions\User;


use App\Application\Validators\UserPostValidator;
use App\Domain\Entity\User;
use App\Domain\Persister\UserPersister;
use App\Domain\Repository\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class UserPostAction extends UserAction
{
	private $validator;

	public function __construct(
		LoggerInterface $logger,
		UserPersister $userPersister,
		UserRepository $userRepository,
		UserPostValidator $validator
	)
	{
		parent::__construct($logger, $userPersister, $userRepository);
		$this->validator = $validator;
	}

	protected function action(): Response
	{
		$data = $this->request->getParsedBody();

		$this->validator->validate($data);

		$user = new User($data["name"], $data["second_name"], $data["email"], $data["phone"]);

		$user = $this->userPersister->persist($user);

		return $this->respondWithData($user->toArray());
	}
}