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

use eTraxis\CommandBus\DummyCommand;
use eTraxis\Serializer\ConstraintViolationsNormalizer;
use League\Tactician\Bundle\Middleware\InvalidCommandException;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class UnhandledExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSubscribedEvents()
    {
        $expected = [
            'kernel.exception',
        ];

        self::assertEquals($expected, array_keys(UnhandledException::getSubscribedEvents()));
    }

    public function testMasterRequest()
    {
        $request = new Request();

        /** @var HttpKernelInterface $kernel */
        $kernel = $this->createMock(HttpKernelInterface::class);

        $event = new GetResponseForExceptionEvent(
            $kernel,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new HttpException(Response::HTTP_NOT_FOUND, 'Unknown username.')
        );

        $logger     = new NullLogger();
        $normalizer = new ConstraintViolationsNormalizer();
        $exception  = new UnhandledException($logger, $normalizer);

        $exception->onException($event);

        $response = $event->getResponse();

        self::assertNull($response);
    }

    public function testInvalidCommandException()
    {
        $expected = [
            [
                'property' => 'property',
                'value'    => '0',
                'message'  => 'This value should be "1" or more.',
            ],
        ];

        $request = new Request();
        $request->headers->add(['X-Requested-With' => 'XMLHttpRequest']);

        /** @var HttpKernelInterface $kernel */
        $kernel = $this->createMock(HttpKernelInterface::class);

        $command = new DummyCommand(['property' => '0']);

        $violations = new ConstraintViolationList();
        $violations->add(new ConstraintViolation(
            'This value should be "1" or more.',
            'This value should be {{ limit }} or more.',
            [
                '{{ value }}' => '"0"',
                '{{ limit }}' => '"1"',
            ],
            new DummyCommand(['property' => 0]),
            'property',
            '0'
        ));

        $event = new GetResponseForExceptionEvent(
            $kernel,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            InvalidCommandException::onCommand($command, $violations)
        );

        $logger     = new NullLogger();
        $normalizer = new ConstraintViolationsNormalizer();
        $exception  = new UnhandledException($logger, $normalizer);

        $exception->onException($event);

        $response = $event->getResponse();
        $content  = $response->getContent();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertEquals($expected, json_decode($content, true));
    }

    public function testHttpException()
    {
        $request = new Request();
        $request->headers->add(['X-Requested-With' => 'XMLHttpRequest']);

        /** @var HttpKernelInterface $kernel */
        $kernel = $this->createMock(HttpKernelInterface::class);

        $event = new GetResponseForExceptionEvent(
            $kernel,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new AccessDeniedHttpException('You are not allowed for this action.')
        );

        $logger     = new NullLogger();
        $normalizer = new ConstraintViolationsNormalizer();
        $exception  = new UnhandledException($logger, $normalizer);

        $exception->onException($event);

        $response = $event->getResponse();
        $content  = $response->getContent();

        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        self::assertEquals('"You are not allowed for this action."', $content);
    }

    public function testException()
    {
        $request = new Request();
        $request->headers->add(['X-Requested-With' => 'XMLHttpRequest']);

        /** @var HttpKernelInterface $kernel */
        $kernel = $this->createMock(HttpKernelInterface::class);

        $event = new GetResponseForExceptionEvent(
            $kernel,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new \Exception('Something went wrong.')
        );

        $logger     = new NullLogger();
        $normalizer = new ConstraintViolationsNormalizer();
        $exception  = new UnhandledException($logger, $normalizer);

        $exception->onException($event);

        self::assertNull($event->getResponse());
    }
}
