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

class AccountProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        $expected = [
            AccountProvider::ETRAXIS,
            AccountProvider::LDAP,
        ];

        self::assertEquals($expected, AccountProvider::keys());
    }

    public function testFallback()
    {
        self::assertEquals(AccountProvider::ETRAXIS, AccountProvider::FALLBACK);
    }
}
