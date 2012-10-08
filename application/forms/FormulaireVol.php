<?php
class FormulaireVol extends Zend_Form
{
	public function init(){
		
		$heureArrivee= new Zend_Form_Element_Text('heureArrivee');
		$heureArrivee->setLabel('Heure darrivée : ');
		
		$this->addElement($heureArrivee);		
	}
}