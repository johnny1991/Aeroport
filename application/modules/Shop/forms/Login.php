<?php 
class Shop_Form_Login extends Zend_Form{

	public function init(){

		$Email = new Zend_Form_Element_Text("login");
		$Email->setLabel("Identifiant *");
		$Email->setRequired(true);
		$Email->setAttrib("size","20");
		$Email->addValidator("Alnum");

		$Pass = new Zend_Form_Element_Password("pass");
		$Pass->setRequired(true);
		$Pass->setLabel("Mot de passe *");
		$Pass->setAttrib("size","20");

		$Submit = new Zend_Form_Element_Submit("Connexion");
		$Submit->setLabel("CONNEXION");

		$this->setMethod("post");
		$this->addElement($Email);
		$this->addElement($Pass);
		$this->addElement($Submit);
	}
}
?>