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

namespace eTraxis\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Handles any unhandled exception.
 */
class UnhandledException implements EventSubscriberInterface
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
    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onException',
        ];
    }

    /**
     * In case of AJAX: logs the exception and converts it into JSON response with HTTP error.
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onException(GetResponseForExceptionEvent $event)
    {
        $request   = $event->getRequest();
        $exception = $event->getException();

        if ($request->isXmlHttpRequest()) {

            if ($exception instanceof HttpException) {
                $message = $exception->getMessage() ?: JsonResponse::$statusTexts[$exception->getStatusCode()];
                $this->logger->error('HTTP exception', [$message]);
                $response = new JsonResponse($message, $exception->getStatusCode());
                $event->setResponse($response);
            }
        }
    }
}
