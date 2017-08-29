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

use eTraxis\SharedDomain\Framework\CommandBus\DummyLogger;
use eTraxis\SharedDomain\Framework\Tests\WebTestCase;
use Psr\Log\NullLogger;

class EventBusTest extends WebTestCase
{
    public function testTiming()
    {
        $logger = new DummyLogger();

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher */
        $dispatcher = $this->client->getContainer()->get('event_dispatcher');

        /** @var \Symfony\Component\Validator\Validator\ValidatorInterface $validator */
        $validator = $this->client->getContainer()->get('validator');

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        $eventbus = new EventBus($logger, $dispatcher, $validator, $manager);
        $eventbus->notify(new DummyEvent());

        self::assertTrue($logger->contains('Event processing time'));
    }

    /**
     * @expectedException \eTraxis\SharedDomain\Framework\EventBus\InvalidEventException
     */
    public function testViolations()
    {
        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher */
        $dispatcher = $this->client->getContainer()->get('event_dispatcher');

        /** @var \Symfony\Component\Validator\Validator\ValidatorInterface $validator */
        $validator = $this->client->getContainer()->get('validator');

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        $eventbus = new EventBus(new NullLogger(), $dispatcher, $validator, $manager);
        $eventbus->notify(new DummyEvent([
            'property' => 0,
        ]));
    }
}
