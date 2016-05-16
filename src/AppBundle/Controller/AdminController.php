<?php
/**
 * Created by PhpStorm.
 * User: alin
 * Date: 31.03.2016
 * Time: 19:00
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class AdminController extends Controller
{
    /**
     * @return type
     */
    public function addUserAction()
    {
        return $this->redirectToRoute('fos_user_registration_register');
    }

    /**
     * @return type
     */
    public function promoteUserListAction()
    {
        /** @var AuthorizationChecker $authorizationChecker */
        $authorizationChecker = $this->container->get('security.authorization_checker');
        if (!$authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            if (!$authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
                return $this->render(':admin:accessDenied.html.twig');
            }
        }

        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get('doctrine')->getManager()->getRepository('AppBundle:User');

        $users = $userRepository->findAll();

        return $this->render(':admin:listUser.html.twig',
            array(
                'messages' => "",
                'users' => $users
            )
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function promoteUserAction(Request $request)
    {
        /** @var AuthorizationChecker $authorizationChecker */
        $authorizationChecker = $this->container->get('security.authorization_checker');
        if (!$authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            if (!$authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
                return $this->render(':admin:accessDenied.html.twig');
            }
        }

        $role = $request->get('role');
        $userId = $request->get('id');

        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get('doctrine')->getManager()->getRepository('AppBundle:User');

        /** @var User $user */
        $user = $userRepository->findOneBy(array('id' => $userId));

        if ($user == null || !($user instanceof User)) {
            $this->get('session')->getFlashBag()->add('error', 'User Does Not Exist');
        }

        $userManager = $this->get('fos_user.util.user_manipulator');
        $userManager->addRole($user, $role);

        $users = $userRepository->findAll();

        return $this->render(':admin:listUser.html.twig',
            array(
                'users' => $users
            )
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function demoteUserAction(Request $request)
    {
        /** @var AuthorizationChecker $authorizationChecker */
        $authorizationChecker = $this->container->get('security.authorization_checker');
        if (!$authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            if (!$authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
                return $this->render(':admin:accessDenied.html.twig');
            }
        }

        $role = $request->get('role');
        $userId = $request->get('id');

        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get('doctrine')->getManager()->getRepository('AppBundle:User');

        /** @var User $user */
        $user = $userRepository->findOneBy(array('id' => $userId));

        if ($user == null || !($user instanceof User)) {
            $this->get('session')->getFlashBag()->add('error', 'User Does Not Exist');
        }

        $userManager = $this->get('fos_user.util.user_manipulator');
        $userManager->removeRole($user, $role);

        $users = $userRepository->findAll();

        $this->get('session')->getFlashBag()->add('success', 'User Done Demote');
        return $this->render(':admin:listUser.html.twig',
            array(
                'users' => $users
            )
        );
    }

    public function editUserAction(Request $request)
    {

    }
}
