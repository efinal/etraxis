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

namespace eTraxis\Service;

use Psr\Log\NullLogger;

class MailerServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var \Twig_Environment */
    protected $twig;

    /** @var \Swift_Mailer */
    protected $mailer;

    protected function setUp()
    {
        $this->logger = new NullLogger();

        $loader     = new \Twig_Loader_Filesystem(getcwd() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'views');
        $this->twig = new \Twig_Environment($loader);

        $transport    = new \Swift_NullTransport();
        $this->mailer = new \Swift_Mailer($transport);
    }

    public function testFullSender()
    {
        /** @noinspection PhpParamsInspection */
        $service = new MailerService($this->logger, $this->twig, $this->mailer, 'noreply@example.com', 'Test Mailer');

        $result = $service->send(
            'anna@example.com',
            'Anna Rodygina',
            'Test subject',
            'email.html.twig',
            ['message' => 'Test message'],
            function (\Swift_Message $message) {
                self::assertEquals('text/html', $message->getContentType());
                self::assertEquals('Test subject', $message->getSubject());
                self::assertEquals(['noreply@example.com' => 'Test Mailer'], $message->getSender());
                self::assertEquals(['noreply@example.com' => 'Test Mailer'], $message->getFrom());
                self::assertEquals(['anna@example.com' => 'Anna Rodygina'], $message->getTo());
                self::assertEquals($this->twig->render('email.html.twig'), $message->getBody());
            }
        );

        self::assertTrue($result);
    }

    public function testAddressOnlySender()
    {
        /** @noinspection PhpParamsInspection */
        $service = new MailerService($this->logger, $this->twig, $this->mailer, 'noreply@example.com');

        $result = $service->send(
            'anna@example.com',
            'Anna Rodygina',
            'Test subject',
            'email.html.twig',
            ['message' => 'Test message'],
            function (\Swift_Message $message) {
                self::assertEquals('text/html', $message->getContentType());
                self::assertEquals('Test subject', $message->getSubject());
                self::assertEquals(['noreply@example.com' => null], $message->getSender());
                self::assertEquals(['noreply@example.com' => null], $message->getFrom());
                self::assertEquals(['anna@example.com' => 'Anna Rodygina'], $message->getTo());
                self::assertEquals($this->twig->render('email.html.twig'), $message->getBody());
            }
        );

        self::assertTrue($result);
    }

    public function testNoSender()
    {
        /** @noinspection PhpParamsInspection */
        $service = new MailerService($this->logger, $this->twig, $this->mailer);

        $result = $service->send(
            'anna@example.com',
            'Anna Rodygina',
            'Test subject',
            'email.html.twig',
            ['message' => 'Test message'],
            function (\Swift_Message $message) {
                self::assertEquals('text/html', $message->getContentType());
                self::assertEquals('Test subject', $message->getSubject());
                self::assertNull($message->getSender());
                self::assertEquals(['anna@example.com' => 'Anna Rodygina'], $message->getTo());
                self::assertEquals($this->twig->render('email.html.twig'), $message->getBody());
            }
        );

        self::assertTrue($result);
    }
}
