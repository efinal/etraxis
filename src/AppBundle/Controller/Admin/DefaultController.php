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

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default controller for admin area.
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="admin")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('admin/base.html.twig');
    }
}