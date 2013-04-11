<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\UserBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\GroupManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;

class UserCreateFormHandler extends BaseHandler
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
        $group = $user->getGroup();
        $user->setRoles($group->getRoles());

        $this->userManager->updateUser($user);
    }
}
