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

namespace eTraxis\SharedDomain\Framework\EventBus;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class InvalidEventExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $violation = $this->createMock(ConstraintViolation::class);

        $event      = new Event();
        $violations = new ConstraintViolationList([$violation]);

        $exception = new InvalidEventException($event, $violations);

        self::assertEquals('Validation failed for Symfony\\Component\\EventDispatcher\\Event with 1 violation(s).', $exception->getMessage());
        self::assertEquals($event, $exception->getEvent());
        self::assertEquals($violations, $exception->getViolations());
    }
}
