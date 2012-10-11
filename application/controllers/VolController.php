<?php
class VolController extends Zend_Controller_Action
{
	public function ajoutAction()
	{
		$TableVol = new Vol;
		$TablePays = new Pays;

		$this->view->title="Ajouter un vol";
		echo $form= new FormulaireVol($TablePays,NULL);
		$insertion=$this->_getParam('insertion');
		if($insertion){
			echo 'hey';
			$Vol=$TableVol->createRow();
			$Vol->id_aeroport_arriver=$this->getRequest()->getPost('aeroportArrivee');
			$Vol->id_periodicite=$this->getRequest()->getPost('periodicite');
			$Vol->id_aeroport_partir=$this->getRequest()->getPost('aeroportDepart');
			$Vol->id_aeroport='CDG';//$this->getRequest()->getPost('aeroportDepart');
			$Vol->heure_depart=$this->getRequest()->getPost('heureDepart');
			$Vol->heure_arrivee=$this->getRequest()->getPost('heureArrivee');
			$Vol->date_depart=$this->getRequest()->getPost('dateDepart');
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

	public function rechercheaeroportAction()
	{
		$this->_helper->layout->disableLayout();
		$pays=$this->_getParam('pays');
		$tableAeroport = new Aeroport;
		$requete=$tableAeroport->select()
		->setIntegrityCheck(false)
		->from(array('ae'=>'aeroport'),array('ae.nom','ae.code_ville','ae.id_aeroport'))
		->join(array('v'=>'ville'),'v.code_ville = ae.code_ville',array('v.code_pays'))
		->where('code_pays=?',$pays);
		$aeroports=$tableAeroport->fetchAll($requete);
		foreach ($aeroports as $aeroport){
			echo '<option value="'.$aeroport->id_aeroport.'">'.$aeroport->nom.'</option>';
		}
	}

	public function init(){
		parent::init();
	}
}