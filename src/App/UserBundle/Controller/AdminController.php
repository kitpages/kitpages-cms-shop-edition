<?php
namespace App\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\UserBundle\Entity\User;

use App\UserBundle\Form\Handler\UserFormHandler;


class AdminController extends Controller
{

    public function widgetNavigationAction()
    {

        return $this->render(
            'AppUserBundle:Admin:navigation.html.twig'
        );
    }

    public function userListAction() {

        $userList = $this->container->get('fos_user.user_manager')->findUsers();

        return $this->render(
            'AppUserBundle:Admin:userList.html.twig',
            array(
                'userList' => $userList
            )
        );
    }

    /**
     * Show a user
     */
    public function userShowAction(User $user)
    {

        return $this->container->get('templating')->renderResponse(
            'AppUserBundle:Admin:show.html.'.$this->container->getParameter('fos_user.template.engine'),
            array(
                'user' => $user
            )
        );
    }


    public function userCreateAction()
    {
        $form = $this->container->get('app_user.user.create.form');
        $formHandler = $this->container->get('app_user.user.create.form.handler');
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');

        $process = $formHandler->process($confirmationEnabled);
        if ($process) {
            $user = $form->getData();

            if ($confirmationEnabled) {
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
            }

            $this->getRequest()->getSession()->setFlash('fos_user_success', 'registration.flash.user_created');
            $url = $this->container->get('router')->generate('app_user_admin_user_list');

            return new RedirectResponse($url);
        }

        return $this->container->get('templating')->renderResponse('AppUserBundle:Admin:create.html.'.$this->container->getParameter('fos_user.template.engine'), array(
            'form' => $form->createView(),
            'theme' => $this->container->getParameter('fos_user.template.theme')
        ));
    }


    /**
     * Edit a user
     */
    public function userEditAction(User $user)
    {

        $form = $this->container->get('app_user.user.form');
        $formHandler = $this->container->get('app_user.user.form.handler');
        $process = $formHandler->process($user);
        if ($process) {
            $this->getRequest()->getSession()->setFlash('fos_user_success', 'profile.flash.updated');

            return new RedirectResponse($this->container->get('router')->generate('app_user_admin_user_list'));
        }

        return $this->container->get('templating')->renderResponse(
            'AppUserBundle:Admin:edit.html.'.$this->container->getParameter('fos_user.template.engine'),
            array(
                'kit_user_id' => $user->getId(),
                'form' => $form->createView(),
                'theme' => $this->container->getParameter('fos_user.template.theme')
            )
        );
    }

    /**
     * Delete a user
     */
    public function userDeleteAction(User $user)
    {

        $this->container->get('fos_user.user_manager')->deleteUser($user);
        return new RedirectResponse($this->container->get('router')->generate('app_user_admin_user_list'));
    }


}