<?php
/**
 * Created by PhpStorm.
 * User: alin
 * Date: 31.03.2016
 * Time: 19:00
 */

namespace AppBundle\Controller;

use AppBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
     * @param Request $request
     * @return type
     */
    public function promoteUserListAction(Request $request)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get('doctrine')->getManager()->getRepository('AppBundle:User');

        $users = $userRepository->findAll();

        return $this->render(':admin:listUser.html.twig',
            array(
                'users' => $users
            )
        );
    }
    
    public function promoteUserAction(Request $request)
    {
        
    }
    
    public function demoteUserAction(Request $request)
    {
        
    }
}
