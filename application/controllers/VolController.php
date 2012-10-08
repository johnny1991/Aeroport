<?php
class VolController extends Zend_Controller_Action
{
	public function ajoutAction()
	{
		$this->view->title="Ajouter un vol";
		echo $form= new FormulaireVol();
	}
	
	public function modifierAction()
	{
		$this->view->title="Modifier un vol";
	}
	
	public function supprimerAction()
	{
		$this->view->title="Supprimer un vol";
	}
}