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

namespace eTraxis\CommandBus\User;

use eTraxis\CommandBus\CommandTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Increases locks count for specified account.
 *
 * @property string $username Username to lock.
 */
class LockAccountCommand
{
    use CommandTrait;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max="254")
     * @Assert\Email
     */
    public $username;
}
