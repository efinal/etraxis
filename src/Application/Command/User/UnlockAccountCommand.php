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

namespace eTraxis\Application\Command\User;

use eTraxis\Framework\CommandBus\CommandTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Clears locks count for specified account.
 *
 * @property string $username Username to lock.
 */
class UnlockAccountCommand
{
    use CommandTrait;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max="254")
     * @Assert\Email
     */
    public $username;
}
