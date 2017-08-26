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

use Dictionary\StaticDictionary;

/**
 * Supported account providers.
 */
class AccountProvider extends StaticDictionary
{
    const FALLBACK = self::ETRAXIS;

    const ETRAXIS = 'etraxis';
    const LDAP    = 'ldap';

    protected static $dictionary = [
        self::ETRAXIS => 'eTraxis',
        self::LDAP    => 'LDAP',
    ];
}
