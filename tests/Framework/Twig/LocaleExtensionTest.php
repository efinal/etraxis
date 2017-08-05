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

namespace eTraxis\Framework\Twig;

class LocaleExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testFilters()
    {
        $expected = [
            'direction',
            'language',
        ];

        $extension = new LocaleExtension();

        $filters = array_map(function (\Twig_Filter $filter) {
            return $filter->getName();
        }, $extension->getFilters());

        self::assertEquals($expected, $filters);
    }

    public function testFilterDirection()
    {
        $extension = new LocaleExtension();

        self::assertEquals(LocaleExtension::LEFT_TO_RIGHT, $extension->filterDirection('en'));
        self::assertEquals(LocaleExtension::RIGHT_TO_LEFT, $extension->filterDirection('ar'));
        self::assertEquals(LocaleExtension::RIGHT_TO_LEFT, $extension->filterDirection('fa'));
        self::assertEquals(LocaleExtension::RIGHT_TO_LEFT, $extension->filterDirection('he'));
    }

    public function testFilterLanguage()
    {
        $extension = new LocaleExtension();

        self::assertEquals('Русский', $extension->filterLanguage('ru'));
    }
}
