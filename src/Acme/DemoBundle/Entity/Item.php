<?php
namespace Acme\DemoBundle\Entity;

class Item
{
    public $name;
    public $amount;
    public $unit;
    public $useBy;
    
    public function setName($name){
    	$this->name = $name;
    }
    
    public function getName(){
    	return $this->name;
    }
    
    public function setAmount($amount){
    	$this->amount = $amount;
    }
    
    public function getAmount(){
    	return $this->amount;
    }
    
    public function setUnit($unit){
    	$this->unit = $unit;
    }
    
    public function getUnit(){
    	return $this->unit;
    }
    
    public function setUseBy($useBy){
    	$this->useBy = $useBy;
    }
    
    public function getuseBy(){
    	return $this->useBy;
    }
}
?>
