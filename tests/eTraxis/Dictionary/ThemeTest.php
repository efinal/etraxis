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

class ThemeTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        $expected = [
            'allblacks',
            'azure',
            'emerald',
            'humanity',
            'mars',
            'nexada',
        ];

        self::assertEquals($expected, Theme::keys());
    }
}
