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

use Doctrine\Common\Persistence\ObjectManager;
use eTraxis\CommandBus\User\UnlockAccountCommand;
use eTraxis\Entity\User;

/**
 * Command handler.
 */
class UnlockAccountHandler
{
    protected $manager;

    /**
     * Dependency Injection constructor.
     *
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Clears locks count for specified account.
     *
     * @param UnlockAccountCommand $command
     */
    public function handle(UnlockAccountCommand $command)
    {
        /** @var \Pignus\Model\UserRepositoryInterface $repository */
        $repository = $this->manager->getRepository(User::class);

        /** @var User $user */
        if ($user = $repository->findOneByUsername($command->username)) {

            $user->unlockAccount();

            $this->manager->persist($user);
        }
    }
}
