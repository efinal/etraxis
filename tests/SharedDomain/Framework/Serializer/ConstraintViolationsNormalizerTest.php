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

namespace eTraxis\SharedDomain\Framework\Serializer;

use eTraxis\SharedDomain\Framework\CommandBus\DummyCommand;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ConstraintViolationsNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalize()
    {
        $normalizer = new ConstraintViolationsNormalizer();

        $violations = new ConstraintViolationList();
        $violations->add(new ConstraintViolation(
            'This value should be "1" or more.',
            'This value should be {{ limit }} or more.',
            [
                '{{ value }}' => '"0"',
                '{{ limit }}' => '"1"',
            ],
            new DummyCommand(['property' => 0]),
            'property',
            '0'
        ));

        $expected = [
            [
                'property' => 'property',
                'value'    => '0',
                'message'  => 'This value should be "1" or more.',
            ],
        ];

        self::assertEquals($expected, $normalizer->normalize($violations));
    }

    public function testSupportsNormalization()
    {
        $normalizer = new ConstraintViolationsNormalizer();

        self::assertTrue($normalizer->supportsNormalization(new ConstraintViolationList()));
        self::assertFalse($normalizer->supportsNormalization(new \stdClass()));
    }
}
