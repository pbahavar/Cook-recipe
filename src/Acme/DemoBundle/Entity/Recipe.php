<?php
namespace Acme\DemoBundle\Entity;

class Recipe
{
    public $name;
    public $ingredients;
    
    
    public function setName($name){
    	$this->name = $name;
    }
    
    public function getName(){
    	return $this->name;
    }
    
    public function setIngredients($ingredients){
    	$this->ingredients = $ingredients;
    }
    
    public function getIngredients(){
    	return $this->ingredients;
    }
        
}
?>
