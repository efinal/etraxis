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

namespace eTraxis\Entity;

use eTraxis\Tests\ReflectionTrait;

class UserTest extends \PHPUnit_Framework_TestCase
{
    use ReflectionTrait;

    public function testConstructor()
    {
        $user = new User();
        self::assertEquals('ROLE_USER', $this->getProperty($user, 'role'));
    }

    public function testUsername()
    {
        $user = new User();
        self::assertNotEquals('anna@example.com', $user->getUsername());

        $user->email = 'anna@example.com';
        self::assertEquals('anna@example.com', $user->getUsername());
    }

    public function testPassword()
    {
        $user = new User();
        self::assertNotEquals('secret', $user->getPassword());

        $user->password = 'secret';
        self::assertEquals('secret', $user->getPassword());
    }

    public function testRoles()
    {
        $user = new User();
        self::assertEquals(['ROLE_USER'], $user->getRoles());

        $user->isAdmin = true;
        self::assertEquals(['ROLE_ADMIN'], $user->getRoles());

        $user->isAdmin = false;
        self::assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testEncoderName()
    {
        $user = new User();

        $user->password = '8dbdda48fb8748d6746f1965824e966a';
        self::assertEquals('legacy.md5', $user->getEncoderName());

        $user->password = 'mzMEbtOdGC462vqQRa1nh9S7wyE=';
        self::assertEquals('legacy.sha1', $user->getEncoderName());

        $user->password = 'secret';
        self::assertNull($user->getEncoderName());
    }

    public function testIsAdmin()
    {
        $user = new User();
        self::assertFalse($user->isAdmin);

        $user->isAdmin = true;
        self::assertTrue($user->isAdmin);

        $user->isAdmin = false;
        self::assertFalse($user->isAdmin);
    }
}
