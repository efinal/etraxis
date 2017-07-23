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

namespace eTraxis\Twig;

use eTraxis\Dictionary\Locale;

/**
 * Twig extension for user locale.
 */
class LocaleExtension extends \Twig_Extension
{
    const LEFT_TO_RIGHT = 'ltr';
    const RIGHT_TO_LEFT = 'rtl';

    protected $bbcode;

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        $options = [
            'pre_escape' => 'html',
            'is_safe'    => ['html'],
        ];

        return [
            new \Twig_SimpleFilter('direction', [$this, 'filterDirection'], $options),
            new \Twig_SimpleFilter('language', [$this, 'filterLanguage'], $options),
        ];
    }

    /**
     * Returns language direction ("ltr" or "rtl") for specified locale.
     *
     * @param string $locale
     *
     * @return string
     */
    public function filterDirection(string $locale)
    {
        $rtl = ['ar', 'fa', 'he'];

        return in_array(mb_substr($locale, 0, 2), $rtl, true) ? self::RIGHT_TO_LEFT : self::LEFT_TO_RIGHT;
    }

    /**
     * Returns translated language name for specified locale.
     *
     * @param string $locale
     *
     * @return null|string
     */
    public function filterLanguage(string $locale)
    {
        return Locale::has($locale) ? Locale::get($locale) : null;
    }
}
