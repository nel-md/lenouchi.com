<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// Add the Bundle Entities
use AppBundle\Entity\Dictionary;
use AppBundle\Entity\User;
use AppBundle\Entity\VoteDown;
use AppBundle\Entity\VoteUp;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Contact;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function indexAction(Request $request)
    {
		$em = $this->getDoctrine()->getManager();
		$entries = $em->getRepository('AppBundle:Dictionary')->findAll();
		
		/**
		* @var $paginator \Knp\Component\Pager\Paginator
		*/
		$paginator  = $this->get('knp_paginator');
		$items = $paginator->paginate(
			$entries,
			$request->query->getInt('page', 1)/*page number*/,
			$request->query->getInt('limit', 4)/*limit per page*/
    	);
		
        return $this->render('AppBundle:Default:index.html.twig',array('list' => $items));
    }
    
	/**
     * @Route("/lettre={letter}", name="app_dictionary_list_by_letter")
     */
    public function letterAction(Request $request, $letter)
    {
        $em = $this->getDoctrine()->getManager();
        $entries = $em->getRepository('AppBundle:Dictionary')
						 ->createQueryBuilder('t')
		 				 ->where('t.word like :word')
		 				 ->setParameter('word', $letter.'%')
		  				 ->orderBy('t.word', 'ASC')
		  			     ->getQuery()
		  			     ->getResult();
		
		/**
		* @var $paginator \Knp\Component\Pager\Paginator
		*/
		$paginator  = $this->get('knp_paginator');
		$items = $paginator->paginate(
			$entries,
			$request->query->getInt('page', 1)/*page number*/,
			$request->query->getInt('limit', 4)/*limit per page*/
    	);
	
		return $this->render('AppBundle:Default:letter.html.twig',array(
				'list' => $items,
				'currentLetter' => $letter
		));
    }
    
    /**
     * @Route("/recherche", name="app_search")
     */
    public function searchAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	$search = $request->get('search');
    	$entries = $em->getRepository('AppBundle:Dictionary')
    				  ->createQueryBuilder('t')
    				  ->where('t.word like :word')
    				  ->setParameter('word', '%'.$search.'%')
    				  ->orderBy('t.word', 'ASC')
    				  ->getQuery()
    				  ->getResult();
    	
    	/**
    	 * @var $paginator \Knp\Component\Pager\Paginator
    	 */
    	$paginator  = $this->get('knp_paginator');
    	$items = $paginator->paginate(
    			$entries,
    			$request->query->getInt('page', 1)/*page number*/,
    			$request->query->getInt('limit', 4)/*limit per page*/
    	);
    	
    	return $this->render('AppBundle:Default:search.html.twig',array(
    			'list' => $items,
    			'search' => $search
    	));
    }

    /**
     * @Route("/dictionnaire/entry_id={id}", name="app_dictionary_entry")
     */
    public function showEntryAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
		$entry = $em->getRepository('AppBundle:Dictionary')->find($id);
		
		return $this->render('AppBundle:Default:show.html.twig',array(
				'entry' => $entry
		));
    }

    /**
     * @Route("/voter", name="app_voter")
     */
    public function voterAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	$id = $request->request->get('entry_id');
		$entry = $em->getRepository('AppBundle:Dictionary')->find($id);

		$session = $request->getSession();

		if(!$this->getUser()){

			if(!$session->has('lenouchi_user_id') || !$session->get('lenouchi_user_id'))
			{
				$session->set('lenouchi_user_id', uniqid(true, null));
			}

			$userId = $session->get('lenouchi_user_id');

		}else{
			$userId = $this->getUser()->getId();
		}

		$content = ['status' => true, 'is_updated' => false];

		if($request->request->get('type') == 'down'){

			$vote = $em->getRepository("AppBundle:VoteUp")->findOneBy(array('word' => $id, 'userId' => $userId));

			if($vote){
				$em->remove($vote);
			}

			$vote = $em->getRepository("AppBundle:VoteDown")->findOneBy(array('word' => $id, 'userId' => $userId));

			if(! $vote){
				$vote = new VoteDown();
				$vote->setUserId($userId);
				$vote->setWord($entry);

				$em->persist($vote);

				$content['is_updated'] = true;
			}
			
		}

		if($request->request->get('type') == 'up'){

			$vote = $em->getRepository("AppBundle:VoteDown")->findOneBy(array('word' => $id, 'userId' => $userId));

			if($vote){
				$em->remove($vote);
			}

			$vote = $em->getRepository("AppBundle:VoteUp")->findOneBy(array('word' => $id, 'userId' => $userId));

			if(! $vote){
				$vote = new VoteUp();
				$vote->setUserId($userId);
				$vote->setWord($entry);

				$em->persist($vote);

				$content['is_updated'] = true;
			}
		}
		
		$em->flush();

		$response = new Response();
		$response->setContent(json_encode($content));
		$response->headers->set('Content-type', 'application/json');

		return $response;
    }
    
    /**
     * @Route("/contacts", name="app_contacts")
     */
    public function contactAction(Request $request)
    {
    	$contact = new Contact();
    	
    	$form = $this->createFormBuilder($contact)
    	->add('author', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px', 'required' => 'required')))
    	->add('subject', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px', 'style' => 'resize:none', 'rows' => '5', 'required' => 'required')))
    	->add('message', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px', 'style' => 'resize:none', 'rows' => '10', 'required' => 'required')))
			    	->add('submit', SubmitType::class, array('label' => 'Soumettre','attr' => array('class' => 'btn btn-primary btn-block', 'style' => 'margin-bottom:15px')))
			    	->getForm();
    	
    	$form->handleRequest($request);
    	
    	if($form->isSubmitted() && $form->isValid()){
    		
    		$author = $form['author']->getData();
    		$subject = $form['subject']->getData();
    		$message = $form['message']->getData();
    		
    		$contact->setAuthor($author);
    		$contact->setSubject($subject);
    		$contact->setMessage($message);
    		
    		$em = $this->getDoctrine()->getManager();
    		
    		$em->persist($contact);
    		$em->flush();
    		
    		$this->addFlash(
    				'success',
    				'Votre requête a été soumise'
    		);
    		return $this->redirectToRoute('app_contacts');
    	}
    	
    	return $this->render("AppBundle:Default:contact.html.twig", array('form' => $form->createView()));
    }
    
    /**
     * @Route("/a-propos", name="app_about_us")
     */
    public function aboutAction(Request $request)
    {
    	return $this->render('AppBundle:Default:about_us.html.twig');
    }
    
    /**
     * @Route("/faq", name="app_faq")
     */
    public function faqAction(Request $request)
    {
    	return $this->render('AppBundle:Default:faq.html.twig');
    }
    
    /**
     * @Route("/cgu", name="app_cgu")
     */
    public function CGUAction(Request $request)
    {
    	return $this->render('AppBundle:Default:cgu.html.twig');
    }
    
}
