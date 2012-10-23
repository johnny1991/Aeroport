<?php
class Aeroport_Planning {
	
	private $_tabJour = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
	private $_numDay;
	
	function __construct($numDay){
		$this->_numDay = $numDay;
	}
	
	function getTranslateDay(){
		return $this->_tabJour[($this->_numDay-1)];
	}
}