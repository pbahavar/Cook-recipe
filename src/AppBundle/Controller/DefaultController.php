<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/app/example", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }


	/**
    * @Route("/hello/{name}", name="hello")
    */
	public  function helloAction($name){
		return $this->render('default/hello.html.twig',array('name'=>$name));
	}

	/**
	 * @Route("/app/example")
	 */
	public function  fridgeAction(Request $request){
		$form = $this->createFormBuilder()
        ->add('submitFile', 'file', array('label' => 'File to Submit'))
        ->getForm();

		// Check if we are posting stuff
		if ($request->getMethod('post') == 'POST') {
    	//Bind request to the form
    	$form->bindRequest($request);

    	// If form is valid
    	if ($form->isValid()) {
         // Get file
         $file = $form->get('submitFile');

         // Your csv file here when you hit submit button
         $file->getData();
    }

 }

//return $this->render('YourBundle:YourControllerName:index.html.twig',
//    array('form' => $form->createView(),));

	return $this->render('YourBundle:YourControllerName:index.html.twig',
    array('form' => $form->createView(),));
		
	}


}


	