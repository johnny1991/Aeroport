<?php
class Shop_SidebarController extends Zend_Controller_Action
{
	public function panierAction(){ // Block sibeBar Panier

		$sessionPanier = new Zend_Session_Namespace('panier');
		$TableVol = new Vol();
		$totalProduit = 0;
		$produits = array();
		if($sessionPanier->content != NULL){
			foreach($sessionPanier->content as $id => $quantite) {
				$produit = $TableVol->find($id)->current();
				$totalProduit += ($produit->prix * $quantite);
				if( (($this->getRequest()->getParam('rm') != NULL) && ($this->getRequest()->getParam('rm') == $id)) || ($quantite == 0) )
				{
					unset($sessionPanier->content[$id]);
					$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]);
				}
				else{
					$urlProd = new Application_Url();
					$produits[] = array($id,$produit,$quantite,$urlProd::getUrlProduit($produit));
				}
			}
		}
		$this->view->produits = $produits;
		$this->view->sousTotal = $totalProduit;

	}

	/*	public function categorieAction(){ // Block sibeBar Categorie

	$tableCategories = new Shop_Model_Categorie;
	$tableSouscategorie = new Shop_Model_SousCategorie;
	$Total = array();
	foreach($tableCategories->fetchAll() as $categorie)
	{
	$requete = $tableSouscategorie->select()->from($tableSouscategorie)->where("id_categorie=?",$categorie->id_categorie);
	foreach($tableSouscategorie->fetchAll($requete) as $souscategorie)
		$SousTotal[] = $souscategorie->toArray();
	$Total[] = array($categorie->toArray(),$SousTotal);
	$SousTotal = array();
	}
	$this->view->total = $Total;
	}*/

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