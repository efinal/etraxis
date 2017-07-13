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

namespace eTraxis\Composer;

use Composer\Script\Event;

/**
 * Composer script to process parameters file.
 */
class ParameterHandler
{
    /**
     * Updates the 'secret' parameter.
     *
     * @param Event $event
     */
    public static function updateSecret(Event $event)
    {
        $extras = $event->getComposer()->getPackage()->getExtra();

        $file         = $extras['incenteev-parameters']['file'];
        $parameterKey = $extras['incenteev-parameters']['parameter-key'] ?? 'parameters';

        system("./bin/console etraxis:secret {$file} {$parameterKey}");
    }
}
