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

namespace eTraxis\Domain\Repository;

use Doctrine\ORM\EntityRepository;
use Pignus\Model\UserRepositoryInterface;

/**
 * User repository.
 */
class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneByUsername(string $username)
    {
        return $this->findOneBy([
            'email' => $username,
        ]);
    }
}
