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

namespace eTraxis\CommandBus;

use Symfony\Component\Validator\Constraints as Assert;

class DummyCommand
{
    use CommandTrait;

    /**
     * @Assert\Range(min="1", max="100")
     */
    public $property = 1;
}
