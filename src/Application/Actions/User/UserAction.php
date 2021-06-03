<?php

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Domain\Persister\UserPersister;
use App\Domain\Repository\UserRepository;
use Psr\Log\LoggerInterface;

abstract class UserAction extends Action
{
	/**
	 * @var UserPersister
	 */
	protected $userPersister;

	/**
	 * @var UserRepository
	 */
	protected $userRepository;

	/**
	 * UserAction constructor.
	 *
	 * @param LoggerInterface $logger
	 * @param UserPersister $userPersister
	 * @param UserRepository $userRepository
	 */
	public function __construct(
		LoggerInterface $logger,
		UserPersister $userPersister,
		UserRepository $userRepository
	)
	{
		parent::__construct($logger);
		$this->userPersister  = $userPersister;
		$this->userRepository = $userRepository;
	}
}