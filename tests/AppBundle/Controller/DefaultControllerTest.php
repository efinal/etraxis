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

namespace AppBundle\Controller;

use eTraxis\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class DefaultControllerTest extends WebTestCase
{
    public function testIndexAction()
    {
        $this->client->request(Request::METHOD_GET, '/');
        self::assertTrue($this->client->getResponse()->isRedirect());

        $this->loginAs('admin@example.com');

        $this->client->request(Request::METHOD_GET, '/');
        self::assertTrue($this->client->getResponse()->isOk());
    }
}
