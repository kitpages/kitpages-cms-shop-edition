<?php
namespace App\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\UserBundle\Entity\Group;
use App\UserBundle\Entity\User;


class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    public function load($em)
    {

        $groupManager = $this->container->get('fos_user.group_manager');
        $groupAdmin = new Group();
        $groupAdmin->setName('admin');
        $groupAdmin->setRoles(array('ROLE_ADMIN'));
        $user = $groupManager->updateGroup($groupAdmin);

        $groupManager = $this->container->get('fos_user.group_manager');
        $groupAdmin = new Group();
        $groupAdmin->setName('user');
        $groupAdmin->setRoles(array('ROLE_USER'));
        $user = $groupManager->updateGroup($groupAdmin);

    }
}
