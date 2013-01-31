<?php

/*
 * Cette page permet de créer la navigation (fil d'ariane)
* Dans cette navigation, j'y inclut toutes les categories (grace au foreach) ansi que toutes les sous-catégories
* J'y inclut aussi TOUT les produits (foreach)
* J'en profite aussi pour créer la redirection des produits/catégories/sous-catégories pour une lecture plus facile dans la barre d'adresse
* Je retourne le tableau final à Zend_navigation dans le bootstrap
*/

$array = array(
		'label'      => 'Accueil',
		'module'     => 'Shop',
		'controller' => 'index',
		'action'     => 'index',
		'title'		 =>	"Page d'accueil",
		'route'      => 'Accueil',
		'id'         => 'accueil',
		'pages'      => array(
				array(
						'label'      => 'Catalogue',
						'module'     => 'Shop',
						'controller' => 'produit',
						'action'     => 'catalogue',
						'title'		 =>	'Catalogue',
						'route'		 => 'catalogue',
						'id'         => 'catalogue'),
				array(
						'label'      => 'Nouveautés',
						'module'     => 'Shop',
						'controller' => 'index',
						'action'     => 'new',
						'title'		 =>	'Nouveautés',
						'route'		 => 'new')
				,array(
						'label'      => 'Top ventes',
						'module'     => 'Shop',
						'controller' => 'index',
						'action'     => 'top-ventes',
						'title'		 =>	'Top ventes',
						'route'		 => 'topVentes')
				,array(
						'label'      => 'Connexion Administration',
						'module'     => 'Shop',
						'controller' => 'administration',
						'action'     => 'login',
						'route'		 => 'menu',
						'title'		 =>	'Connexion Administration')
				,array(
						'label'      => 'Connexion',
						'module'     => 'Shop',
						'controller' => 'index',
						'action'     => 'login',
						'route'		 => 'connexionClient',
						'title'		 =>	'Connexion client')
				,array(
						'label'      => 'Créer un compte',
						'module'     => 'Shop',
						'controller' => 'client',
						'action'     => 'ajout',
						'route'		 => 'newCompte',
						'title'		 =>	'Créer un compte')
				,array(
						'label'      => 'Mon panier',
						'module'     => 'Shop',
						'controller' => 'client',
						'action'     => 'panier',
						'route'		 => 'panier',
						'title'		 =>	'Mon panier',
						'pages'=>
						array(
								array(
										'label'      => 'Adresse',
										'module'     => 'Shop',
										'controller' => 'client',
										'action'     => 'checkout-adresse',
										'title'		 =>	'Adresse',
										'pages'=>
										array(
												array(
														'label'      => 'Livraison',
														'module'     => 'Shop',
														'controller' => 'client',
														'action'     => 'checkout-mode-livraison',
														'title'		 =>	'Livraison',
														'pages'=>
														array(
																array(
																		'label'      => 'Paiement',
																		'module'     => 'Shop',
																		'controller' => 'client',
																		'action'     => 'checkout-mode-paiement',
																		'title'		 =>	'Paiement',
																		'pages'=>
																		array(
																				array(
																						'label'      => 'Confirmation',
																						'module'     => 'Shop',
																						'controller' => 'client',
																						'action'     => 'checkout-confirmation',
																						'title'		 =>	'Confirmation'),
																				array(
																						'label'      => 'Confirmation',
																						'module'     => 'Shop',
																						'controller' => 'client',
																						'action'     => 'commande-confirmer',
																						'title'		 =>	'Confirmation')
																		)
																)
														)
												)
										)
								)
						)
				)
				,array(
						'label'      => 'Tableau de bord',
						'module'     => 'Shop',
						'controller' => 'client',
						'action'     => 'tableau-bord',
						'route'		 => 'monCompte',
						'title'		 =>	'Tableau de bord',
						'pages'      => array(
								array(
										'label'      => 'Information du compte',
										'module'     => 'Shop',
										'controller' => 'client',
										'action'     => 'information-compte',
										'route'		 => 'informationCompte',
										'title'		 =>	'Information du compte')
								,array(
										'label'      => "Carnet d'adresses",
										'module'     => 'Shop',
										'controller' => 'client',
										'action'     => 'carnet-adresse',
										'route'		 => 'carnetAdresse',
										'title'		 =>	"Carnet d'adresses",
										'pages'      => array(
												array(
														'label'      => 'Gestion des adresses',
														'module'     => 'Shop',
														'controller' => 'client',
														'action'     => 'ajout-carnet-adresse',
														'route'		 => 'ajoutAdresse',
														'title'		 =>	'Gestion des adresses')
										)
								)
								,array(
										'label'      => "Mes commandes",
										'module'     => 'Shop',
										'controller' => 'client',
										'action'     => 'commande',
										'route'		 => 'mesCommandes',
										'title'		 =>	"Mes commandes",
										'pages'		 => array(
												array(
														'label'      => 'Fiche de commande',
														'module'     => 'Shop',
														'controller' => 'client',
														'action'     => 'fiche-commande',
														'route'		 => 'mesFichesCommandes',
														'title'		 =>	'Fiche de commande')
												,
												array(
														'label'      => 'Commentaires Commande',
														'module'     => 'Shop',
														'controller' => 'client',
														'action'     => 'commentaire',
														'title'		 =>	'Commentaires Commande')
										)
								)
						)
				)
		)
);

$TableVol = new Vol();
$front = $this->bootstrap('FrontController')->getResource('FrontController');
$router = $front->getRouter();

$NomCat = new Application_Url();

$requete4 = $TableVol->select()->setIntegrityCheck(false)->from(array('v'=>'vol'))
->join(array('l'=>'ligne'), 'v.numero_ligne = l.numero_ligne')
->join(array('ad'=>'aeroport'),'ad.id_aeroport = l.id_aeroport_depart')
->join(array('vd'=>'ville'),'ad.code_ville = vd.code_ville',array('vd.code_pays as code_pays_Depart','vd.code_ville'))
->join(array('pd'=>'pays'),'pd.code_pays = vd.code_pays',array('pd.nom as pays_Depart','pd.nom'))
->join(array('aa'=>'aeroport'),'aa.id_aeroport = l.id_aeroport_arrivee',array('aa.nom as aeroportArrivee','aa.id_aeroport'))
->join(array('va'=>'ville'),'aa.code_ville = va.code_ville',array('va.code_pays as code_pays_Arrivee','va.code_ville'))
->join(array('pa'=>'pays'),'pa.code_pays = vd.code_pays',array('pa.nom as pays_Arrive','pa.nom'))
;
$vols = $TableVol->fetchAll($requete4);
$array3 = array();
foreach ($vols as $vol)
{
	$array3[] = array(
			'label'	=> $vol->pays_Depart." vers ".$vol->pays_Arrive,
			'uri' 	=> '/itineraire/'.$vol->numero_ligne."_".$vol->id_vol,
			'id' 	=> $vol->numero_ligne."_".$vol->id_vol,
			'title' => $vol->numero_ligne."_".$vol->id_vol);
	$router->addRoute($vol->numero_ligne."_".$vol->id_vol, new Zend_Controller_Router_Route('itineraire/'.$vol->numero_ligne."_".$vol->id_vol, array('module' => 'Shop', 'controller' => 'produit', 'action' => 'fiche', 'id'=>$vol->numero_ligne."_".$vol->id_vol)));

}
$array["pages"][0]["pages"] = $array3;

return $array;
?>