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

namespace eTraxis\SharedDomain\Framework\Doctrine;

use Doctrine\ORM\Query\SqlWalker;

/**
 * PostgreSQL treats NULLs as greatest values.
 * This walker is to workaround it.
 */
class SortableNullsWalker extends SqlWalker
{
    /**
     * {@inheritdoc}
     */
    public function walkOrderByItem($orderByItem)
    {
        $sql = parent::walkOrderByItem($orderByItem);

        /** @noinspection PhpUndefinedMethodInspection */
        if ($orderByItem->isAsc()) {
            $sql .= ' NULLS FIRST';
        }

        /** @noinspection PhpUndefinedMethodInspection */
        if ($orderByItem->isDesc()) {
            $sql .= ' NULLS LAST';
        }

        return $sql;
    }
}
