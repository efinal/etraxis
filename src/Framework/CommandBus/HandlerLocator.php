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

use League\Tactician\Exception\MissingHandlerException;
use League\Tactician\Handler\Locator\HandlerLocator as HandlerLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Custom command handlers locator.
 */
class HandlerLocator implements ContainerAwareInterface, HandlerLocatorInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function getHandlerForCommand($commandName)
    {
        // 'eTraxis\Application\Command\...\FooCommand'
        if (!preg_match('/^eTraxis\\\\Application\\\\Command\\\\(.+)\\\\(.+)Command$/s', $commandName)) {
            throw MissingHandlerException::forCommand($commandName);
        }

        // 'eTraxis\Application\Command\...\FooCommand' => 'eTraxis\Application\Handler\...\FooHandler'
        $handler = mb_substr($commandName, 0, -7) . 'Handler';
        $handler = str_replace('eTraxis\\Application\\Command\\', 'eTraxis\\Application\\Handler\\', $handler);

        return $this->container->get($handler);
    }
}
