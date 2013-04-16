<?php
namespace App\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Gedmo\Translatable\Translatable;

use Kitpages\ShopBundle\Entity\Order;
use Kitpages\ShopBundle\Entity\OrderHistory;
use Kitpages\ShopBundle\Entity\OrderUser;


class UserAreaController extends Controller
{
    public function orderListAction()
    {
        $currentUser = $this->get('security.context')->getToken()->getUser();
        // on récupère la commande à afficher
        $em = $this->getDoctrine()->getManager();
        $orderList = $em->getRepository("KitpagesShopBundle:Order")->findByUserIdAndState(
            $currentUser->getId(),
            OrderHistory::STATE_PAYED
        );
        $orderTwigList = array();
        foreach($orderList as $order) {

            $invoiceId = null;

            $invoice=$order->getInvoice();
            if ($invoice != null) {
                $invoiceId = $invoice->getId();
            }

            $orderTwigList[] = array(
                'invoiceId' => $invoiceId,
                'orderId' => $order->getId()
            );
        }
        return $this->render(
            'AppSiteBundle:UserArea:orderList.html.twig',
            array(
                'orderList' => $orderTwigList,
                'kitMeta' => array(
                    'title' => $this->get('translator')->trans('Orders')
                )
            )
        );
    }

    public function widgetNavigationAction($appSiteUserAreaNavigationSelect)
    {
        return $this->render(
            'AppSiteBundle:UserArea:navigation.html.twig',
            array('appSiteUserAreaNavigationSelect' => $appSiteUserAreaNavigationSelect)
        );
    }

}