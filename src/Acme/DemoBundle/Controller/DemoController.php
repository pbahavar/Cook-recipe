<?php
namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Acme\DemoBundle\Form\ContactType;
use Acme\DemoBundle\Form\FridgeType;
use Acme\DemoBundle\Entity\Item;
use Acme\DemoBundle\Entity\Recipe;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
class DemoController extends Controller {

	/**
	  *@Route("/fridge", name="_demo_fridge") 
	  *@Template()
	*/

	public function fridgeAction(Request $request) {
		$form = $this->createForm(new FridgeType());
		$form->handleRequest($request);
		$message = "Nothing in the fridge, order take out !!!";
		$messageType='error';
		# Check if we are posting
		if ($request->getMethod('post') == 'POST') {

			// If form is valid
			if ($form->isValid()) {
				// Get file
				$fileObj = $form->get('fridge_csv');
				$recipeJson = $form->get('recipe');
				$recipes = json_decode($recipeJson->getData(), true);
				if(count($recipes)> 0){
				// Your csv file here when you hit submit button
				$file = $fileObj->getData();
				$extension = $file->guessExtension();
				if ($extension == "txt") { // check if the file extension is as required; you can also check the mime type itself: $file->getMimeType()

					#Parse the CSV file and populate proper Item Entities
					$itemArray = $this->parseCsv($file->getPathname());
					
					#Check if fridge has any valid items in it
					if($itemArray != array()){
					$recipeArray = array ();
					
					# crate an array List of Recipe Entities
					foreach ($recipes as $recipe) {
						array_push($recipeArray, $this->populateRecipe($recipe));
					}
					
					# check for any matching reciepes and ingrediants 
					$recipeList = $this->checkRecipe($recipeArray, $itemArray);
					# get the recipe with the soonest expiring ingredient if multiple selections are available
					if(count($recipeList)>1 ){
					$firstRecipe = $this->firstToExpire($recipeList);
					}else
						$firstRecipe = $recipeList; 						
						$message = "You can cook ".$firstRecipe->name." !!!";
						$messageType='success';
					}
				}
				}
				
			}
		
		$request->getSession()->getFlashBag()->add(
            $messageType, $message);
		}


		return array (
			'form' => $form->createView()
		);
	}

	/*
	 * Parse the Input CSV file and create an array of Item Entities
	 * 
	 */
	protected function parseCsv($path) {
		$row = 1;
		$itemArray = array ();
		if (($handle = fopen($path, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$num = count($data);
				$row++;
				$arrayItem = array ();
				for ($c = 0; $c < $num; $c++) {
					array_push($arrayItem, $data[$c]);
				}
				$item = $this->populateItem($arrayItem);
				if ($item != null)
					array_push($itemArray, $item);
			}
		}
		return $itemArray;
	}

	/*
	 * Populate the Item Entity
	 * Do not set the Items which have passed there Expiration Dates
	 */
	protected function populateItem(array $item) {
		$expirationDate = \ DateTime :: createFromFormat('d/m/Y', $item[3]);
		$now = new \ DateTime();
		$dateDiff = $expirationDate->diff($now);
		if ($dateDiff->format('%R%a') < 0) {
			$itemEntity = new Item();
			$itemEntity->setName($item[0]);
			$itemEntity->setAmount((int) $item[1]);
			$itemEntity->setUnit($item[2]);
			$itemEntity->setUseBy($item[3]);

			return $itemEntity;
		} else
			return null;
	}

	/*
	 * Populate the Recipe Entity
	 */
	protected function populateRecipe(array $recipe) {
		$recipeEntity = new Recipe();
		$recipeEntity->setName($recipe['name']);
		$recipeEntity->setIngredients($recipe['ingredients']);
		return $recipeEntity;
	}

	/*
	 * Look at Recipe and match with ingrediants
	 * check ingrediant name and amount
	 * return all the matched recipecs
	 */
	protected function checkRecipe(array $recipeArray, array $itemArray) {
		$recipesAvailable = array();
		$recipeList = array();
		foreach ($recipeArray as $recipe) {

			$ingredientMissing = false;
			$count=0;
			foreach ($recipe->ingredients as $ingredient) {
				
				$itemMissing = true; //Flag to identify if an item from reciep is missing				
				foreach ($itemArray as $item) {
					
					/*
					 *  Check Recipe Ingrediants with items in the fridge
					 *  Compare Item Name, Amount and unit types
					*/
					if ($ingredient['item'] == $item->name && $ingredient['amount'] <= $item->amount && $ingredient['unit'] == $item->unit) {
												
						$recipe->ingredients[$count]['expiration'] = $item->useBy;// We add the expiration time of the ingredient for later use
						$itemMissing = false;
						break;
					}
						
				}
												
				// if a single Item is missing there is no need to check the rest
					if($itemMissing){									
						$ingredientMissing = true;
						break;
					}
					$count++;
			}
			
			#if all ingrediants are avalable then start Cooking and push the reciepe to the reciep list !!!
			if(!$ingredientMissing){
				array_push($recipeList,$recipe);
			}
		}
		return $recipeList;

	}

	/*
	 * Find the reciepe which has the soonest expiration date
	 */	
	protected function firstToExpire($recipes){
		$recipeList = $recipes;
		$minExpirationDate='30/12/2100';
		
		# Loop through all valid ingredients and find the one with the soonest expiration 
		foreach($recipes as $recipe){
			foreach($recipe->ingredients as $ingredient) {
					if($this->compareDate($minExpirationDate,$ingredient['expiration'])== -1)
						$minExpirationDate= $ingredient['expiration'];														
					
			}
						
		}

		## Loop through all valid recipes and find the one with the soonest expiring ingrediant
		foreach($recipes as $recipe){
			foreach($recipe->ingredients as $ingredient) {
				if($minExpirationDate == $ingredient['expiration'])
					return $recipe;
			}
		}

	}
	
	#Compare to date and determine which one is smaller
	protected function compareDate($d1,$d2){		
		$date1 = \ DateTime :: createFromFormat('d/m/Y', $d1);
		$date2 = \ DateTime :: createFromFormat('d/m/Y', $d2);
		$dateDiff = $date1->diff($date2);
		return ($dateDiff->format('%R%a') < 0) ? -1 : 1;
		
	}

	/**
	 * @Route("/", name="_demo")
	 * @Template()
	 */
	public function indexAction() {
		return array ();
	}

	/**
	 * @Route("/hello/{name}", name="_demo_hello")
	 * @Template()
	 */
	public function helloAction($name) {
		return array (
			'name' => $name
		);
	}

	
}