<?php
class PlanningController extends Zend_Controller_Action
{
	public function planningAction(){
		
	}
	
	public function listevolAction(){
		$timestamp = $this->getParam('date') / 1000;
		
		$NumJour = new Aeroport_Planning(date('N', $timestamp));
		$currentDay = $NumJour->getTranslateDay();
		
	}
	
	public function init(){
		parent::init();
		
		$this->view->headLink()->appendStylesheet('/css/calendar.jquery.css');
		$this->view->headScript()->offsetSetFile(1000, '/js/calendar.jquery.js');
	}
}