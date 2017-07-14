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

namespace eTraxis\Dictionary;

class DatabasePlatformTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        $expected = [
            DatabasePlatform::MYSQL,
            DatabasePlatform::POSTGRESQL,
        ];

        self::assertEquals($expected, DatabasePlatform::keys());
    }
}
