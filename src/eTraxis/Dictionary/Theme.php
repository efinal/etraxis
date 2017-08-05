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
 * UI themes.
 */
class Theme extends StaticDictionary
{
    const FALLBACK = 'azure';

    protected static $dictionary = [
        'allblacks' => 'All Blacks',
        'azure'     => 'Azure',
        'emerald'   => 'Emerald',
        'humanity'  => 'Humanity',
        'mars'      => 'Mars',
    ];
}
