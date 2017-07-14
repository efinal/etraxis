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

namespace eTraxis\Repository;

use eTraxis\Entity\User;
use eTraxis\Tests\WebTestCase;

class UserRepositoryTest extends WebTestCase
{
    public function testFindOneByUsernameSuccess()
    {
        /** @var UserRepository $repository */
        $repository = $this->doctrine->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByUsername('admin@example.com');

        self::assertInstanceOf(User::class, $user);
        self::assertEquals('eTraxis Admin', $user->fullname);
    }

    public function testFindOneByUsernameUnknown()
    {
        /** @var UserRepository $repository */
        $repository = $this->doctrine->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByUsername('404@example.com');

        self::assertNull($user);
    }
}
