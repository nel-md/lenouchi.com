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
// Add the different FormField Types
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DictionaryController extends Controller
{	
	/**
     * @Route("/mon-dictionnaire", name="app_user_dictionary_list")
     */
    public function listAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	$entries = $em->getRepository('AppBundle:Dictionary')
    				  ->findBy(array('author' => $this->getUser()->getId()), array('word' => 'ASC'));
		
		return $this->render("AppBundle:Dictionary:list.html.twig", array('list' => $entries));
    }

	/**
     * @Route("/mon-dictionnaire/ajouter", name="app_user_dictionary_add")
     */
    public function addAction(Request $request)
    {
		$newEntry = new Dictionary;
		
		$form = $this->createFormBuilder($newEntry)
			->add('word', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
			->add('definition', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px', 'style' => 'resize:none', 'rows' => '5')))
			->add('example', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px', 'style' => 'resize:none', 'rows' => '10')))
			->add('ajouter', SubmitType::class, array('label' => 'Ajouter','attr' => array('class' => 'btn btn-primary btn-block', 'style' => 'margin-bottom:15px')))
			->getForm();
		
		$form->handleRequest($request);
		
		if($form->isSubmitted() && $form->isValid()){
			
			$word = strtolower($form['word']->getData());
			$definition = $form['definition']->getData();
			$example = $form['example']->getData();
			$author = $this->getUser();
			
			$em = $this->getDoctrine()->getManager();
			$entries = $em->getRepository("AppBundle:Dictionary")->findBy(array('word' => $word));
			
			if($entries && count($entries) > 0){
				$word .= "(".count($entries).")";
			}
			
			$newEntry->setWord($word);
			$newEntry->setDefinition($definition);
			$newEntry->setExample($example);
			$newEntry->setAuthor($author);
			
			
			$em->persist($author);
			$em->persist($newEntry);
			$em->flush();
			
			$this->addFlash(
				'success',
				'Votre mot a été ajouté'
			);
			return $this->redirectToRoute('app_homepage');
		}
		
		return $this->render("AppBundle:Dictionary:add.html.twig", array('form' => $form->createView()));
    }

    /**
     * @Route("/mon-dictionnaire/{id}/modifier", name="app_user_dictionary_edit")
     */
    public function editAction(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getManager();
		$entry = $em->getRepository('AppBundle:Dictionary')->find($id);
		
		$form = $this->createFormBuilder($entry)
			->add('word', TextType::class, array('attr' => array('class' => 'form-control disabled', 'style' => 'margin-bottom:15px')))
			->add('definition', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px', 'style' => 'resize:none', 'rows' => '5')))
			->add('example', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px', 'style' => 'resize:none', 'rows' => '10')))
			//Submit button
			->add('modifier', SubmitType::class, array('label' => 'Modifier','attr' => array('class' => 'btn btn-primary')))
			->getForm();
		
		$form->handleRequest($request);
		
		if($form->isSubmitted() && $form->isValid()){
			
			$word = $form['word']->getData();
			$definition = $form['definition']->getData();
			$example = $form['example']->getData();
			
			$entry->setWord($word);
			$entry->setDefinition($definition);
			$entry->setExample($example);
			
			$em->flush();
			
			$this->addFlash(
				'success',
				'Votre défintion <b>'.$entry->getWord().'</b> a été mise à jour.'
			);

			return $this->redirectToRoute('app_user_dictionary_list');
		}
		
		return $this->render("AppBundle:Dictionary:edit.html.twig", array(
				'form' => $form->createView(),
				'entry' => $entry
		));
    }

    /**
     * @Route("/mon-dictionnaire/{id}/supprimer", name="app_user_dictionary_delete")
     */
    public function deleteAction(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getManager();
		$entry = $em->getRepository('AppBundle:Dictionary')->find($id);

		$this->addFlash(
				'danger',
				'Votre défintion <b>'.$entry->getWord().'</b> a été supprimée.'
			);

		$em->remove($entry);
		$em->flush();
			
		return $this->redirectToRoute('app_user_dictionary_list');
    }
    
}
