<?php
class VolController extends Zend_Controller_Action
{
	public function ajoutAction() // faire les controles de saisie /!\
	{
		$this->view->headScript()->appendFile('/js/jquery-ui-sliderAccess.js');
		$this->view->headScript()->appendFile('http://code.jquery.com/jquery-1.8.2.min.js');		
		$this->view->headScript()->appendFile('http://code.jquery.com/ui/1.8.24/jquery-ui.min.js');
		$this->view->headLink()->appendStylesheet('/css/jquery-ui-timepicker-addon.css');
		$this->view->headLink()->appendStylesheet('http://code.jquery.com/ui/1.8.23/themes/smoothness/jquery-ui.css');
		$TableLigne = new Ligne;
		$this->view->title="Ajouter un vol";
		$form= new FormulaireVol();
		$TableVol1=new Vol;
		Zend_Debug::dump($TableVol1->getLastId(4));
		if($this->getRequest()->isPost())
		{
			$data=$this->getRequest()->getPost();
			if($form->isValid($data))
			{
				$Ligne=$TableLigne->createRow();
				$Ligne->id_aeroport_origine='CDG';
				$Ligne->id_aeroport_depart=$form->getValue('aeroportDepart');
				$Ligne->id_aeroport_arrivee=$form->getValue('aeroportArrivee');
				$Ligne->heure_depart=$form->getValue('heureDepart');
				$Ligne->heure_arrivee=$form->getValue('heureArrivee');
				$Ligne->periodique=$form->getValue('periodicite');
				$Id=$Ligne->save();
				if($this->getRequest()->getPost('periodicite'))
				{
					$TablePeriodicite=new Periodicite;
					$jours=$form->getValue("jours");
					$Periode=$TablePeriodicite->createRow();
					$Periode->numero_ligne=1;
					$Periode->numero_jour=1;
					foreach ($jours as $jour){
						$Periode=$TablePeriodicite->createRow();
						$Periode->numero_ligne=$Id;
						$Periode->numero_jour=$jour;
						$Periode->save();
					}
				}
				else 
				{
					$TableVol=new Vol;
					$Vol=$TableVol->createRow();
					$Vol->numero_ligne=$Id;
					$Vol->id_aeroport_depart_effectif=$form->getValue('aeroportDepart');
					$Vol->id_aeroport_arrivee_effectif=$form->getValue('aeroportArrivee');
					$Vol->date_depart=$form->getValue('dateDepart');
					$Vol->date_arrivee=$form->getValue('dateArrivee');
					$Vol->save();
				}
				echo "insertion reussi !!!";
			}
			else{
				$form->populate($data);
				$this->view->Form=$form;
			}
		}
		else
		{
			$this->view->Form=$form;
		}
	}

	public function modifierAction()
	{
		$this->view->title="Modifier un vol";
		$TableVol= new Vol;
		$numeroVol=$this->_getParam('numero');
		$Vol=$TableVol->find($numeroVol)->current();
		$Vol->save();
	}

	public function supprimerAction()
	{
		$this->view->title="Supprimer un vol";
		$TableVol= new Vol;
		$numeroVol=$this->_getParam('numero');
		$Vol=$TableVol->find($numeroVol)->current();
		try{
			$Vol->delete();
		}catch(Exception $e){
			$this->view->erreur=$e->getMessage();
		}

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