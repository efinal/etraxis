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

use Doctrine\ORM\Mapping as ORM;
use eTraxis\Dictionary\AccountProvider;

/**
 * Trait to support external accounts (LDAP, OAuth, etc).
 */
trait ExternalAccountTrait
{
    /**
     * @var string Account provider (see the "AccountProvider" dictionary).
     *
     * @ORM\Column(name="account_provider", type="string", length=20)
     */
    protected $accountProvider;

    /**
     * @var string Account UID as in the external provider's system.
     *
     * @ORM\Column(name="account_uid", type="string", length=128)
     */
    protected $accountUid;

    /**
     * Checks whether the account is external.
     *
     * @return bool
     */
    public function isAccountExternal(): bool
    {
        return $this->accountProvider !== AccountProvider::ETRAXIS;
    }

    /**
     * Returns ID of the account provider.
     *
     * @return string
     */
    public function getAccountProvider(): string
    {
        return $this->accountProvider;
    }

    /**
     * Sets the account provider.
     *
     * @param string $provider
     *
     * @return self
     */
    public function setAccountProvider(string $provider)
    {
        $this->accountProvider = AccountProvider::has($provider)
            ? $provider
            : AccountProvider::FALLBACK;

        return $this;
    }

    /**
     * Returns account UID as in the external provider's system.
     *
     * @return string
     */
    public function getAccountUid(): string
    {
        return $this->accountUid;
    }

    /**
     * Sets the account UID.
     *
     * @param string $uid
     *
     * @return self
     */
    public function setAccountUid(string $uid)
    {
        $this->accountUid = $uid;

        return $this;
    }
}
