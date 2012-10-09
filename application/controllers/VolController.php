<?php
class VolController extends Zend_Controller_Action
{
	public function ajoutAction()
	{
		$TableVol= new Vol;
		$this->view->title="Ajouter un vol";
		echo $form= new FormulaireVol();
		$insertion=$this->_getParam('insertion');
		if($insertion){
			$Vol=$TableVol->createRow();
			$Vol->id_copilote=$this->getRequest()->getPost('');
			$Vol->id_aeroport=$this->getRequest()->getPost('');
			$Vol->id_aeroport_arriver=$this->getRequest()->getPost('');
			$Vol->immatriculation=$this->getRequest()->getPost('');
			$Vol->id_periodicite=$this->getRequest()->getPost('');
			$Vol->id_pilote=$this->getRequest()->getPost('');
			$Vol->id_aeroport_partir=$this->getRequest()->getPost('');
			$Vol->heure_depart=$this->getRequest()->getPost('');
			$Vol->heure_arrivee=$this->getRequest()->getPost('');
			$Vol->heure_arrivee_effective=$this->getRequest()->getPost('');
			$Vol->date_depart=$this->getRequest()->getPost('');
			$Vol->save();
		}
	}

	public function modifierAction()
	{
		$this->view->title="Modifier un vol";
		$TableVol= new Vol;
		$numeroVol=$this->_getParam('numero');
		$Vol=$TableVol->find('numero')->current();
		$Vol->save();
	}

	public function supprimerAction()
	{
		$this->view->title="Supprimer un vol";
		$TableVol= new Vol;
		$numeroVol=$this->_getParam('numero');
		$Vol=$TableVol->find('numero')->current();
		$Vol->delete();
	}

	public function init(){
		parent::init();
	}
}