<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2017 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\User\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\CommandBus\User\RegisterExternalAccountCommand;
use eTraxis\Entity\User;
use Psr\Log\LoggerInterface;

/**
 * Command handler.
 */
class RegisterExternalAccountHandler
{
    protected $logger;
    protected $manager;

    /**
     * Dependency Injection constructor.
     *
     * @param LoggerInterface        $logger
     * @param EntityManagerInterface $manager
     */
    public function __construct(LoggerInterface $logger, EntityManagerInterface $manager)
    {
        $this->logger  = $logger;
        $this->manager = $manager;
    }

    /**
     * Registers external account in eTraxis database.
     * If specified account is already registered, its properties will be updated.
     *
     * @param RegisterExternalAccountCommand $command
     */
    public function handle(RegisterExternalAccountCommand $command)
    {
        $repository = $this->manager->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneBy([
            'accountProvider' => $command->provider,
            'accountUid'      => $command->uid,
        ]);

        // If we can't find the account by its UID, try to find by the email.
        if ($user === null) {
            $this->logger->info('Cannot find by UID.', [$command->provider, $command->uid]);

            $user = $repository->findOneBy([
                'email' => $command->email,
            ]);
        }

        // Register new account.
        if ($user === null) {
            $this->logger->info('Register external account.', [$command->email, $command->fullname]);

            $user = new User();
        }
        // The account already exists - update it.
        else {
            $this->logger->info('Update external account.', [$command->email, $command->fullname]);
        }

        $user->setAccountProvider($command->provider);
        $user->setAccountUid($command->uid);

        $user->email    = $command->email;
        $user->fullname = $command->fullname;

        $this->manager->persist($user);
    }
}
