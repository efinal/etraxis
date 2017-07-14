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

use eTraxis\CommandBus\User\LockAccountCommand;
use eTraxis\CommandBus\User\UnlockAccountCommand;
use League\Tactician\CommandBus;
use Pignus\Authenticator\AbstractAuthenticator;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Authenticates user against the database.
 */
class GenericAuthenticator extends AbstractAuthenticator
{
    protected $commandbus;

    /**
     * Dependency Injection constructor.
     *
     * @param RouterInterface         $router
     * @param SessionInterface        $session
     * @param EncoderFactoryInterface $encoders
     * @param FirewallMap             $firewalls
     * @param CommandBus              $commandbus
     */
    public function __construct(
        RouterInterface         $router,
        SessionInterface        $session,
        EncoderFactoryInterface $encoders,
        FirewallMap             $firewalls,
        CommandBus              $commandbus)
    {
        parent::__construct($router, $session, $encoders, $firewalls);

        $this->commandbus = $commandbus;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var \eTraxis\Entity\User $user */
        $user = parent::getUser($credentials, $userProvider);

        if ($user->isAccountExternal()) {
            throw new AuthenticationException('Bad credentials.');
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        try {
            parent::checkCredentials($credentials, $user);

            $command = new UnlockAccountCommand([
                'username' => $credentials['username'],
            ]);

            $this->commandbus->handle($command);
        }
        catch (AuthenticationException $e) {

            $command = new LockAccountCommand([
                'username' => $credentials['username'],
            ]);

            $this->commandbus->handle($command);

            throw $e;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function getLoginUrl(RouterInterface $router, string $firewall): string
    {
        return $router->generate('pignus.login');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultUrl(RouterInterface $router, string $firewall): string
    {
        return $router->generate('homepage');
    }
}
