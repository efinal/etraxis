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

namespace eTraxis\CommandBus;

use Psr\Log\AbstractLogger;

class DummyLogger extends AbstractLogger
{
    protected $logs;

    public function log($level, $message, array $context = [])
    {
        $this->logs .= $message;
    }

    public function contains($message)
    {
        return mb_strpos($this->logs, $message) !== false;
    }
}
