<?php
class PlanningController extends Zend_Controller_Action
{
	public function planningAction(){
		
	}

	public function init(){
		parent::init();
		
		$this->view->headLink()->appendStylesheet('/css/calendar.jquery.css');
		$this->view->headScript()->offsetSetFile(1000, '/js/calendar.jquery.js');
	}
}