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

class HelperController extends Controller
{

	public function entriesByLetterAction(Request $request, $max = 10, $id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$entry = $em->getRepository("AppBundle:Dictionary")->find($id);
    	
		$letter = mb_strcut($entry->getWord(), 0,1);
        $entries = $em->getRepository('AppBundle:Dictionary')
						 ->createQueryBuilder('t')
		 				 ->where('t.word like :word')
		 				 ->andWhere('t.id != :id')
		 				 ->setParameters(['word' => $letter.'%', 'id' => $id])
		 				 ->setMaxResults($max)
		  				 ->orderBy('t.word', 'ASC')
		  			     ->getQuery()
		  			     ->getResult();
		
		return $this->render('AppBundle:Helper:letter.html.twig',array(
				'list' => $entries
		));
    }
    
    public function topVoteUpsAction(Request $request, $max = 10)
    {
    	$em = $this->getDoctrine()->getManager();
    	$entries = $em->getRepository('AppBundle:Dictionary')
    				  ->findTopVoteUps($max);
    	
    	return $this->render('AppBundle:Helper:vote_ups.html.twig',array(
    			'list' => $entries
    	));
    }
    
    public function flopVoteDownsAction(Request $request, $max)
    {
    	$em = $this->getDoctrine()->getManager();
    	$entries = $em->getRepository('AppBundle:Dictionary')
    				  ->findFlopVoteDowns($max);
    	
    	return $this->render('AppBundle:Helper:vote_downs.html.twig',array(
    			'list' => $entries
    	));
    }
    
    
}
