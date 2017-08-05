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

namespace eTraxis\Framework\Security;

use eTraxis\Domain\Dictionary\AccountProvider;
use eTraxis\Domain\Entity\User;
use eTraxis\Framework\Tests\ReflectionTrait;
use eTraxis\Framework\Tests\TransactionalTestCase;
use Pignus\Provider\GenericUserProvider;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Ldap\Adapter\QueryInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Exception\ConnectionException;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LdapAuthenticatorTest extends TransactionalTestCase
{
    use ReflectionTrait;

    /** @var \Pignus\Model\UserRepositoryInterface */
    protected $repository;

    /** @var \Symfony\Component\Security\Core\User\UserProviderInterface */
    protected $provider;

    /** @var RouterInterface */
    protected $router;

    /** @var SessionInterface */
    protected $session;

    /** @var EncoderFactoryInterface */
    protected $encoders;

    /** @var FirewallMap */
    protected $firewall;

    protected function setUp()
    {
        parent::setUp();

        $this->repository = $this->doctrine->getRepository(User::class);
        $this->provider   = new GenericUserProvider($this->repository);

        $this->router   = $this->createMock(RouterInterface::class);
        $this->session  = $this->createMock(SessionInterface::class);
        $this->encoders = $this->createMock(EncoderFactoryInterface::class);
        $this->firewall = $this->createMock(FirewallMap::class);
    }

    public function testLdapMinimumConfig()
    {
        $expected = [
            'host'       => 'localhost',
            'port'       => 389,
            'encryption' => 'none',
        ];

        $ldap = LdapAuthenticator::ldap('localhost');

        self::assertInstanceOf(LdapInterface::class, $ldap);

        $adapter = $this->getProperty($ldap, 'adapter');
        $config  = $this->getProperty($adapter, 'config');

        self::assertEquals($expected, $config);
    }

    public function testLdapFullConfig()
    {
        $expected = [
            'host'       => 'localhost',
            'port'       => 12345,
            'encryption' => 'tls',
        ];

        $ldap = LdapAuthenticator::ldap('localhost', 12345, 'tls');

        self::assertInstanceOf(LdapInterface::class, $ldap);

        $adapter = $this->getProperty($ldap, 'adapter');
        $config  = $this->getProperty($adapter, 'config');

        self::assertEquals($expected, $config);
    }

    public function testLdapNoConfig()
    {
        $ldap = LdapAuthenticator::ldap();

        self::assertNull($ldap);
    }

    public function testGetUserNew()
    {
        $entry = $this->createMock(Entry::class);
        $entry
            ->method('getAttributes')
            ->willReturn([
                'uid'  => ['newton'],
                'mail' => ['newton@example.com'],
                'cn'   => ['Isaac Newton'],
            ]);

        $query = $this->createMock(QueryInterface::class);
        $query
            ->method('execute')
            ->willReturn([$entry]);

        $ldap = $this->createMock(LdapInterface::class);
        $ldap
            ->method('escape')
            ->willReturn('newton@example.com');
        $ldap
            ->method('query')
            ->willReturn($query);

        $authenticator = new LdapAuthenticator(
            $this->router,
            $this->session,
            $this->encoders,
            $this->firewall,
            $this->commandbus,
            $ldap
        );

        $count = count($this->repository->findAll());

        $credentials = [
            'username' => 'newton@example.com',
            'password' => 'secret',
        ];

        /** @var User $user */
        $user = $authenticator->getUser($credentials, $this->provider);

        self::assertInstanceOf(User::class, $user);
        self::assertEquals(AccountProvider::LDAP, $user->accountProvider);
        self::assertEquals('newton', $user->accountUid);
        self::assertEquals('newton@example.com', $user->email);
        self::assertEquals('Isaac Newton', $user->fullname);
        self::assertCount($count + 1, $this->repository->findAll());
    }

    public function testGetUserExisting()
    {
        $entry = $this->createMock(Entry::class);
        $entry
            ->method('getAttributes')
            ->willReturn([
                'uid'  => ['einstein'],
                'mail' => ['einstein@example.com'],
                'cn'   => ['A. Einstein'],
            ]);

        $query = $this->createMock(QueryInterface::class);
        $query
            ->method('execute')
            ->willReturn([$entry]);

        $ldap = $this->createMock(LdapInterface::class);
        $ldap
            ->method('escape')
            ->willReturn('einstein@example.com');
        $ldap
            ->method('query')
            ->willReturn($query);

        $authenticator = new LdapAuthenticator(
            $this->router,
            $this->session,
            $this->encoders,
            $this->firewall,
            $this->commandbus,
            $ldap
        );

        $count = count($this->repository->findAll());

        $credentials = [
            'username' => 'einstein@example.com',
            'password' => 'secret',
        ];

        /** @var User $user */
        $user = $this->repository->findOneBy(['accountUid' => 'einstein']);

        self::assertInstanceOf(User::class, $user);
        self::assertEquals(AccountProvider::LDAP, $user->accountProvider);
        self::assertEquals('einstein@ldap.forumsys.com', $user->email);
        self::assertEquals('Albert Einstein', $user->fullname);

        /** @var User $user */
        $user = $authenticator->getUser($credentials, $this->provider);

        self::assertInstanceOf(User::class, $user);
        self::assertEquals(AccountProvider::LDAP, $user->accountProvider);
        self::assertEquals('einstein', $user->accountUid);
        self::assertEquals('einstein@example.com', $user->email);
        self::assertEquals('A. Einstein', $user->fullname);
        self::assertCount($count, $this->repository->findAll());
    }

    public function testGetUserIncomplete()
    {
        $entry = $this->createMock(Entry::class);
        $entry
            ->method('getAttributes')
            ->willReturn([
                'mail' => ['newton@example.com'],
                'cn'   => ['Isaac Newton'],
            ]);

        $query = $this->createMock(QueryInterface::class);
        $query
            ->method('execute')
            ->willReturn([$entry]);

        $ldap = $this->createMock(LdapInterface::class);
        $ldap
            ->method('escape')
            ->willReturn('newton@example.com');
        $ldap
            ->method('query')
            ->willReturn($query);

        $authenticator = new LdapAuthenticator(
            $this->router,
            $this->session,
            $this->encoders,
            $this->firewall,
            $this->commandbus,
            $ldap
        );

        $count = count($this->repository->findAll());

        $credentials = [
            'username' => 'newton@example.com',
            'password' => 'secret',
        ];

        /** @var User $user */
        $user = $authenticator->getUser($credentials, $this->provider);

        self::assertNull($user);
        self::assertCount($count, $this->repository->findAll());
    }

    public function testGetUserUnknown()
    {
        $query = $this->createMock(QueryInterface::class);
        $query
            ->method('execute')
            ->willReturn([]);

        $ldap = $this->createMock(LdapInterface::class);
        $ldap
            ->method('escape')
            ->willReturn('newton@example.com');
        $ldap
            ->method('query')
            ->willReturn($query);

        $authenticator = new LdapAuthenticator(
            $this->router,
            $this->session,
            $this->encoders,
            $this->firewall,
            $this->commandbus,
            $ldap
        );

        $count = count($this->repository->findAll());

        $credentials = [
            'username' => 'newton@example.com',
            'password' => 'secret',
        ];

        /** @var User $user */
        $user = $authenticator->getUser($credentials, $this->provider);

        self::assertNull($user);
        self::assertCount($count, $this->repository->findAll());
    }

    /** @expectedException \Symfony\Component\Security\Core\Exception\AuthenticationException */
    public function testGetUserNoLdap()
    {
        $authenticator = new LdapAuthenticator(
            $this->router,
            $this->session,
            $this->encoders,
            $this->firewall,
            $this->commandbus
        );

        $credentials = [
            'username' => 'newton@example.com',
            'password' => 'secret',
        ];

        $authenticator->getUser($credentials, $this->provider);
    }

    public function testCheckCredentialsValid()
    {
        $ldap = $this->createMock(LdapInterface::class);

        $authenticator = new LdapAuthenticator(
            $this->router,
            $this->session,
            $this->encoders,
            $this->firewall,
            $this->commandbus,
            $ldap
        );

        $credentials = [
            'username' => 'newton@example.com',
            'password' => 'secret',
        ];

        /** @var User $user */
        $user = $this->repository->findOneBy(['accountUid' => 'einstein']);

        self::assertTrue($authenticator->checkCredentials($credentials, $user));
    }

    public function testCheckCredentialsWrong()
    {
        $ldap = $this->createMock(LdapInterface::class);
        $ldap
            ->method('bind')
            ->willThrowException(new ConnectionException());

        $authenticator = new LdapAuthenticator(
            $this->router,
            $this->session,
            $this->encoders,
            $this->firewall,
            $this->commandbus,
            $ldap
        );

        $credentials = [
            'username' => 'newton@example.com',
            'password' => 'secret',
        ];

        /** @var User $user */
        $user = $this->repository->findOneBy(['accountUid' => 'einstein']);

        self::assertFalse($authenticator->checkCredentials($credentials, $user));
    }

    /** @expectedException \Symfony\Component\Security\Core\Exception\AuthenticationException */
    public function testCheckCredentialsNoLdap()
    {
        $authenticator = new LdapAuthenticator(
            $this->router,
            $this->session,
            $this->encoders,
            $this->firewall,
            $this->commandbus
        );

        $credentials = [
            'username' => 'newton@example.com',
            'password' => 'secret',
        ];

        /** @var User $user */
        $user = $this->repository->findOneBy(['accountUid' => 'einstein']);

        $authenticator->checkCredentials($credentials, $user);
    }

    public function testOnAuthenticationFailure()
    {
        $authenticator = new LdapAuthenticator(
            $this->router,
            $this->session,
            $this->encoders,
            $this->firewall,
            $this->commandbus
        );

        $request   = new Request();
        $exception = new AuthenticationException();

        self::assertNull($authenticator->onAuthenticationFailure($request, $exception));
    }

    public function testGetLoginUrl()
    {
        $authenticator = new LdapAuthenticator(
            $this->router,
            $this->session,
            $this->encoders,
            $this->firewall,
            $this->commandbus
        );

        $router = $this->client->getContainer()->get('router');

        self::assertEquals('/login', $this->callMethod($authenticator, 'getLoginUrl', [$router, 'main']));
    }

    public function testGetDefaultUrl()
    {
        $authenticator = new LdapAuthenticator(
            $this->router,
            $this->session,
            $this->encoders,
            $this->firewall,
            $this->commandbus
        );

        $router = $this->client->getContainer()->get('router');

        self::assertEquals('/', $this->callMethod($authenticator, 'getDefaultUrl', [$router, 'main']));
    }
}
