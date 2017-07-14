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
        $pos = mb_strrpos($commandName, '\\');

        if ($pos === false) {
            throw MissingHandlerException::forCommand($commandName);
        }

        $handler = mb_substr($commandName, 0, $pos) . '\\Handler' . mb_substr($commandName, $pos, -7) . 'Handler';

        return $this->container->get($handler);
    }
}
