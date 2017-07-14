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

namespace eTraxis\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as SymfonyWebTestCase;

/**
 * Extended web test case with an autoboot kernel and few helpers.
 */
class WebTestCase extends SymfonyWebTestCase
{
    /** @var \Symfony\Bundle\FrameworkBundle\Client */
    protected $client;

    /** @var \Symfony\Bridge\Doctrine\RegistryInterface */
    protected $doctrine;

    /** @var \League\Tactician\CommandBus */
    protected $commandbus;

    /**
     * Boots the kernel and retrieve most often used services.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();

        $this->doctrine   = $this->client->getContainer()->get('doctrine');
        $this->commandbus = $this->client->getContainer()->get('tactician.commandbus');
    }

    /**
     * Makes AJAX request to specified URI.
     *
     * @param string $method
     * @param string $uri
     * @param array  $parameters
     * @param array  $headers
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function ajax(string $method, string $uri, array $parameters = [], array $headers = [])
    {
        $headers['HTTP_X-Requested-With'] = 'XMLHttpRequest';

        $this->client->request($method, $uri, $parameters, [], $headers);

        return $this->client->getResponse();
    }
}
