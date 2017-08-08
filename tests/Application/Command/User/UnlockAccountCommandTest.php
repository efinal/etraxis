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

namespace eTraxis\Application\Command\User;

use eTraxis\Domain\Entity\User;
use eTraxis\Framework\Tests\TransactionalTestCase;

class UnlockAccountCommandTest extends TransactionalTestCase
{
    public function testUnlockUser()
    {
        /** @var \Pignus\Model\UserRepositoryInterface $repository */
        $repository = $this->doctrine->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByUsername('artem@example.com');
        $user->lockAccount();

        self::assertFalse($user->isAccountNonLocked());

        $command = new UnlockAccountCommand([
            'username' => $user->getUsername(),
        ]);

        $this->commandbus->handle($command);

        self::assertTrue($user->isAccountNonLocked());
    }
}