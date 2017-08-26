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

namespace eTraxis\SharedDomain\Framework\EventSubscriber;

use League\Tactician\Bundle\Middleware\InvalidCommandException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Handles any unhandled exception.
 */
class UnhandledException implements EventSubscriberInterface
{
    protected $logger;
    protected $normalizer;

    /**
     * Dependency Injection constructor.
     *
     * @param LoggerInterface     $logger
     * @param NormalizerInterface $normalizer
     */
    public function __construct(LoggerInterface $logger, NormalizerInterface $normalizer)
    {
        $this->logger     = $logger;
        $this->normalizer = $normalizer;
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

            if ($exception instanceof InvalidCommandException) {
                $violations = $this->normalizer->normalize($exception->getViolations());
                $this->logger->critical('Validation exception', [$exception->getMessage(), $violations]);
                $response = new JsonResponse($violations, JsonResponse::HTTP_BAD_REQUEST);
                $event->setResponse($response);
            }
            elseif ($exception instanceof HttpException) {
                $message = $exception->getMessage() ?: JsonResponse::$statusTexts[$exception->getStatusCode()];
                $this->logger->error('HTTP exception', [$message]);
                $response = new JsonResponse($message, $exception->getStatusCode());
                $event->setResponse($response);
            }
        }
    }
}
