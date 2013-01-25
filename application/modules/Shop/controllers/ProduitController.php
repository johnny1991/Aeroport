<?php
class Shop_ProduitController extends Zend_Controller_Action
{
	public function ajoutAction(){ // Ajout d'un article
		$this->view->title = "Ajouter un produit"; // Attribution du titre de la page
		$form = new Shop_Form_AjoutProduit(); // Nouveau formulaire Produit
		$form->setAction($this->view->url()); // On met l'url de la page actuelle en action dans le formulaire
		$data = $this->_request->getPost(); // Récupération des données en POST

		if(($this->getRequest()->isPost()) && ($form->isValid($data)))
		{
			// Si le formulaire est valide
			$tableProduit = new Shop_Model_Produit; // On récupère  la table produit
			$produit = $tableProduit->createRow(); // On créer un nouveau produit
			$produit->designation = ltrim($this->getRequest()->getPost('designation'), " "); // On enlève les espaces en début de chaine (s'il y en a) sinon pb de triage dans le catalogue
			$produit->description = $this->getRequest()->getPost('description');
			$produit->descriptionBreve = $this->getRequest()->getPost('descriptionBreve');
			$produit->prix = str_replace ( ",", ".", $this->getRequest()->getPost('prix')); // On remplace les virgules par des points sinon pb dans la bdd
			$produit->quantite = $this->getRequest()->getPost('quantite');
			$produit->actif = $this->getRequest()->getPost('actif');
			$id = $produit->save(); // On enregistre le produit
			$upload = new Zend_File_Transfer_Adapter_Http();
			$cheminPublic = "shop/img";

			$fullPath = APPLICATION_PATH."/../public/".$cheminPublic."/Produits/"; // On définit le chemin où l'on va mettre les images
			$upload->setDestination($fullPath);
			$imageTool = new Application_Image_Tools(); // On appelle ma classe perso
			$numeroPhoto = 1; // on initialise le numero de la photo à 1
			for($i = 1; $i <= 3; $i++)
			{
				if ($upload->isUploaded('photo'.$i))
				{
					// Si la photo est uploadé
					$photo = "photo".$numeroPhoto; // On donne un nom à la photo
					$upload->receive('photo'.$i); // On la receptionne
					$produit->$photo = $imageTool::enregistrerImages($upload->getFileName('photo'.$i),$id,$numeroPhoto,$upload->getMimeType('photo'.$i)); // On enregistre l'image avec le nom que l'on veut, son extention et son chemin, puis on retourne le nom final de l'image dans la bdd
					$imageTool::modifierTaille($fullPath.$produit->$photo,$upload->getMimeType('photo'.$i)); // On modifie la taille l'image pour quel s'affiche proprement dans le catalogue
					$imageTool::nouvelleThumbnail($fullPath.$produit->$photo, $id,$numeroPhoto,$upload->getMimeType('photo'.$i)); // On créer une Thumbnail pour les petites images
					unlink($upload->getFileName('photo'.$i)); // On supprime l'image temporaire
					$numeroPhoto++;
				}
			}
			$produit->save(); // On enregistre le nom des photos

			if(isset($data['categorie']) && ($data['categorie'] > 0) && isset($data['sousCategorie']) && ($data['sousCategorie'] == 0))
			{
				// Si l'on met le produit dans une catégorie
				$TableCategorieProduit = new Shop_Model_CategorieProduit;
				$categorieProduit = $TableCategorieProduit->createRow(); // On créer une ligne qui fera le lien entre le produit et la catégorie
				$categorieProduit->id_categorie = $data['categorie'];
				$categorieProduit->id_produit = $id;
				$categorieProduit->save(); // On enregistre dans la bdd
			}
			else if (isset($data['categorie']) && ($data['categorie'] > 0) && isset($data['sousCategorie']) && ($data['sousCategorie'] > 0))
			{
				// Si l'on met le produit dans une sous-catégorie
				$TableSousCategorieProduit = new Shop_Model_SousCategorieProduit;
				$sousCategorieProduit = $TableSousCategorieProduit->createRow(); // On créer une ligne qui fera le lien entre le produit et la sous-catégorie
				$sousCategorieProduit->id_souscategorie = $data['sousCategorie'];
				$sousCategorieProduit->id_produit = $id;
				$sousCategorieProduit->save(); // On enregistre dans la bdd

			}
			$message = "<div id='message_ok'><label>L'ajout du produit '".$produit->designation."' est réussi !!</label></div>";
			$this->_helper->FlashMessenger($message); // On envoie un message OK
			$this->_redirector->gotoUrl('liste_produit'); // On redirige vers la liste produit
		}
		else if(isset($data['categorie']) && ($data['categorie'] > 0))
		{
			// Si le formulaire n'est valide, on remet l'affichage des catégories
			$TableCategorie = new Shop_Model_Categorie;
			$Categorie = $TableCategorie->find($data['categorie'])->current();

			$TableSousCategorie = new Shop_Model_SousCategorie();
			$SousCategories = $TableSousCategorie->fetchAll($TableSousCategorie->select()->where('id_categorie=?',$Categorie->id_categorie));

			$form->getElement('sousCategorie')->addMultiOption('0','Choisissez une sous-catégorie');
			foreach($SousCategories as $SousCategorie)
				$form->getElement('sousCategorie')->addMultiOption($SousCategorie->id_souscategorie,$SousCategorie->libelle);
			if(isset($data['sousCategorie']) && ($data['sousCategorie']>0))
				$form->getElement('sousCategorie')->setValue($data['sousCategorie']);
		}

		$form->populate($data); // On peuple le formulaire
		$this->view->form = $form; // On affiche le formulaire dans la vue

	}

