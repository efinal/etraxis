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

namespace eTraxis\SharedDomain\Framework\Tests;

use eTraxis\AccountsDomain\Domain\Model\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as SymfonyWebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Extended web test case with an autoboot kernel and few helpers.
 */
class WebTestCase extends SymfonyWebTestCase
{
    /** @var \Symfony\Bundle\FrameworkBundle\Client */
    protected $client;

    /** @var \Symfony\Bridge\Doctrine\RegistryInterface */
    protected $doctrine;

    /** @var \League\Tactician\CommandBus */
    protected $commandbus;

    /**
     * Boots the kernel and retrieve most often used services.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();

        $this->doctrine   = $this->client->getContainer()->get('doctrine');
        $this->commandbus = $this->client->getContainer()->get('tactician.commandbus');
    }

    /**
     * Makes AJAX request to specified URI.
     *
     * @param string $method
     * @param string $uri
     * @param array  $parameters
     * @param array  $headers
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function ajax(string $method, string $uri, array $parameters = [], array $headers = [])
    {
        $headers['HTTP_X-Requested-With'] = 'XMLHttpRequest';

        $this->client->request($method, $uri, $parameters, [], $headers);

        return $this->client->getResponse();
    }

    /**
     * Emulates authentication of specified user.
     *
     * @param string $email Login.
     *
     * @return null|User Whether user was authenticated.
     */
    protected function loginAs(string $email)
    {
        /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $session */
        $session = $this->client->getContainer()->get('session');

        /** @var \Pignus\Model\UserRepositoryInterface $repository */
        $repository = $this->client->getContainer()->get('doctrine')->getRepository(User::class);

        /** @var User $user */
        if ($user = $repository->findOneByUsername($email)) {

            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->client->getContainer()->get('security.token_storage')->setToken($token);

            $session->set('_security_main', serialize($token));
            $session->save();

            $cookie = new Cookie($session->getName(), $session->getId());
            $this->client->getCookieJar()->set($cookie);
        }

        return $user;
    }
}
