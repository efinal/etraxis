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

namespace eTraxis\AccountsDomain\Application\Command;

use eTraxis\AccountsDomain\Domain\Dictionary\AccountProvider;
use eTraxis\AccountsDomain\Domain\Model\User;
use eTraxis\SharedDomain\Framework\Tests\TransactionalTestCase;

class RegisterExternalAccountCommandTest extends TransactionalTestCase
{
    public function testRegisterUser()
    {
        $uid    = bin2hex(random_bytes(12));
        $locale = static::$kernel->getContainer()->getParameter('locale');
        $theme  = static::$kernel->getContainer()->getParameter('theme');

        $repository = $this->doctrine->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneBy([
            'email' => 'earlene.gibson@example.com',
        ]);

        self::assertNull($user);

        // first time (user never was registered before)
        $command = new RegisterExternalAccountCommand([
            'provider' => AccountProvider::LDAP,
            'uid'      => $uid,
            'email'    => 'earlene.gibson@example.com',
            'fullname' => 'Earlene Gibson',
        ]);

        $this->commandbus->handle($command);

        $user = $repository->findOneBy([
            'accountProvider' => AccountProvider::LDAP,
            'accountUid'      => $uid,
        ]);

        self::assertInstanceOf(User::class, $user);
        self::assertEquals('earlene.gibson@example.com', $user->email);
        self::assertEquals('Earlene Gibson', $user->fullname);
        self::assertEquals($locale, $user->locale);
        self::assertEquals($theme, $user->theme);

        $id = $user->id;

        // second time (assume the user changed his last name)
        $command = new RegisterExternalAccountCommand([
            'provider' => AccountProvider::LDAP,
            'uid'      => $uid,
            'email'    => 'earlene.doyle@example.com',
            'fullname' => 'Earlene Doyle',
        ]);

        $this->commandbus->handle($command);

        $user = $repository->findOneBy([
            'accountProvider' => AccountProvider::LDAP,
            'accountUid'      => $uid,
        ]);

        self::assertInstanceOf(User::class, $user);
        self::assertEquals($id, $user->id);
        self::assertEquals('earlene.doyle@example.com', $user->email);
        self::assertEquals('Earlene Doyle', $user->fullname);
        self::assertEquals($locale, $user->locale);
        self::assertEquals($theme, $user->theme);
    }

    /**
     * @expectedException \League\Tactician\Bundle\Middleware\InvalidCommandException
     */
    public function testBadRequest()
    {
        $command = new RegisterExternalAccountCommand();

        $this->commandbus->handle($command);
    }
}
