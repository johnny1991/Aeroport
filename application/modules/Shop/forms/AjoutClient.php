<?php
class Shop_Form_AjoutClient extends Zend_Form
{
	
	public function init(){

		$Prenom=new Zend_Form_Element_Text("prenom");
		$Prenom->setLabel("Prénom *");
		$Prenom->setRequired(true);
		$Prenom->addValidator('Alpha','',array('allowWhiteSpace' => true));
		
		$Nom=new Zend_Form_Element_Text("nom");
		$Nom->setLabel("Nom *");
		$Nom->setRequired(true);
		$Nom->addValidator('Alpha','',array('allowWhiteSpace' => true));
		

		$Email=new Zend_Form_Element_Text("mail");
		$Email->setLabel("Email *");
		$Email->setRequired(true);
		$Email->addValidator("EmailAddress");

		$Login=new Zend_Form_Element_Text("login");
		$Login->setLabel("Login *");
		$Login->setRequired(true);
		$Login->addValidator("Alnum");

		$Pass=new Zend_Form_Element_Password("password");
		$Pass->setLabel("Mot de passe *");
		$Pass->setRequired(true);
		$Pass->addValidator("StringLength",'',array('min' => 6,'max' => 12));
		
		$Pass1=new Zend_Form_Element_Password("password1");
		$Pass1->setLabel("Confirmer le mot de passe *");
		$Pass1->setRequired(true);
		$Pass1->addValidator("StringLength",'',array('min' => 6,'max' => 12));
		
		$Submit=new Zend_Form_Element_Submit("Valider");

		$this->setMethod("post");
		$this->addElement($Prenom);
		$this->addElement($Nom);
		$this->addElement($Email);
		$this->addElement($Login);
		$this->addElement($Pass);
		$this->addElement($Pass1);
		$this->addElement($Submit);
	}
}