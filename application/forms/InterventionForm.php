<?php
class InterventionForm extends Zend_Form{
	public function init(){
		$IDuree = new Zend_Form_Element_Text('Duree');
		$ICommentaire = new Zend_Form_Element_Text('Commentaire');
		$ISubmit = new Zend_Form_Element_Submit('Ajouter');
		
		$IDuree->setLabel('Durée :');
		$IDuree->setRequired(true);
		
		$ICommentaire->setLabel('Commentaire :');
		$ICommentaire->setRequired(true);
		
		$ISubmit->setLabel('Ajouter');
		
		
		$this->setMethod('POST');
		
		$this->addElement($IDuree);
		$this->addElement($ICommentaire);
		$this->addElement($ISubmit);
	}
}
