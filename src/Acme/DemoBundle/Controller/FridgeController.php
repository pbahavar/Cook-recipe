<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FridgeController extends Controller
{
	/**
	 *@Route("/") 
	 */
	
    public function indexAction(Request $reques)
    {
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

	return $this->render('AcmeDemoBundle:Fridge:index.html.twig');

        
    }
}
