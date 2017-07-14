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

use Dictionary\StaticDictionary;

/**
 * LDAP server type by host's operating system.
 */
class LdapServerType extends StaticDictionary
{
    const FALLBACK = self::POSIX;

    const POSIX   = 'posix';
    const WIN2000 = 'win2000';
    const WINNT   = 'winnt';

    protected static $dictionary = [
        self::POSIX   => 'uid',
        self::WIN2000 => 'userPrincipalName',   // Windows 2000 Server and above
        self::WINNT   => 'sAMAccountName',      // Windows NT 4.0, Windows 95, Windows 98
    ];
}
