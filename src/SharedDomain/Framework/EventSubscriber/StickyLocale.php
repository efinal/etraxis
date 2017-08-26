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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * "Sticky" user's locale.
 */
class StickyLocale implements EventSubscriberInterface
{
    protected $session;
    protected $locale;

    /**
     * Dependency Injection constructor.
     *
     * @param SessionInterface $session
     * @param string           $locale
     */
    public function __construct(SessionInterface $session, string $locale)
    {
        $this->session = $session;
        $this->locale  = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'security.interactive_login' => 'saveLocale',
            'kernel.request'             => ['setLocale', 10],
        ];
    }

    /**
     * Save user's locale when one has been authenticated.
     *
     * @param InteractiveLoginEvent $event
     */
    public function saveLocale(InteractiveLoginEvent $event)
    {
        /** @var \eTraxis\AccountsDomain\Domain\Model\User $user */
        $user = $event->getAuthenticationToken()->getUser();

        $this->session->set('_locale', $user->locale);
    }

    /**
     * Overrides current locale with one is saved in the session.
     *
     * @param GetResponseEvent $event
     */
    public function setLocale(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // Override global locale with current user's one.
        if ($request->hasPreviousSession()) {
            $request->setLocale($request->getSession()->get('_locale', $this->locale));
        }
    }
}
