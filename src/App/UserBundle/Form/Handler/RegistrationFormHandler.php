<?php

namespace App\UserBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\GroupManagerInterface;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;

class RegistrationFormHandler extends BaseHandler
{

    protected $groupManager;

    public function __construct(FormInterface $form, Request $request, UserManagerInterface $userManager, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, GroupManagerInterface $groupManager)
    {
        parent::__construct($form, $request, $userManager, $mailer, $tokenGenerator);
        $this->groupManager = $groupManager;
    }

    protected function onSuccess(UserInterface $user, $confirmation)
    {

        parent::onSuccess($user, $confirmation);
//        if ($user->getGroup() == null) {
            $group = $this->groupManager->findGroupBy(array('name' => 'user'));
            $user->setGroup($group);
            $user->setRoles($group->getRoles());
            $this->userManager->updateUser($user);
//        } else {
//            $group = $user->getGroup();
//            $user->setRoles($group->getRoles());
//            $this->userManager->updateUser($user);
//        }
    }
}