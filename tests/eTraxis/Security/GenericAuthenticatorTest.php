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

use eTraxis\Entity\User;
use eTraxis\Tests\ReflectionTrait;
use eTraxis\Tests\TransactionalTestCase;
use Pignus\Provider\GenericUserProvider;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class GenericAuthenticatorTest extends TransactionalTestCase
{
    use ReflectionTrait;

    /** @var GenericAuthenticator $authenticator */
    protected $authenticator;

    protected function setUp()
    {
        parent::setUp();

        /** @var RouterInterface $router */
        $router = $this->createMock(RouterInterface::class);

        /** @var SessionInterface $session */
        $session = $this->createMock(SessionInterface::class);

        /** @var EncoderFactoryInterface $encoders */
        $encoders = $this->client->getContainer()->get('security.encoder_factory');

        /** @var FirewallMap $firewall */
        $firewall = $this->createMock(FirewallMap::class);

        $this->authenticator = new GenericAuthenticator($router, $session, $encoders, $firewall);
    }

    public function testGetUser()
    {
        /** @var \Pignus\Model\UserRepositoryInterface $repository */
        $repository = $this->doctrine->getRepository(User::class);
        $provider   = new GenericUserProvider($repository);

        $credentials = [
            'username' => 'artem@example.com',
            'password' => 'secret',
        ];

        $user = $this->authenticator->getUser($credentials, $provider);

        self::assertInstanceOf(User::class, $user);
        self::assertEquals('artem@example.com', $user->getUsername());
    }

    /** @expectedException \Symfony\Component\Security\Core\Exception\AuthenticationException */
    public function testGetUnknownUser()
    {
        /** @var \Pignus\Model\UserRepositoryInterface $repository */
        $repository = $this->doctrine->getRepository(User::class);
        $provider   = new GenericUserProvider($repository);

        $credentials = [
            'username' => '404@example.com',
            'password' => 'secret',
        ];

        $this->authenticator->getUser($credentials, $provider);
    }

    /** @expectedException \Symfony\Component\Security\Core\Exception\AuthenticationException */
    public function testGetExternalUser()
    {
        /** @var \Pignus\Model\UserRepositoryInterface $repository */
        $repository = $this->doctrine->getRepository(User::class);
        $provider   = new GenericUserProvider($repository);

        $credentials = [
            'username' => 'einstein@ldap.forumsys.com',
            'password' => 'secret',
        ];

        $this->authenticator->getUser($credentials, $provider);
    }

    public function testGetLoginUrl()
    {
        $router = $this->client->getContainer()->get('router');

        self::assertEquals('/login', $this->callMethod($this->authenticator, 'getLoginUrl', [$router, 'main']));
    }

    public function testGetDefaultUrl()
    {
        $router = $this->client->getContainer()->get('router');

        self::assertEquals('/', $this->callMethod($this->authenticator, 'getDefaultUrl', [$router, 'main']));
    }
}
