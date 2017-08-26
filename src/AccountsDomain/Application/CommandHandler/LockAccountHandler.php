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

namespace eTraxis\AccountsDomain\Application\CommandHandler;

use Doctrine\Common\Persistence\ObjectManager;
use eTraxis\AccountsDomain\Application\Command\LockAccountCommand;
use eTraxis\AccountsDomain\Domain\Model\User;
use Psr\Log\LoggerInterface;

/**
 * Command handler.
 */
class LockAccountHandler
{
    protected $logger;
    protected $manager;
    protected $authFailures;
    protected $lockDuration;

    /**
     * Dependency Injection constructor.
     *
     * @param LoggerInterface $logger
     * @param ObjectManager   $manager
     * @param int             $authFailures
     * @param int             $lockDuration
     */
    public function __construct(
        LoggerInterface $logger,
        ObjectManager   $manager,
        int             $authFailures = null,
        int             $lockDuration = null
    )
    {
        $this->logger       = $logger;
        $this->manager      = $manager;
        $this->authFailures = $authFailures;
        $this->lockDuration = $lockDuration;
    }

    /**
     * Increases locks count for specified account.
     *
     * @param LockAccountCommand $command
     */
    public function handle(LockAccountCommand $command)
    {
        if ($this->authFailures === null) {
            return;
        }

        /** @var \Pignus\Model\UserRepositoryInterface $repository */
        $repository = $this->manager->getRepository(User::class);

        /** @var User $user */
        if ($user = $repository->findOneByUsername($command->username)) {

            $this->logger->info('Authentication failure', [$command->username]);

            if ($user->incAuthFailures() >= $this->authFailures) {

                if ($this->lockDuration === null) {
                    $user->lockAccount();
                }
                else {
                    $interval = sprintf('PT%dM', $this->lockDuration);
                    $user->lockAccount(date_create()->add(new \DateInterval($interval)));
                }
            }

            $this->manager->persist($user);
        }
    }
}
