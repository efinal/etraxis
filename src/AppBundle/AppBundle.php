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

namespace AppBundle;

use Doctrine\ORM\Query;
use eTraxis\Framework\Doctrine\SortableNullsWalker;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->container->get('doctrine.orm.entity_manager');

        // PostgreSQL treats NULLs as greatest values.
        if ($this->container->getParameter('database_driver') === 'pdo_pgsql') {

            $manager->getConfiguration()->setDefaultQueryHint(
                Query::HINT_CUSTOM_OUTPUT_WALKER,
                SortableNullsWalker::class
            );
        }
    }
}
