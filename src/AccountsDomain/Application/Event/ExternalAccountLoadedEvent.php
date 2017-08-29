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

namespace eTraxis\AccountsDomain\Application\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Validator\Constraints as Assert;
use Webinarium\DataTransferObjectTrait;

/**
 * External account info is loaded.
 *
 * @property string $provider Account provider.
 * @property string $uid      Account UID as in the provider's system.
 * @property string $email    Email address as in the provider's system.
 * @property string $fullname Full name as in the provider's system.
 */
class ExternalAccountLoadedEvent extends Event
{
    use DataTransferObjectTrait;

    /**
     * @Assert\NotBlank
     * @Assert\Choice(callback={"eTraxis\AccountsDomain\Domain\Dictionary\AccountProvider", "keys"}, strict=true)
     */
    public $provider;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max="128")
     */
    public $uid;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max="254")
     * @Assert\Email
     */
    public $email;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max="50")
     */
    public $fullname;
}
