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

use League\Tactician\Middleware;
use Psr\Log\LoggerInterface;

/**
 * Middleware to calculate command processing time.
 */
class TimingMiddleware implements Middleware
{
    protected $logger;

    /**
     * Dependency Injection constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($command, callable $next)
    {
        try {
            $start = microtime(true);
            $value = $next($command);
        }
        finally {
            $stop = microtime(true);
            $this->logger->debug('Command processing time', [$stop - $start]);
        }

        return $value ?? null;
    }
}
