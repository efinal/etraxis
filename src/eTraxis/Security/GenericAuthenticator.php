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

/**
 * Authenticates user against the database.
 */
class GenericAuthenticator extends AbstractAuthenticator
{
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
