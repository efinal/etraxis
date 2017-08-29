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
use eTraxis\AccountsDomain\Application\Event\LoginFailedEvent;
use eTraxis\AccountsDomain\Domain\Model\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener.
 */
class LockAccountListener implements EventSubscriberInterface
{
    protected $logger;
    protected $manager;
    protected $authFailures;
    protected $lockDuration;

    /**
     * Dependency Injection constructor.
     *
     * @param LoggerInterface $logger
     * @param ObjectManager   $manager
     * @param int             $authFailures
     * @param int             $lockDuration
     */
    public function __construct(
        LoggerInterface $logger,
        ObjectManager   $manager,
        int             $authFailures = null,
        int             $lockDuration = null
    )
    {
        $this->logger       = $logger;
        $this->manager      = $manager;
        $this->authFailures = $authFailures;
        $this->lockDuration = $lockDuration;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            LoginFailedEvent::class => 'handle',
        ];
    }

    /**
     * Increases locks count for specified account.
     *
     * @param LoginFailedEvent $event
     */
    public function handle(LoginFailedEvent $event)
    {
        if ($this->authFailures === null) {
            return;
        }

        /** @var \Pignus\Model\UserRepositoryInterface $repository */
        $repository = $this->manager->getRepository(User::class);

        /** @var User $user */
        if ($user = $repository->findOneByUsername($event->username)) {

            $this->logger->info('Authentication failure', [$event->username]);

            if ($user->incAuthFailures() >= $this->authFailures) {

                if ($this->lockDuration === null) {
                    $user->lockAccount();
                }
                else {
                    $interval = sprintf('PT%dM', $this->lockDuration);
                    $user->lockAccount(date_create()->add(new \DateInterval($interval)));
                }
            }

            $this->manager->persist($user);
        }
    }
}
