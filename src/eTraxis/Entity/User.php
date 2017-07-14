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

namespace eTraxis\Entity;

use Doctrine\ORM\Mapping as ORM;
use eTraxis\Dictionary\AccountProvider;
use eTraxis\Security\ExternalAccountTrait;
use Pignus\Model as Pignus;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Webinarium\PropertyTrait;

/**
 * User.
 *
 * @ORM\Table(
 *     name="users",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"account_provider", "account_uid"})
 *     })
 * @ORM\Entity(repositoryClass="eTraxis\Repository\UserRepository")
 * @Assert\UniqueEntity(fields={"email"}, message="user.conflict.email")
 *
 * @property-read int    $id
 * @property      string $email
 * @property      string $password
 * @property      string $fullname
 * @property      string $description
 * @property      bool   $isAdmin
 */
class User implements AdvancedUserInterface, EncoderAwareInterface
{
    use PropertyTrait;
    use Pignus\UserTrait;
    use ExternalAccountTrait;
    use Pignus\DisableAccountTrait;
    use Pignus\ExpireAccountTrait;
    use Pignus\ExpirePasswordTrait;
    use Pignus\LockAccountTrait;

    // Roles.
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER  = 'ROLE_USER';

    // Constraints.
    const MAX_EMAIL       = 254;
    const MAX_FULLNAME    = 50;
    const MAX_DESCRIPTION = 100;

    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    /**
     * @var string Email address (also used as a username).
     *
     * @ORM\Column(name="email", type="string", length=254, unique=true)
     */
    protected $email;

    /**
     * @var string User's password (hash).
     *
     * @ORM\Column(name="password", type="string", length=60, nullable=true)
     */
    protected $password;

    /**
     * @var string Full name.
     *
     * @ORM\Column(name="fullname", type="string", length=50)
     */
    protected $fullname;

    /**
     * @var string Description of the user.
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=true)
     */
    protected $description;

    /**
     * @var string User's role (see "User::ROLE_..." constants).
     *
     * @ORM\Column(name="role", type="string", length=20)
     */
    protected $role;

    /**
     * Sets default values to required fields.
     */
    public function __construct()
    {
        $this->role            = self::ROLE_USER;
        $this->accountProvider = AccountProvider::ETRAXIS;
        $this->accountUid      = Uuid::uuid4()->getHex();
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return [$this->role];
    }

    /**
     * {@inheritdoc}
     *
     * @todo Remove in 4.1
     */
    public function getEncoderName()
    {
        switch (mb_strlen($this->password)) {
            case 32:
                return 'legacy.md5';
            case 28:
                return 'legacy.sha1';
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function getters(): array
    {
        return [

            'isAdmin' => function () {
                return $this->role === self::ROLE_ADMIN;
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function setters(): array
    {
        return [

            'isAdmin' => function (bool $value) {
                $this->role = $value ? self::ROLE_ADMIN : self::ROLE_USER;
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function canPasswordBeExpired(): bool
    {
        return !$this->isAccountExternal();
    }

    /**
     * {@inheritdoc}
     */
    protected function canAccountBeLocked(): bool
    {
        return !$this->isAccountExternal();
    }
}
