<?php
class Shop_SidebarController extends Zend_Controller_Action
{
	public function panierAction(){ // Block sibeBar Panier

		$sessionPanier = new Zend_Session_Namespace('panier');
		$TableVol = new Vol();
		$totalVol = 0;
		$produits = array();
		if($sessionPanier->content != NULL){
			foreach($sessionPanier->content as $id => $quantite) {
				$ids = explode("_",$id);
				$requete = $TableVol->select()->setIntegrityCheck(false)->from(array('v'=>'vol'))
				->join(array('l'=>'ligne'), 'v.numero_ligne = l.numero_ligne')
				->join(array('ad'=>'aeroport'),'ad.id_aeroport = l.id_aeroport_depart',array('ad.nom as aeroportDepart','ad.id_aeroport'))
				->join(array('vd'=>'ville'),'ad.code_ville = vd.code_ville',array('vd.code_pays as code_pays_Depart','vd.code_ville'))
				->join(array('pd'=>'pays'),'pd.code_pays = vd.code_pays',array('pd.nom as pays_Depart','pd.nom'))
				->join(array('aa'=>'aeroport'),'aa.id_aeroport = l.id_aeroport_arrivee',array('aa.nom as aeroportArrivee','aa.id_aeroport'))
				->join(array('va'=>'ville'),'aa.code_ville = va.code_ville',array('va.code_pays as code_pays_Arrivee','va.code_ville'))
				->join(array('pa'=>'pays'),'pa.code_pays = va.code_pays',array('pa.nom as pays_Arrive','pa.nom'))
				->joinLeft(array('av'=>'avion'),'av.id_avion = v.id_avion',array('av.nb_places'))
				->joinLeft(array('r'=>'reservation'),'(r.numero_ligne = v.numero_ligne) and (r.id_vol = v.id_vol)',array('SUM(r.nbreservation) as nbreservations'))
				->group('v.numero_ligne')
				->group('v.id_vol')
				->where('v.id_vol=?',$ids[1])
				->where('v.numero_ligne=?',$ids[0]);
				
				$vol = $TableVol->fetchRow($requete);
				if($vol->tarif_effectif == 0)
					$prix = $vol->tarif;
				else
					$prix = $vol->tarif_effectif;
				$totalVol += ($prix * $quantite);
				if( (($this->getRequest()->getParam('rm') != NULL) && ($this->getRequest()->getParam('rm') == $id)) || ($quantite == 0) )
				{
					unset($sessionPanier->content[$id]);
					$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]);
				}
				else{
					//$urlProd = new Application_Url();
					$produits[] = array($id,$vol,$quantite,$vol->numero_ligne."_".$vol->id_vol,$prix);
				}
			}
		}
		$this->view->produits = $produits;
		$this->view->sousTotal = $totalVol;

	}

	public function topVentesAction(){ // Block sibeBar Top Ventes
		$TableVol = new Vol();
		$requete = $TableVol->select()->setIntegrityCheck(false)->from(array('p'=>'Produit'))->where('actif=1')
		->joinLeft(array('cp'=>'CommandeProduit'),'p.id_produit = cp.id_produit',array('nbProduit' => 'count(cp.id_produit)'))
		->order('nbProduit desc')->group('id_produit')->limit(3);
		$produits1 = $TableVol->fetchAll($requete);
		$produits = array();
		foreach ($produits1 as $produit)
		{
			$urlProd = new Application_Url();
			$produits[] = array($produit,$urlProd::Rewrite($produit->designation));
		}
		//$this->view->produits = $produits;

	}

	public function init(){

		$this->_redirector = $this->_helper->getHelper('Redirector');

	}
}