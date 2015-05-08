<?php

namespace Acme\DemoBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DemoControllerTest extends WebTestCase
{
	
	/*
	 * Test the Demo Controller Fridge function
	 */
	public function testFridge()
	{
		$client = static::createClient();
		
		// goes to Fridge page
        $crawler = $client->request('GET', '/demo/fridge');
		
		# Select the file from the filesystem
   		$csvFile = new UploadedFile(
        # Path to the file to send
        dirname(__FILE__).'/../Uploads/test.csv',
        # Name of the sent file
        'test.csv',
        # MIME type
        'text/csv',
        # Size of the file
        151
    	);

	
	  $jsonRecipe ='[

    {

        "name": "grilled cheese on toast",

        "ingredients": [

            { "item":"bread", "amount":"2", "unit":"slices"},

            { "item":"cheese", "amount":"2", "unit":"slices"}

        ]

    }

    ,

    {

        "name": "salad sandwich",

        "ingredients": [

            { "item":"bread", "amount":"2", "unit":"slices"},

            { "item":"mixed salad", "amount":"100", "unit":"grams"}

        ]

    }

]';

    # Select the form and populate the fields
    $form = $crawler->filter('input[type=submit]')->form(
    array('fridge[fridge_csv]' => $csvFile ,
		 'fridge[recipe]' => $jsonRecipe)
		);
		
    # Send the form with the given values
    $crawler = $client->submit($form);
   
    #Fecth the appropriate result from the function 
    $this->assertEquals(1,
         $crawler->filter('html:contains("salad sandwich")')->count()
    );
	} 
	
	
  
}
