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

namespace eTraxis\AccountsDomain\Application\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use eTraxis\AccountsDomain\Application\Event\ExternalAccountLoadedEvent;
use eTraxis\AccountsDomain\Domain\Model\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener.
 */
class RegisterExternalAccountListener implements EventSubscriberInterface
{
    protected $logger;
    protected $manager;
    protected $locale;
    protected $theme;

    /**
     * Dependency Injection constructor.
     *
     * @param LoggerInterface $logger
     * @param ObjectManager   $manager
     * @param string          $locale
     * @param string          $theme
     */
    public function __construct(LoggerInterface $logger, ObjectManager $manager, string $locale, string $theme)
    {
        $this->logger  = $logger;
        $this->manager = $manager;
        $this->locale  = $locale;
        $this->theme   = $theme;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ExternalAccountLoadedEvent::class => 'handle',
        ];
    }

    /**
     * Registers external account in eTraxis database.
     * If specified account is already registered, its properties will be updated.
     *
     * @param ExternalAccountLoadedEvent $event
     */
    public function handle(ExternalAccountLoadedEvent $event)
    {
        $repository = $this->manager->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneBy([
            'accountProvider' => $event->provider,
            'accountUid'      => $event->uid,
        ]);

        // If we can't find the account by its UID, try to find by the email.
        if ($user === null) {
            $this->logger->info('Cannot find by UID.', [$event->provider, $event->uid]);

            $user = $repository->findOneBy([
                'email' => $event->email,
            ]);
        }

        // Register new account.
        if ($user === null) {
            $this->logger->info('Register external account.', [$event->email, $event->fullname]);

            $user = new User();

            $user->locale = $this->locale;
            $user->theme  = $this->theme;
        }
        // The account already exists - update it.
        else {
            $this->logger->info('Update external account.', [$event->email, $event->fullname]);
        }

        $user->accountProvider = $event->provider;
        $user->accountUid      = $event->uid;
        $user->email           = $event->email;
        $user->fullname        = $event->fullname;

        $this->manager->persist($user);
    }
}
