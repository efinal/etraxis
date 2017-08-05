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

use eTraxis\CommandBus\User\RegisterExternalAccountCommand;
use eTraxis\Dictionary\AccountProvider;
use eTraxis\Dictionary\LdapServerType;
use eTraxis\Entity\User;
use League\Tactician\CommandBus;
use Pignus\Authenticator\AbstractAuthenticator;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Authenticates user against LDAP server.
 */
class LdapAuthenticator extends AbstractAuthenticator
{
    protected $commandbus;
    protected $ldap;
    protected $type;
    protected $user;
    protected $password;
    protected $basedn;

    /**
     * Dependency Injection constructor.
     *
     * @param RouterInterface         $router
     * @param SessionInterface        $session
     * @param EncoderFactoryInterface $encoders
     * @param FirewallMap             $firewalls
     * @param CommandBus              $commandbus
     * @param LdapInterface           $ldap
     * @param string                  $type
     * @param string                  $user
     * @param string                  $password
     * @param string                  $basedn
     */
    public function __construct(
        RouterInterface         $router,
        SessionInterface        $session,
        EncoderFactoryInterface $encoders,
        FirewallMap             $firewalls,
        CommandBus              $commandbus,
        LdapInterface           $ldap     = null,
        string                  $type     = null,
        string                  $user     = null,
        string                  $password = null,
        string                  $basedn   = null
    )
    {
        parent::__construct($router, $session, $encoders, $firewalls);

        $this->commandbus = $commandbus;
        $this->ldap       = $ldap;
        $this->type       = $type;
        $this->user       = $user;
        $this->password   = $password;
        $this->basedn     = $basedn;
    }

    /**
     * Returns LDAP client if it's configured.
     *
     * @param string $host
     * @param string $port
     * @param string $encryption
     *
     * @return null|LdapInterface
     */
    public static function ldap(string $host = null, string $port = null, string $encryption = null)
    {
        if ($host === null) {
            return null;
        }

        return Ldap::create('ext_ldap', [
            'host'       => $host,
            'port'       => $port ?? 389,
            'encryption' => $encryption ?? 'none',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if ($this->ldap === null) {
            throw new AuthenticationException('Bad credentials.');
        }

        $this->ldap->bind($this->user, $this->password);

        $username = $this->ldap->escape($credentials['username'], null, LdapInterface::ESCAPE_FILTER);
        $query    = $this->ldap->query($this->basedn, sprintf('(mail=%s)', $username));
        $entries  = $query->execute();

        if (count($entries) === 0) {
            return null;
        }

        $attributes = $entries[0]->getAttributes();

        $uid      = $attributes[LdapServerType::get($this->type)][0] ?? null;
        $fullname = $attributes['cn'][0] ?? null;

        if ($uid === null || $fullname === null) {
            return null;
        }

        $command = new RegisterExternalAccountCommand([
            'provider' => AccountProvider::LDAP,
            'uid'      => $uid,
            'email'    => $credentials['username'],
            'fullname' => $fullname,
        ]);

        $this->commandbus->handle($command);

        return $userProvider->loadUserByUsername($credentials['username']);
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        if ($this->ldap === null) {
            throw new AuthenticationException('Bad credentials.');
        }

        try {
            /** @var User $user */
            $dn = sprintf('%s=%s,%s', LdapServerType::get($this->type), $user->accountUid, $this->basedn);
            $this->ldap->bind($dn, $credentials['password']);
        }
        catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return null;
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
