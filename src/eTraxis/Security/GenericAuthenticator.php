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

use Pignus\Authenticator\AbstractAuthenticator;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Authenticates user against the database.
 */
class GenericAuthenticator extends AbstractAuthenticator
{
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
