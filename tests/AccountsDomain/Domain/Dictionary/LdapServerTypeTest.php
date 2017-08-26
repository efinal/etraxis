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

namespace eTraxis\AccountsDomain\Domain\Dictionary;

class LdapServerTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        $expected = [
            LdapServerType::POSIX,
            LdapServerType::WIN2000,
            LdapServerType::WINNT,
        ];

        self::assertEquals($expected, LdapServerType::keys());
    }

    public function testFallback()
    {
        self::assertEquals(LdapServerType::POSIX, LdapServerType::FALLBACK);
    }

    public function testAttributes()
    {
        self::assertEquals('uid', LdapServerType::get(LdapServerType::POSIX));
        self::assertEquals('userPrincipalName', LdapServerType::get(LdapServerType::WIN2000));
        self::assertEquals('sAMAccountName', LdapServerType::get(LdapServerType::WINNT));
    }
}
