<?php
namespace App\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Gedmo\Translatable\Translatable;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use App\UserBundle\Entity\User;
use Kitpages\ShopBundle\Entity\Order;
use Kitpages\ShopBundle\Entity\OrderHistory;
use Kitpages\ShopBundle\Entity\OrderUser;


class PurchaseProcessController extends Controller
{

    public function widgetBreadcrumbAction($appSitePurchaseProcessBreadcrumbSelect)
    {
        return $this->render(
            'AppSiteBundle:PurchaseProcess:breadcrumb.html.twig',
            array('appSitePurchaseProcessBreadcrumbSelect' => $appSitePurchaseProcessBreadcrumbSelect)
        );
    }

    public function identificationAction()
    {

        $form = $this->container->get('fos_user.registration.form');
        $formHandler = $this->container->get('fos_user.registration.form.handler');
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');


        $request = $this->container->get('request');
        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $session = $request->getSession();
        /* @var $session \Symfony\Component\HttpFoundation\Session */

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            // TODO: this is a potential security risk (see http://trac.symfony-project.org/ticket/9523)
            $error = $error->getMessage();
        }
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);

        $process = $formHandler->process($confirmationEnabled);
        if ($process) {
            $user = $form->getData();

            if ($confirmationEnabled) {
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $route = 'fos_user_registration_check_email';
                $url = $this->container->get('router')->generate($route);
            } else {
                $this->authenticateUser($user);
                $url = $request->getSession()->get('_security.target_path', null);

            }
            $request->getSession()->setFlash('fos_user_success', 'registration.flash.user_created');

            return new RedirectResponse($url);
        }

        return $this->render(
            'AppSiteBundle:PurchaseProcess:identification.html.twig',
            array(
                'last_username' => $lastUsername,
                'error'         => $error,
                'form' => $form->createView(),
                'theme' => $this->container->getParameter('fos_user.template.theme'),
                'kitMeta' => array(
                    'title' => $this->get('translator')->trans('Purchase process identification'),
                    'description' => $this->get('translator')->trans('Purchase process identification')
                )
            )
        );
    }

    /**
     * Authenticate a user with Symfony Security
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     */
    protected function authenticateUser(User $user)
    {
        $providerKey = $this->container->getParameter('fos_user.firewall_name');
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        $this->container->get('security.context')->setToken($token);
    }

    public function cartAction()
    {
        return $this->render(
            'AppSiteBundle:PurchaseProcess:cart.html.twig'
        );
    }

    public function displayOrderAction($orderId)
    {
        if (
            ! $this->get('security.context')->isGranted('ROLE_SHOP_USER')
        ) {
            return new Response('The user should be authenticated on this page');
        }

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository("KitpagesShopBundle:Order")->find($orderId);

        $currentUser = $this->get('security.context')->getToken()->getUser();

        $invoiceUser = $order->getInvoiceUser();
        if (
            ($invoiceUser != null) &&
            ($invoiceUser->getUserId() != $currentUser->getId())
        ) {
            return new Response('You are not allowed to see this order');
        }

        $invoiceUser = new OrderUser();
        $invoiceUser->setCountryCode($currentUser->getCountry());
        $invoiceUser->setEmail($currentUser->getEmail());
        $invoiceUser->setFirstName($currentUser->getFirstname());
        $invoiceUser->setLastName($currentUser->getLastname());
        $invoiceUser->setInvoiceOrder($order);
        $invoiceUser->setUserId($currentUser->getId());

        $em->persist($invoiceUser);
        $order->setInvoiceUser($invoiceUser);
        $em->persist($order);
        $em->flush();

        $response = $this->forward('KitpagesShopBundle:Order:displayOrder', array(
            'orderId'  => $orderId
        ));

        return $response;
    }

    public function backToShopAction()
    {
        $transactionId = $this->get('request')->query->get('transactionId', null);
        // on récupère la transaction
        $em = $this->getDoctrine()->getManager();
        $transaction = $em->getRepository("KitanoPaymentBundle:Transaction")->find($transactionId);

        if ($transaction == null) {
            $this->get('session')->setFlash('notice', "no transaction found");
            //redirect
            return $this->redirect($this->generateUrl('homepage'));
        }

        $order = $em->getRepository("KitpagesShopBundle:Order")->find($transaction->getOrderId());

        if ($order == null) {
            $this->get('session')->setFlash('notice', "no order found");
            //redirect
            return $this->redirect($this->generateUrl('homepage'));
        }

        $currentUser = $this->get('security.context')->getToken()->getUser();
        if (!($currentUser instanceof User) || $order->getInvoiceUser()->getUserId() != $currentUser->getId()) {
            $this->get('session')->setFlash('notice', "you can not access");
            //redirect
            return $this->redirect($this->generateUrl('homepage'));
        }

        $data = array(
            'transaction_id' => $transactionId,
            'amount'         => $transaction->getAmount(),
            'state'          => $order->getState(),
            'order_id'       => $order->getId()

        );

        if($order->getState() == OrderHistory::STATE_PAYED) {
            return $this->render(
                'AppSiteBundle:PurchaseProcess:backToShopSuccess.html.twig',
                array('data' => $data)
            );
        } else {
            $message = '';
            if (OrderHistory::STATE_READY_TO_PAY) {
                $message = $this->get('translator')->trans('Your payment has been refused', array());
            }

            if (OrderHistory::STATE_CANCELED) {
                $message = $this->get('translator')->trans('You canceled the payment', array());
            }
            return $this->render(
                'AppSiteBundle:PurchaseProcess:backToShopFailure.html.twig',
                array('message' => $message)
            );
        }

    }

}
