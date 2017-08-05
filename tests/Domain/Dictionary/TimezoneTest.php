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

namespace eTraxis\Domain\Dictionary;

class TimezoneTest extends \PHPUnit_Framework_TestCase
{
    public function testCountries()
    {
        $countries = Timezone::getCountries();

        self::assertArrayNotHasKey('??', $countries);
        self::assertArrayHasKey('NZ', $countries);
        self::assertEquals('New Zealand', $countries['NZ']);
    }

    public function testCities()
    {
        $expected = [
            'Australia/Adelaide'    => 'Adelaide',
            'Australia/Brisbane'    => 'Brisbane',
            'Australia/Broken_Hill' => 'Broken Hill',
            'Australia/Currie'      => 'Currie',
            'Australia/Darwin'      => 'Darwin',
            'Australia/Eucla'       => 'Eucla',
            'Australia/Hobart'      => 'Hobart',
            'Australia/Lindeman'    => 'Lindeman',
            'Australia/Lord_Howe'   => 'Lord Howe',
            'Antarctica/Macquarie'  => 'Macquarie',
            'Australia/Melbourne'   => 'Melbourne',
            'Australia/Perth'       => 'Perth',
            'Australia/Sydney'      => 'Sydney',
        ];

        self::assertEquals($expected, Timezone::getCities('AU'));
    }

    public function testDictionary()
    {
        self::assertEquals(timezone_identifiers_list(), Timezone::keys());
        self::assertEquals(timezone_identifiers_list(), Timezone::values());
    }
}
