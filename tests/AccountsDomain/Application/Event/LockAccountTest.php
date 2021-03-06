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

namespace eTraxis\AccountsDomain\Application\Event;

use eTraxis\AccountsDomain\Application\EventListener\LockAccountListener;
use eTraxis\AccountsDomain\Domain\Model\User;
use eTraxis\SharedDomain\Framework\Tests\TransactionalTestCase;

class LockAccountTest extends TransactionalTestCase
{
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var \Doctrine\ORM\EntityManagerInterface */
    protected $manager;

    protected function setUp()
    {
        parent::setUp();

        $this->logger  = $this->client->getContainer()->get('logger');
        $this->manager = $this->doctrine->getManager();
    }

    public function testLockUser()
    {
        /** @var \Pignus\Model\UserRepositoryInterface $repository */
        $repository = $this->doctrine->getRepository(User::class);

        $event = new LoginFailedEvent([
            'username' => 'artem@example.com',
        ]);

        $handler = new LockAccountListener($this->logger, $this->manager, 2, 10);

        // first time
        $handler->handle($event);

        /** @var User $user */
        $user = $repository->findOneByUsername('artem@example.com');
        self::assertTrue($user->isAccountNonLocked());

        // second time
        $handler->handle($event);

        $user = $repository->findOneByUsername('artem@example.com');
        self::assertFalse($user->isAccountNonLocked());
    }

    public function testLockUserForever()
    {
        /** @var \Pignus\Model\UserRepositoryInterface $repository */
        $repository = $this->doctrine->getRepository(User::class);

        $event = new LoginFailedEvent([
            'username' => 'artem@example.com',
        ]);

        $handler = new LockAccountListener($this->logger, $this->manager, 2, null);

        // first time
        $handler->handle($event);

        /** @var User $user */
        $user = $repository->findOneByUsername('artem@example.com');
        self::assertTrue($user->isAccountNonLocked());

        // second time
        $handler->handle($event);

        $user = $repository->findOneByUsername('artem@example.com');
        self::assertFalse($user->isAccountNonLocked());
    }

    public function testNoLock()
    {
        /** @var \Pignus\Model\UserRepositoryInterface $repository */
        $repository = $this->doctrine->getRepository(User::class);

        $event = new LoginFailedEvent([
            'username' => 'artem@example.com',
        ]);

        $handler = new LockAccountListener($this->logger, $this->manager, null, null);

        // first time
        $handler->handle($event);

        /** @var User $user */
        $user = $repository->findOneByUsername('artem@example.com');
        self::assertTrue($user->isAccountNonLocked());

        // second time
        $handler->handle($event);

        $user = $repository->findOneByUsername('artem@example.com');
        self::assertTrue($user->isAccountNonLocked());
    }
}
