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

namespace eTraxis\SharedDomain\Framework\CommandBus;

class CommandTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $command = new DummyCommand();

        self::assertEquals(1, $command->property);
    }

    public function testInitialization()
    {
        $command = new DummyCommand(['property' => 2]);

        self::assertEquals(2, $command->property);
    }

    public function testInitializationExtra()
    {
        $command = new DummyCommand(['property' => 2], ['property' => 3]);

        self::assertEquals(3, $command->property);
    }

    public function testInitializationEmptyString()
    {
        $command = new DummyCommand(['property' => '']);

        self::assertNull($command->property);
    }
}
