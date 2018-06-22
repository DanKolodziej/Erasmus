<?php

namespace AppBundle\Service;

use AppBundle\Entity\DeansOffice;
use AppBundle\Entity\DeansOfficeSyllabus;
use AppBundle\Entity\DWM;
use AppBundle\Entity\ExternalCoordinator;
use AppBundle\Entity\InternalCoordinator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Psr\Log\LoggerInterface;

class UserService
{
    protected $em;
    protected $container;
    protected $logger;

    public function __construct(EntityManager $em, Container $container, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->container = $container;
        $this->logger = $logger;
    }

    public function addUser($user, $form){

        $user->setName($form["name"]->getData());
        $user->setSurname($form["surname"]->getData());
        $user->setUsername($form["username"]->getData());
        $user->setEmail($form["email"]->getData());
        $user->setEmailCanonical($form["email"]->getData());
        $user->setEnabled(1);
        $user->setPlainPassword($form["plainPassword"]->getData());

        $userManager = $this->container->get('fos_user.user_manager');
        $userManager->updateUser($user);
    }

    public function getUser($userId){

        return $this->em->getRepository('AppBundle:User')->find($userId);
    }

    public function saveUser($user){

        if($user){

            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->updateUser($user);
        }
    }

    public function internalCoordinatorOrExternalCoordinatorOrDeansOfficeAccess($user){

        if (!is_object($user) || !$user instanceof UserInterface || (!$this->isInternalCoordinator($user) && !$this->isExternalCoordinator($user) && !$this->isDeansOffice($user))) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
    }

    public function internalCoordinatorOrDeansOfficeAccess($user){

        if (!is_object($user) || !$user instanceof UserInterface || (!$this->isInternalCoordinator($user) && !$this->isDeansOffice($user))) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
    }

    public function internalCoordinatorOrExternalCoordinatorAccess($user){

        if (!is_object($user) || !$user instanceof UserInterface || (!$this->isInternalCoordinator($user) && !$this->isExternalCoordinator($user))) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
    }

    public function internalCoordinatorOrDWMAccess($user){

        if (!is_object($user) || !$user instanceof UserInterface || (!$this->isInternalCoordinator($user) && !$this->isDWM($user))) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
    }

    public function administratorOrInternalCoordinatorAccess($user){

        if (!is_object($user) || !$user instanceof UserInterface || (!$this->isAdmin($user) && !$this->isInternalCoordinator($user))) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
    }

    public function internalCoordinatorAccess($user){

        if (!is_object($user) || !$user instanceof UserInterface || !$this->isInternalCoordinator($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
    }

    public function externalCoordinatorAccess($user){

        if (!is_object($user) || !$user instanceof UserInterface || !$this->isExternalCoordinator($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
    }

    public function deansOfficeAccess($user){

        if (!is_object($user) || !$user instanceof UserInterface || !$this->isDeansOffice($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
    }

    public function studentAccess($user){

        if (!is_object($user) || !$user instanceof UserInterface || !$this->isStudent($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
    }

    public function dwmAccess($user){

        if (!is_object($user) || !$user instanceof UserInterface || !$this->isDWM($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
    }

    public function administratorAccess($user){

        if (!is_object($user) || !$user instanceof UserInterface || !$this->isAdmin($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
    }

    public function isInternalCoordinator($user){

        $intCoordinator = $this->em->getRepository('AppBundle:InternalCoordinator')->findOneByUser($user);

        return $intCoordinator == null ? false : true;
    }

    public function isExternalCoordinator($user){

        $exCoordinator = $this->em->getRepository('AppBundle:ExternalCoordinator')->findOneByUser($user);

        return $exCoordinator == null ? false : true;
    }

    public function isDeansOffice($user){

        $deansOffice = $this->em->getRepository('AppBundle:DeansOffice')->findOneByUser($user);

        return $deansOffice == null ? false : true;
    }

    public function isStudent($user){

        $student = $this->em->getRepository('AppBundle:Student')->findOneByUser($user);

        return $student == null ? false : true;
    }

    public function isDWM($user){

        $admin = $this->em->getRepository('AppBundle:DWM')->findOneByUser($user);

        return $admin == null ? false : true;
    }

    public function isAdmin($user){

        $admin = $this->em->getRepository('AppBundle:Administrator')->findOneByUser($user);

        return $admin == null ? false : true;
    }
}