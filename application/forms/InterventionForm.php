<?php
class InterventionForm extends Zend_Form{
	public function init(){
		$IDuree = new Zend_Form_Element_Text('Duree');
		$IDuree->setLabel('Durée :');
		$IDuree->setRequired(true);
		
		$ICommentaire = new Zend_Form_Element_Text('Commentaire');
		$ICommentaire->setLabel('Commentaire :');
		$ICommentaire->setRequired(true);
		
		$ISubmit = new Zend_Form_Element_Submit('Ajouter');
		$ISubmit->setLabel('Ajouter');
		
		$this->setMethod('POST');
		
		$this->addElement($IDuree);
		$this->addElement($ICommentaire);
		$this->addElement($ISubmit);
	}
}
