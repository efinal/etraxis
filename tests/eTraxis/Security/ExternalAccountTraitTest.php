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

namespace eTraxis\Security;

use eTraxis\Dictionary\AccountProvider;
use eTraxis\Entity\User;
use Ramsey\Uuid\Uuid;

class ExternalAccountTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testIsAccountExternal()
    {
        $user = new User();

        $user->setAccountProvider(AccountProvider::ETRAXIS);
        self::assertFalse($user->isAccountExternal());

        $user->setAccountProvider(AccountProvider::LDAP);
        self::assertTrue($user->isAccountExternal());
    }

    public function testAccountProvider()
    {
        $user = new User();
        self::assertNotEquals(AccountProvider::LDAP, $user->getAccountProvider());

        $user->setAccountProvider(AccountProvider::LDAP);
        self::assertEquals(AccountProvider::LDAP, $user->getAccountProvider());

        $user->setAccountProvider('invalid');
        self::assertEquals(AccountProvider::ETRAXIS, $user->getAccountProvider());
    }

    public function testAccountUid()
    {
        $expected = Uuid::uuid4()->getHex();

        $user = new User();
        self::assertNotEquals($expected, $user->getAccountUid());

        $user->setAccountUid($expected);
        self::assertEquals($expected, $user->getAccountUid());
    }
}
