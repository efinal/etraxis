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

class LocaleTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        $expected = [
            'bg',
            'cs',
            'de',
            'en_AU',
            'en_CA',
            'en_GB',
            'en_NZ',
            'en_US',
            'es',
            'fr',
            'hu',
            'it',
            'ja',
            'lv',
            'nl',
            'pl',
            'pt_BR',
            'ro',
            'ru',
            'sv',
            'tr',
        ];

        self::assertEquals($expected, Locale::keys());
    }
}
