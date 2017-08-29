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
use eTraxis\AccountsDomain\Application\Event\LoginSuccessfulEvent;
use eTraxis\AccountsDomain\Domain\Model\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener.
 */
class UnlockAccountListener implements EventSubscriberInterface
{
    protected $manager;

    /**
     * Dependency Injection constructor.
     *
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            LoginSuccessfulEvent::class => 'handle',
        ];
    }

    /**
     * Clears locks count for specified account.
     *
     * @param LoginSuccessfulEvent $event
     */
    public function handle(LoginSuccessfulEvent $event)
    {
        /** @var \Pignus\Model\UserRepositoryInterface $repository */
        $repository = $this->manager->getRepository(User::class);

        /** @var User $user */
        if ($user = $repository->findOneByUsername($event->username)) {

            $user->unlockAccount();

            $this->manager->persist($user);
        }
    }
}
