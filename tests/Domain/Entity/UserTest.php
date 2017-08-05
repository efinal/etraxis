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

namespace eTraxis\Domain\Entity;

use eTraxis\Domain\Dictionary\AccountProvider;
use eTraxis\Framework\Tests\ReflectionTrait;

class UserTest extends \PHPUnit_Framework_TestCase
{
    use ReflectionTrait;

    public function testConstructor()
    {
        $user = new User();
        self::assertEquals(AccountProvider::ETRAXIS, $this->getProperty($user, 'accountProvider'));
        self::assertRegExp('/^([[:xdigit:]]{32})$/is', $this->getProperty($user, 'accountUid'));
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

    public function testIsAccountExternal()
    {
        $user = new User();
        self::assertFalse($user->isAccountExternal);

        $user->accountProvider = AccountProvider::LDAP;
        self::assertTrue($user->isAccountExternal);

        $user->accountProvider = AccountProvider::ETRAXIS;
        self::assertFalse($user->isAccountExternal);
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

    public function testLocale()
    {
        $user = new User();
        self::assertEquals('en_US', $user->locale);

        $user->locale = 'ru';
        self::assertEquals('ru', $user->locale);

        $user->locale = 'xx';
        self::assertEquals('ru', $user->locale);
    }

    public function testTheme()
    {
        $user = new User();
        self::assertEquals('azure', $user->theme);

        $user->theme = 'emerald';
        self::assertEquals('emerald', $user->theme);

        $user->theme = 'unknown';
        self::assertEquals('emerald', $user->theme);
    }

    public function testTimezone()
    {
        $user = new User();
        self::assertEquals('UTC', $user->timezone);

        $user->timezone = 'Pacific/Auckland';
        self::assertEquals('Pacific/Auckland', $user->timezone);

        $user->timezone = 'Unknown';
        self::assertEquals('Pacific/Auckland', $user->timezone);
    }

    public function testCanPasswordBeExpired()
    {
        $user = new User();
        self::assertTrue($this->callMethod($user, 'canPasswordBeExpired'));

        $user->accountProvider = AccountProvider::LDAP;
        self::assertFalse($this->callMethod($user, 'canPasswordBeExpired'));

        $user->accountProvider = AccountProvider::ETRAXIS;
        self::assertTrue($this->callMethod($user, 'canPasswordBeExpired'));
    }

    public function testCanAccountBeLocked()
    {
        $user = new User();
        self::assertTrue($this->callMethod($user, 'canAccountBeLocked'));

        $user->accountProvider = AccountProvider::LDAP;
        self::assertFalse($this->callMethod($user, 'canAccountBeLocked'));

        $user->accountProvider = AccountProvider::ETRAXIS;
        self::assertTrue($this->callMethod($user, 'canAccountBeLocked'));
    }
}
