<?php

namespace App\UserBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\ProfileController as BaseController;


class ProfileController extends BaseController
{
    /**
     * Edit the user
     */
    public function editAction()
    {
        $targetPath = $this->container->get('request')->get('_target_path', null);
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->container->get('fos_user.profile.form');
        $formHandler = $this->container->get('fos_user.profile.form.handler');

        $process = $formHandler->process($user);
        if ($process) {
            $this->setFlash('fos_user_success', 'profile.flash.updated');
            $this->setFlash('notice', 'profile updated');
            if ($targetPath != null) {
                return new RedirectResponse($targetPath);
            } else {
                return new RedirectResponse('/');
            }
        }

        return $this->container->get('templating')->renderResponse(
            'FOSUserBundle:Profile:edit.html.'.$this->container->getParameter('fos_user.template.engine'),
            array(
                'form' => $form->createView(),
                'theme' => $this->container->getParameter('fos_user.template.theme'),
                'target_path' => $targetPath
            )
        );
    }

}
