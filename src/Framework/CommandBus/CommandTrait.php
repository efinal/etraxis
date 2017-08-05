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

namespace eTraxis\Framework\CommandBus;

/**
 * A trait for CommandBus command object.
 */
trait CommandTrait
{
    /**
     * Initializes object properties with values from provided arrays.
     *
     * @param array $values Initial values.
     * @param array $extra  Optional extra values.
     *                      In case of keys conflicts this array overrides data from the first one.
     */
    public function __construct(array $values = null, array $extra = [])
    {
        /**
         * Replaces empty strings with nulls.
         *
         * @param mixed $value A value to be updated. Can be an array.
         *
         * @return mixed Updated value.
         */
        $empty2null = function ($value) use (&$empty2null) {

            if (is_array($value)) {
                return array_map($empty2null, $value);
            }

            return is_string($value) && mb_strlen($value) === 0 ? null : $value;
        };

        $data = $empty2null($extra + ($values ?? []));

        $properties = array_keys(get_object_vars($this));

        foreach ($properties as $property) {
            if (array_key_exists($property, $data)) {
                $this->$property = $data[$property];
            }
        }
    }
}
