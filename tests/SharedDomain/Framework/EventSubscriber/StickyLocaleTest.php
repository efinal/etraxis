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

use eTraxis\AccountsDomain\Domain\Model\User;
use eTraxis\SharedDomain\Framework\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class StickyLocaleTest extends WebTestCase
{
    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    protected $requestStack;

    /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface */
    protected $session;

    protected function setUp()
    {
        parent::setUp();

        $this->requestStack = $this->client->getContainer()->get('request_stack');
        $this->session      = $this->client->getContainer()->get('session');
    }

    public function testGetSubscribedEvents()
    {
        $expected = [
            'security.interactive_login',
            'kernel.request',
        ];

        self::assertEquals($expected, array_keys(StickyLocale::getSubscribedEvents()));
    }

    public function testSaveLocale()
    {
        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findOneBy(['email' => 'admin@example.com']);

        $user->locale = 'ru';

        $request = new Request();
        $token   = new UsernamePasswordToken($user, null, 'main');

        $event = new InteractiveLoginEvent($request, $token);

        $object = new StickyLocale($this->session, 'en');
        $object->saveLocale($event);

        self::assertEquals('ru', $this->session->get('_locale'));
    }

    public function testSetDefaultLocale()
    {
        $request = new Request();

        $request->setSession($this->session);
        $request->cookies->set($this->session->getName(), $this->session->getId());

        $this->requestStack->push($request);

        $event = new GetResponseEvent(static::$kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $object = new StickyLocale($this->session, 'ru');

        $object->setLocale($event);

        self::assertEquals('ru', $event->getRequest()->getLocale());
    }

    public function testSetLocaleBySession()
    {
        $request = new Request();

        $request->setSession($this->session);
        $request->cookies->set($this->session->getName(), $this->session->getId());
        $this->session->set('_locale', 'ja');

        $this->requestStack->push($request);

        $event = new GetResponseEvent(static::$kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $object = new StickyLocale($this->session, 'ru');

        $object->setLocale($event);

        self::assertEquals('ja', $event->getRequest()->getLocale());
    }
}
