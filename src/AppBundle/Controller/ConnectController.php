<?php

/*
 * This file override FOSUserBundle\Controller\RegistrationController
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Controller managing the registration.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class ConnectController extends Controller
{
    /**
     * @Route("/connect", name="security_connect")

     * @param Request $request
     *
     * @return Response
     */
    public function connectAction(Request $request)
    {   
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');
        $data = $request->request->all();

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $response = new JsonResponse(['target' => $this->getTargetUrlFromSession()]);
        
        if(! $oldUser = $userManager->findUserByEmail($data['email'])){
            $user->setEmail($data['email']);
            $user->setUsername($data['username']);
            $user->setPlainPassword(md5(uniqid(null, true)));
            $user->setLastLogin(new \DateTime());

            $userManager->updateUser($user);
        }else{
            $user = $oldUser;
            $user->setLastLogin(new \DateTime());

            $this->getDoctrine()->getManager()->flush();
        }
        
        $this->get('fos_user.security.login_manager')->logInUser('main', $user, $response);
        
        return $response;
    }

    /**
     * @return mixed
     */
    private function getTargetUrlFromSession()
    {
        $key = '_security.main.target_path';
        
        if ($this->get('session')->has('_security.main.target_path')) {
            return $this->get('session')->get($key);
        }

        return $this->generateUrl("app_homepage", [], true);
    }
}