	public function rechercheAction(){ // Barre de recherche (dans le header)

		$this->_helper->layout->disableLayout(); // On désactive l'affichage du layout
		$form = new Shop_Form_SearchBar; // Nouveau formulaire Recherche
		$urlRecherche = "'".$this->view->baseUrl("recherche/")."'+document.getElementById('mot').value;";
		$form->getElement('Rechercher')->setAttrib('onclick',"window.location=$urlRecherche");
		$form->getElement('mot')->setAttrib('onKeyPress',"if (event.keyCode == 13) window.location=$urlRecherche");
		$this->view->form = $form; // On affiche le formulaire dans la vue

	}

	public function deleteAction(){ // Page de suppression d'un produit

		$tableProduit = new Shop_Model_Produit;
		$produit = $tableProduit->find($this->getRequest()->getParam('id'))->current();
		if($produit != NULL){
			// Si le produit existe
			$categorieProduit = $produit->findDependentRowset('Shop_Model_CategorieProduit')->current();
			$sousCategorieProduit = $produit->findDependentRowset('Shop_Model_SousCategorieProduit')->current();
			if($categorieProduit != NULL) // Si le produit est lié à une catégorie, on supprimer le lien
				$categorieProduit->delete();
			else if ($sousCategorieProduit != NULL) // Si le produit est lié à une sous-catégorie, on supprimer le lien
				$sousCategorieProduit->delete();
			$message = "<div id='message_ok'><label>La suppression du produit '".$produit->designation."' est réussi !!</label></div>"; // On affiche un message OK
			$produit->delete(); // On supprime le produit
		}
		else {
			// Si le produit n'existe pas , on affiche un message d'erreur
			$message = "<div id='message_nok'><label>Ce produit n'existe pas !!</label></div>";
		}
		$this->_helper->FlashMessenger($message);
		$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]); // on redigire vers la page précedente

	}

	public function statutAction(){ // Page de changement de statut d'un produit

		$tableProduit = new Shop_Model_Produit;
		$produit = $tableProduit->find($this->getRequest()->getParam('id'))->current();
		if($produit != NULL){
			// Si le produit existe
			if($produit->actif == 1)
			{
				// Si le produit est actif, on le désactive et on affiche un message
				$produit->actif = 0;
				$message = "<div id='message_ok'><label>La désactivation du produit '".$produit->designation."' est réussi !!</label></div>";
			}
			else
			{
				// Si le produit est inactif, on l'active et on affiche un message
				$produit->actif = 1;
				$message = "<div id='message_ok'><label>L'activation du produit '".$produit->designation."' est réussi !!</label></div>";
			}
			$produit->save(); // On enregistre le produit
		}
		else {
			// Si le produit n'existe pas, on affiche un message d'erreur
			$message = "<div id='message_nok'><label>Ce produit n'existe pas !!</label></div>";
		}
		$this->_helper->FlashMessenger($message);
		$this->_redirector->gotoUrl($_SERVER["HTTP_REFERER"]);

	}

	public function updateAction(){ // Page de modification d'un produit (Même principe que pour l'ajout d'un produit)

		$id = $this->getRequest()->getParam('id');
		$form = new Shop_Model_AjoutProduit();
		$form->setAction($this->view->url());
		$tableProduit = new Shop_Model_Produit;
		$produit = $tableProduit->find($id)->current();
		$this->view->title = "Modifier le produit $produit->designation"; // Attribution du titre de la page
		$data = $this->getRequest()->getPost(); // Récupération des données en POST
		if(($this->getRequest()->isPost()) && ($form->isValid($data)))
		{
			$produit->designation = ltrim($this->getRequest()->getPost('designation'), " ");
			$produit->description = $this->getRequest()->getPost('description');
			$produit->descriptionBreve = $this->getRequest()->getPost('descriptionBreve');
			$produit->prix = str_replace ( ",", ".", $this->getRequest()->getPost('prix'));
			$produit->quantite = $this->getRequest()->getPost('quantite');
			$produit->actif = $this->getRequest()->getPost('actif');
			$upload = new Zend_File_Transfer_Adapter_Http();
			$cheminPublic = "public/shop";

			$fullPath = APPLICATION_PATH."/../".$cheminPublic."/img/Produits/";
			$upload->setDestination($fullPath);
			$imageTool = new Application_Image_Tools();
			$numeroPhoto = 1;
			for($i = 1; $i <= 3; $i++){
				if ($upload->isUploaded('photo'.$i)){
					$photo = "photo".$i;
					if($produit->$photo != NULL){
						@unlink(APPLICATION_PATH."/../".$cheminPublic."/img/Produits/".$produit->$photo); // On supprime l'ancienne photo si une nouvelle photo va prendre ca place
						@unlink(APPLICATION_PATH."/../".$cheminPublic."/img/Produits/Thumbnails/".$produit->$photo); // On supprime l'ancienne photo si une nouvelle photo va prendre ca place
						$numeroPhoto = $i;
					}
					$upload->receive('photo'.$i);
					$produit->$photo = $imageTool::enregistrerImages($upload->getFileName('photo'.$i),$id,$numeroPhoto,$upload->getMimeType('photo'.$i));
					$imageTool::modifierTaille($fullPath.$produit->$photo,$upload->getMimeType('photo'.$i));
					$imageTool::nouvelleThumbnail($fullPath.$produit->$photo, $id,$numeroPhoto,$upload->getMimeType('photo'.$i));
					unlink($upload->getFileName('photo'.$i));
					$numeroPhoto++;
				}
			}

			$produit->save();

			if(isset($data['categorie']) && ($data['categorie'] == 0))
			{
				$categorieProduit =$produit->findDependentRowset('Shop_Model_CategorieProduit')->current();
				if($categorieProduit != NULL)
					$categorieProduit->delete();
				$sousCategorieProduit = $produit->findDependentRowset('Shop_Model_SousCategorieProduit')->current();
				if($sousCategorieProduit != NULL)
					$sousCategorieProduit->delete();
			}
			else if(isset($data['categorie']) && ($data['categorie'] > 0) && isset($data['sousCategorie']) && ($data['sousCategorie'] == 0))
			{
				$TableCategorieProduit = new Shop_Model_CategorieProduit;
				$categorieProduit = $produit->findDependentRowset('Shop_Model_CategorieProduit')->current();
				if($categorieProduit == NULL)
					$categorieProduit = $TableCategorieProduit->createRow();
				$categorieProduit->id_categorie = $data['categorie'];
				$categorieProduit->id_produit = $id;
				$categorieProduit->save();
				$sousCategorieProduit = $produit->findDependentRowset('Shop_Model_SousCategorieProduit')->current();
				if($sousCategorieProduit != NULL)
					$sousCategorieProduit->delete();
			}
			else if (isset($data['categorie']) && ($data['categorie'] > 0) && isset($data['sousCategorie']) && ($data['sousCategorie'] > 0))
			{
				$TableCategorieProduit = new Shop_Model_CategorieProduit;
				$categorieProduit = $produit->findDependentRowset('Shop_Model_CategorieProduit')->current();
				if($categorieProduit != NULL)
					$categorieProduit->delete();
				$TableSousCategorieProduit = new Shop_Model_SousCategorieProduit;
				$sousCategorieProduit = $produit->findDependentRowset('Shop_Model_SousCategorieProduit')->current();
				if($sousCategorieProduit == NULL)
					$sousCategorieProduit = $TableSousCategorieProduit->createRow();
				$sousCategorieProduit->id_souscategorie = $data['sousCategorie'];
				$sousCategorieProduit->id_produit = $id;
				$sousCategorieProduit->save();
			}

			$message = "<div id='message_ok'><label>La modification du produit '".$produit->designation."' est réussi !!</label></div>";
			$this->_helper->FlashMessenger($message);
			$this->_redirector->gotoUrl('liste_produit');
		}
		else
		{
			$data = array(
					"designation" => $produit->designation,
					"descriptionBreve" => $produit->descriptionBreve,
					"description" => $produit->description,
					"prix" => str_replace ( ".", ",", $produit->prix),
					"quantite" => $produit->quantite,
					"actif" => $produit->actif,
					"Ajouter" => "Ajouter");

			if($produit->photo1 != NULL)
				$data['photo1'] = $produit->photo1;

			$TableCategorie = new Shop_Model_Categorie;
			$TableSousCategorie = new Shop_Model_SousCategorie();

			if($produit->findDependentRowset('Shop_Model_CategorieProduit')->current() != NULL)
			{
				$data['categorie'] = $produit->findDependentRowset('Shop_Model_CategorieProduit')->current()->id_categorie;
				$Categorie = $TableCategorie->find($data['categorie'])->current();
					
				$SousCategories = $TableSousCategorie->fetchAll($TableSousCategorie->select()->where('id_categorie=?',$Categorie->id_categorie));
					
				$form->getElement('sousCategorie')->addMultiOption('0','Choisissez une sous-catégorie');
				foreach($SousCategories as $SousCategorie)
					$form->getElement('sousCategorie')->addMultiOption($SousCategorie->id_souscategorie,$SousCategorie->libelle);
			}
			else if($produit->findDependentRowset('Shop_Model_SousCategorieProduit')->current() != NULL)
			{
				$SousCategorie = $TableSousCategorie->find($produit->findDependentRowset('Shop_Model_SousCategorieProduit')->current()->id_souscategorie)->current();
				$data['sousCategorie'] = $SousCategorie->id_souscategorie;
				$data['categorie'] = $SousCategorie->id_categorie;
					
				$form->getElement('sousCategorie')->addMultiOption('0','Choisissez une sous-catégorie');
				$SousCategories = $TableSousCategorie->fetchAll($TableSousCategorie->select()->where('id_categorie=?',$data['categorie']));

				foreach($SousCategories as $SousCategorie1)
					$form->getElement('sousCategorie')->addMultiOption($SousCategorie1->id_souscategorie,$SousCategorie1->libelle);
				$form->getElement('sousCategorie')->setValue($data['sousCategorie']);
			}
			$this->view->produit = $produit;
		}

		$form->populate($data);
		$this->view->form = $form;

	}

	public function listeAction(){ // Page Liste produit (coté administrateur)
		$this->view->title = "Catalogue Produit"; // Attribution du titre de la page
		$tableProduit = new Shop_Model_Produit;

		if($this->getRequest()->getParam('orderBy')) // On récupère l'ordre dans les paramètres
			$orderBy = $this->getRequest()->getParam('orderBy');
		else
			$orderBy = "Id_Desc"; // Sinon on l'initialise

		$requete = $tableProduit->select()->from($tableProduit);

		switch ($orderBy) // Selon la valeur de $orderBy, les produits seront affiché differemment
		{
			case "Id_Asc": $requete->order("id_produit asc"); break;
			case "Id_Desc": $requete->order("id_produit desc"); break;
			case "Designation_Asc": $requete->order("designation asc"); break;
			case "Designation_Desc": $requete->order("designation desc"); break;
			case "Prix_Asc": $requete->order("prix asc"); break;
			case "Prix_Desc": $requete->order("prix desc"); break;
			case "Quantite_Asc": $requete->order("quantite asc"); break;
			case "Quantite_Desc": $requete->order("quantite desc"); break;
			case "Date_Asc": $requete->order("date asc"); break;
			case "Date_Desc": $requete->order("date desc"); break;
			case "Actif_Asc": $requete->order("actif asc"); break;
			case "Actif_Desc": $requete->order("actif desc"); break;
		}

		/* La fonction orderColumns dans Application_Tableau_OrderColumn permet de créer les liens pour trier
		 * les elements comme l'on souhaite
		* */
		$this->view->HeadId = Application_Tableau_OrderColumn::orderColumns($this, "Id",$orderBy,"idLigneProduit","Id");
		$this->view->HeadDesignation = Application_Tableau_OrderColumn::orderColumns($this,"Designation",$orderBy,"designationLigneProduit","Désignation");
		$this->view->HeadPrix = Application_Tableau_OrderColumn::orderColumns($this,"Prix",$orderBy,"prixLigneProduit","Prix");
		$this->view->HeadQuantite = Application_Tableau_OrderColumn::orderColumns($this,"Quantite",$orderBy,"quantiteLigneProduit","Quantité");
		$this->view->HeadDate = Application_Tableau_OrderColumn::orderColumns($this,"Date",$orderBy,"dateLigneProduit","Date et Heure");
		$this->view->HeadActif = Application_Tableau_OrderColumn::orderColumns($this,"Actif",$orderBy,"actifLigneProduit","Actif");
		$produits = $tableProduit->fetchAll($requete);
		$this->view->order = $orderBy;

		$paginator = Zend_Paginator::factory($produits); // Zend_Paginator permet d'indexer les pages
		$paginator->setItemCountPerPage($this->view->nbProduits);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->pagination = $this->view->paginationControl($paginator, 'Sliding', 'pagination.phtml',array("param"=>$this->getAllParams()));
		$this->view->paginator = $paginator;

	}

	public function catalogueAction(){ // Page de catalogue produit


		$this->view->formRecherche = new RechercheLigne();

		$this->view->title = "Catalogue"; // Attribution du titre de la page
		$this->_helper->layout->setLayout('categories');
		$tableVol = new Vol();
		$requete = $tableVol->select()->setIntegrityCheck(false)->from(array('v'=>'vol'))
		->join(array('l'=>'ligne'), 'v.numero_ligne = l.numero_ligne')
		->join(array('ad'=>'aeroport'),'ad.id_aeroport = l.id_aeroport_depart',array('ad.nom as aeroportDepart','ad.id_aeroport'))
		->join(array('aa'=>'aeroport'),'aa.id_aeroport = l.id_aeroport_arrivee',array('aa.nom as aeroportArrivee','aa.id_aeroport'));
		$this->view->recherche = false;
		$url="";
		$url1="";
		$url = '/catalogue/';
		$NomCat = new Application_Url();
		if($this->getRequest()->getParam('mot'))
		{
			// Si l'on recherche un mot (barre de recherche)
			$mot = $this->getRequest()->getParam('mot');
			$url = '/recherche/'.$mot.'/';

			$this->view->title = "Recherche pour le mot : ".$mot; // Attribution du titre de la page

			$db = $tableProduit->getAdapter();
			$requete->where('('.$db->quoteInto("p.id_produit =?",$mot) . ' OR
					' . $db->quoteInto("p.designation LIKE ?","%".$mot."%") . ' OR
					' . $db->quoteInto("p.description LIKE ?","%".$mot."%") . ' OR
					' . $db->quoteInto("p.descriptionBreve LIKE ?","%".$mot."%").')');
			$this->view->navigation()->findOneBy("id","catalogue")->setVisible(true)->setLabel("Résultat de recherche pour : '".$mot."'")->setTarget('test');
			$this->view->recherche = true;
		}
		else{
			if($this->getParam('Rechercher')){
				if($this->getParam('dateDepart')){
					$requete->where('date_depart=?',$this->getParam('dateDepart'));
				}
			}
		}


		$form = new Zend_Form(); // On créer un formulaire pour trier les articles
		$Trie = new Zend_Form_Element_Select('orderBy');
		$Trie->addMultiOption(0,'Trier par :');
		$Trie->addMultiOption('_Asc','Aeroport de départ');
		$Trie->addMultiOption('Prix_Desc',"Aeroport d'arrivée");
		$Trie->addMultiOption('Designation_Asc','Date de départ');
		$Trie->addMultiOption('Designation_Desc',"Date d'arrivée");
		$Trie->addMultiOption('Quantite_Asc','Places restantes');
		$form->addElement($Trie);
		$form->setMethod("get");
		$form->setName("Trie");

		$params = $this->getRequest()->getParams();

		if(isset($params['page']))
			$url1 = $params['page'];
		$Trie->setAttrib('onChange',"window.location='".$this->view->baseUrl($url."'+document.getElementById('orderBy').value+'/".$url1."'"));

		$params = $this->getAllParams();
		$form->populate($params);

		$this->view->form = $form;

		if($this->getRequest()->getParam('orderBy'))
			$orderBy = $this->getRequest()->getParam('orderBy');
		else
			$orderBy = "Id_vol_Asc";

		switch ($orderBy)
		{
			case "Id_Asc": $requete->order("id_vol asc"); break;
			case "Id_Asc": $requete->order("id_vol desc"); break;
			case "Prix_Asc": $requete->order("p.prix asc"); break;
			case "Prix_Desc": $requete->order("p.prix desc"); break;
			case "Quantite_Asc": $requete->order("quantite asc"); break;
			case "Quantite_Desc": $requete->order("quantite desc"); break;
			case "Date_Asc": $requete->order("date_depart asc"); break;
			case "Date_Desc": $requete->order("date_depart desc"); break;
			case "Date1_Asc": $requete->order("date_arrivee asc"); break;
			case "Date1_Desc": $requete->order("date_arrivee desc"); break;
		}

		$paginator = Zend_Paginator::factory($tableVol->fetchAll($requete));
		$paginator->setItemCountPerPage($this->view->nbElements);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->pagination = $this->view->paginationControl($paginator, 'Sliding', 'pagination.phtml',array("param"=>$this->getAllParams()));
		$this->view->paginator = $paginator;

	}

	public function ficheAction(){ // Fiche d'un produit

		$form = new Shop_Form_AjoutProduitPanier;
		$panier = new Zend_Session_Namespace('panier');
		if (!($this->getRequest()->getParam('layout')))
			$this->_helper->layout->setLayout('categories');
		$tableProduit = new Shop_Model_Produit;
		$produit = $tableProduit->find($this->getRequest()->getParam('id'))->current();
		if ((!($this->getRequest()->getParam('layout'))) && $produit->actif ==0)
			$this->_redirector->gotoUrl($_SERVER['HTTP_REFERER']);

		$navigation = Zend_Registry::get('navigation');

		if($produit != NULL){
			if($this->getRequest()->isPost())
			{
				$data = $this->getRequest()->getPost();
				if($form->isValid($data))
				{
					if($produit->quantite >= $data['quantite'])
						$panier->content[$data['id']] = $data['quantite'];
					else
					{
						$panier->content[$data['id']] = $produit->quantite;
						$form->getElement('quantite')->addError("Maximum : ".$produit->quantite);
						$form->getElement('quantite')->setValue($produit->quantite);
					}
				}
			}

			$MiseEnLigne = new Zend_Date($produit->date, 'dd-MM-yy');
			if(Zend_Date::now() < $MiseEnLigne->addDay("15"))
				$this->view->Nouveaute = true;
			else
				$this->view->Nouveaute = false;

			$categorieProduit = $produit->findDependentRowset('Shop_Model_CategorieProduit')->current();
			$sousCategorieProduit = $produit->findDependentRowset('Shop_Model_SousCategorieProduit')->current();

			if($categorieProduit != NULL)
			{
				$navigation->findOneBy("id",$categorieProduit->findParentRow('Categorie')->id_categorie)->setActive(true);
			}
			else if($sousCategorieProduit != NULL)
			{
				$SousCategorie = $sousCategorieProduit->findParentRow('SousCategorie');
				$Categorie = $SousCategorie->findParentRow('Categorie');

				$navigation->findOneBy("id",$Categorie->id_categorie)->setActive(true);
				$navigation->findOneBy("id",$Categorie->id_categorie.'-'.$SousCategorie->id_souscategorie)->setActive(true);

			}

			$navigation->findOneBy("id","p_".$produit->id_produit)->setActive(true);
			$this->view->title = $produit->designation; // Attribution du titre de la page
			if($produit ==  NULL)
				$this->_redirector->gotoUrl('/produit/catalogue');
			if (($this->getRequest()->getParam('layout')))
				$form->getElement('quantite')->setValue($produit->quantite);
			$this->view->produit = $produit;
			$form->getElement('id')->setValue($this->getRequest()->getParam('id'));
			$cheminPublic = 'public/shop';
			$this->view->form = $form;
			if(($this->view->produit->photo1 == NULL) || (!file_exists(APPLICATION_PATH.'/../'.$cheminPublic.'/img/Produits/'.$this->view->produit->photo1)))
				$this->view->produit->photo1 = "BigNoPicture.png";
		}
		else
			$this->_redirector->gotoUrl('liste_produit');

	}

	public function blocAction(){ // Block produit (que l'on retrouve dans le catalogue, page d'accueil ...)

		$this->_helper->layout->setLayout('categories');
		//$tableProduit = new Shop_Model_Produit;
		$tableVol = new Vol();

		$this->view->produit = $tableVol->find($this->getRequest()->getParam('id_vol'), $this->getRequest()->getParam('numero_ligne'))->current();
		$MiseEnLigne = new Zend_Date($this->view->produit->date_depart, 'dd-MM-yy');
		$cheminPublic = 'shop/public';
		if(Zend_Date::now() < $MiseEnLigne->addDay("15"))
			$this->view->Nouveaute = true;
		else
			$this->view->Nouveaute = false;
		$imageTool = new Application_Image_Tools();
		/*if(($this->view->produit->photo1 != NULL) && (file_exists(APPLICATION_PATH.'/../'.$cheminPublic.'/img/Produits/'.$this->view->produit->photo1)))
			$this->view->TaillePhoto = $imageTool::adaptationTailleImage(APPLICATION_PATH.'/../'.$cheminPublic.'/img/Produits/'.$this->view->produit->photo1,202,140);
		else{
		$this->view->produit->photo1 = "NoPicture.png";
		$this->view->TaillePhoto = array(202,140);
		}*/

		//$categorieProduit = $this->view->produit->findDependentRowset('Shop_Model_CategorieProduit')->current();
		//$sousCategorieProduit = $this->view->produit->findDependentRowset('Shop_Model_SousCategorieProduit')->current();

		//$urlProd = new Application_Url();

		$this->view->url = 'test';//$urlProd::getUrlProduit($this->view->produit);

	}

	public function affichageSousCategorieAction(){ // Affichage des sous-catégories (dans l'ajout produit) en AJAX

		$this->_helper->layout->disableLayout();
		$TableSousCategorie = new Shop_Model_SousCategorie();
		$SousCategories = $TableSousCategorie->fetchAll($TableSousCategorie->select()->where('id_categorie=?',$this->getParam('categorie')));
		echo "<option value='0' selected='selected'>Choisissez une sous-catégorie</option>";
		foreach ($SousCategories as $SousCategorie)
			echo "<option value='".$SousCategorie->id_souscategorie."'>".$SousCategorie->libelle."</option>";

	}

	public function init(){

		$this->_helper->layout->setLayout('administration'); // Layout administration de base
		$this->view->messages = $this->_helper->FlashMessenger->getMessages(); // Création des messages que l'on affiche dans certaines pages
		$this->_redirector = $this->_helper->getHelper('Redirector');

		$TableParametre = new Shop_Model_Parametre();
		$Parametre = $TableParametre->fetchRow();
		$this->view->nbProduits = $Parametre->nbProduits;
		$this->view->nbElements = $Parametre->nbElements;

		$SessionRole = new Zend_Session_Namespace('Role');  // Récupération de la session Role (definit dans le bootsrap)
		$acl = new Application_Acl_Acl();
		if(!($acl->isAllowed($SessionRole->Role,$this->getRequest()->getControllerName(),$this->getRequest()->getActionName()))) // Si l'utilisateur n'a pas le droit d'acceder à cette page, on le redirige vers une page d'erreur
			$this->_redirector->gotoUrl('');
	}

}