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

use Symfony\Component\DependencyInjection\ContainerInterface;

class HandlerLocatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \League\Tactician\Handler\Locator\HandlerLocator */
    protected $locator;

    protected function setUp()
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('get')
            ->willReturnMap([
                ['CommandBus\\Handler\\SomeHandler', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, 'CommandBus\\Handler\\SomeHandler'],
            ]);

        $this->locator = new HandlerLocator();
        $this->locator->setContainer($container);
    }

    public function testSuccess()
    {
        $command = 'CommandBus\\SomeCommand';
        $handler = 'CommandBus\\Handler\\SomeHandler';

        self::assertEquals($handler, $this->locator->getHandlerForCommand($command));
    }

    /**
     * @expectedException \League\Tactician\Exception\MissingHandlerException
     */
    public function testException()
    {
        $this->locator->getHandlerForCommand('SomeCommand');
    }
}
