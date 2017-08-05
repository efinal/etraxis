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

namespace eTraxis\Framework\CommandBus;

class TimingMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    public function testTiming()
    {
        $logger  = new DummyLogger();
        $command = new \stdClass();

        $middleware = new TimingMiddleware($logger);
        $middleware->execute($command, function () {
        });

        self::assertTrue($logger->contains('Command processing time'));
    }
}
