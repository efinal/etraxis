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

namespace AppBundle\DataFixtures\Tests;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eTraxis\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadUsersData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $encoder */
        $encoder = $this->container->get('security.password_encoder');

        $user = new User();

        $user->email    = 'artem@example.com';
        $user->password = $encoder->encodePassword($user, 'secret');
        $user->fullname = 'Artem Rodygin';

        $manager->persist($user);
        $manager->flush();
    }
}
