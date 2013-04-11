<?php
namespace App\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Kitpages\CmsBundle\Controller\Context;

class ToolbarController extends Controller
{

    public function widgetToolbarAction() {
        return $this->render('AppUserBundle:Toolbar:toolbar.html.twig');
    }

}