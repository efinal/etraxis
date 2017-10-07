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

namespace eTraxis\AccountsDomain\Framework\Authentication;

use eTraxis\AccountsDomain\Application\Event\LoginFailedEvent;
use eTraxis\AccountsDomain\Application\Event\LoginSuccessfulEvent;
use eTraxis\SharedDomain\Framework\EventBus\EventBusInterface;
use Pignus\Authenticator\AbstractAuthenticator;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Authenticates user against the database.
 */
class GenericAuthenticator extends AbstractAuthenticator
{
    protected $eventbus;

    /**
     * Dependency Injection constructor.
     *
     * @param RouterInterface         $router
     * @param SessionInterface        $session
     * @param TranslatorInterface     $translator
     * @param EncoderFactoryInterface $encoders
     * @param FirewallMap             $firewalls
     * @param EventBusInterface       $eventbus
     */
    public function __construct(
        RouterInterface         $router,
        SessionInterface        $session,
        TranslatorInterface     $translator,
        EncoderFactoryInterface $encoders,
        FirewallMap             $firewalls,
        EventBusInterface       $eventbus
    )
    {
        parent::__construct($router, $session, $translator, $encoders, $firewalls);

        $this->eventbus = $eventbus;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var \eTraxis\AccountsDomain\Domain\Model\User $user */
        $user = parent::getUser($credentials, $userProvider);

        if ($user->isAccountExternal) {
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

            $event = new LoginSuccessfulEvent([
                'username' => $credentials['username'],
            ]);

            $this->eventbus->notify($event);
        }
        catch (AuthenticationException $e) {

            $event = new LoginFailedEvent([
                'username' => $credentials['username'],
            ]);

            $this->eventbus->notify($event);

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
