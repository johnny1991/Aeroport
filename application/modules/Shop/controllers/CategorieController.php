<?php
/*
class Shop_CategorieController extends Zend_Controller_Action
{
	public function ajoutAction(){ // OK

		$this->view->title = "Ajouter une catégorie/sous-catégorie";
		$form = new AjoutCategorie;
		$data = $this->_request->getPost();
		if(($this->getRequest()->isPost()) && ($form->isValid($data)))
		{
			if($data['newCategorie'] != "")
			{
				$TableCategorie = new Categorie();
				$Categorie = $TableCategorie->createRow();
				$Categorie->libelle = $data['newCategorie'];
				$Categorie->save();
				$message = "<div id='message_ok'><label>L'ajout de la catégorie '".$Categorie->libelle."' est réussi !!</label></div>";
				$this->_helper->FlashMessenger($message);
				$this->_redirector->gotoUrl('/categorie/liste');
			}
			else
			{
				$TableSousCategorie = new SousCategorie();
				$SousCategorie =$TableSousCategorie->createRow();
				$SousCategorie->libelle = $data['newSousCategorie'];
				$SousCategorie->id_categorie = $data['categorie'];
				$SousCategorie->save();
				$message = "<div id='message_ok'><label>L'ajout de la sous-catégorie '".$SousCategorie->libelle."' dans la catégorie '".$SousCategorie->findParentRow('Categorie')->libelle."' est réussi !!</label></div>";
				$this->_helper->FlashMessenger($message);
				$this->_redirector->gotoUrl('/categorie/liste/id/'.$data['categorie']);
			}
		}
		$form->populate($data);
		$this->view->form = $form;

	}

	public function updateAction(){

		$this->view->title = "Modifier une catégorie/sous-catégorie";
		$form = new UpdateCategorie;
		$TableCategorie = new Categorie();
		$data = $this->_request->getPost();
		if($this->getRequest()->isPost())
		{
			if($form->isValid($data))
			{
				if($data['newCategorie'] != "")
				{
					$Categorie = $TableCategorie->find($data['categorie1'])->current();
					$Categorie->libelle = $data['newCategorie'];
					$Categorie->save();
					$message = "<div id='message_ok'><label>La modification de la catégorie '".$Categorie->libelle."' est réussi !!</label></div>";
					$this->_helper->FlashMessenger($message);
					$this->_redirector->gotoUrl('/categorie/liste');
				}
				else
				{
					$TableSousCategorie = new SousCategorie();
					$SousCategorie = $TableSousCategorie->find($data['sousCategorie'])->current();
					$SousCategorie->libelle = $data['newSousCategorie'];
					$SousCategorie->id_categorie = $data['categorie'];
					$SousCategorie->save();
					$message = "<div id='message_ok'><label>La modification de la sous-catégorie '".$SousCategorie->libelle."' dans la catégorie '".$SousCategorie->findParentRow('Categorie')->libelle."' est réussi !!</label></div>";
					$this->_helper->FlashMessenger($message);
					$this->_redirector->gotoUrl('/categorie/liste/id/'.$data['categorie']);
				}
			}
			else if($data['categorie'] != 0)
			{
				$Categorie = $TableCategorie->find($data['categorie'])->current();
				$TableSousCategorie = new SousCategorie();
				$SousCategories = $TableSousCategorie->fetchAll($TableSousCategorie->select()->where('id_categorie=?',$Categorie->id_categorie));
				$form->getElement('sousCategorie')->addMultiOption('0','Choisissez une sous-catégorie');
				foreach($SousCategories as $SousCategorie)
					$form->getElement('sousCategorie')->addMultiOption($SousCategorie->id_souscategorie,$SousCategorie->libelle);
				if(isset($data['sousCategorie']) && ($data['sousCategorie']>0))
					$form->getElement('sousCategorie')->setValue($data['sousCategorie']);
			}
		}
		$form->populate($data);
		$this->view->form = $form;

	}

	public function deleteAction(){

		$TableCategorie = new Categorie;
		$TableCategorieProduit = new CategorieProduit;
		$TableSousCategorie = new SousCategorie;
		$TableSousCategorieProduit = new SousCategorieProduit;

		if($this->getRequest()->getParam('id_categorie'))
		{
			$Categorie = $TableCategorie->find($this->getRequest()->getParam('id_categorie'))->current();
			$requete = $TableCategorieProduit->select()->where('id_categorie=?',$this->getRequest()->getParam('id_categorie'));
			$CategorieProduits = $TableCategorieProduit->fetchAll($requete);
			if($CategorieProduits->count() > 0)
			{
				foreach ($CategorieProduits as $CategorieProduit) // Efface les produits de la categorie
					$CategorieProduit->delete();
			}
			$requete1 = $TableSousCategorie->select()->where('id_categorie=?',$this->getRequest()->getParam('id_categorie'));
			$SousCategories = $TableSousCategorie->fetchAll($requete1);
			if($SousCategories->count() > 0)
			{
				foreach ($SousCategories as $SousCategorie)
				{
					foreach ($SousCategorie->findDependentRowset('SousCategorieProduit') as $SousCategorieProduit)
						$SousCategorieProduit->delete();
					$SousCategorie->delete();
				}
			}
			$message = "<div id='message_ok'><label>La suppression de la catégorie '".$Categorie->libelle."' est réussi !!</label></div>";
			$Categorie->delete();
			$this->_helper->FlashMessenger($message);
			$this->_redirector->gotoUrl('/categorie/liste');
				
		}
		else if($this->getRequest()->getParam('id_souscategorie'))
		{
			$SousCategorie = $TableSousCategorie->find($this->getRequest()->getParam('id_souscategorie'))->current();
			$id = $TableCategorie->find($SousCategorie->id_categorie)->current()->id_categorie;
			$requete = $TableSousCategorieProduit->select()->where('id_souscategorie=?',$this->getRequest()->getParam('id_souscategorie'));
			$SousCategorieProduits = $TableSousCategorieProduit->fetchAll($requete);
			if($SousCategorieProduits->count() > 0)
			{
				foreach ($SousCategorieProduits as $SousCategorieProduit)
				{
					$CategorieProduit = $TableCategorieProduit->createRow();
					$CategorieProduit->id_categorie = $SousCategorieProduit->findParentRow('SousCategorie')->id_categorie;
					$CategorieProduit->id_produit = $SousCategorieProduit->id_produit;
					$CategorieProduit->save();
					$SousCategorieProduit->delete();
				}
			}
			$message = "<div id='message_ok'><label>La suppression de la sous-catégorie '".$SousCategorie->libelle."' dans la catégorie '".$SousCategorie->findParentRow('Categorie')->libelle."' est réussi !!</label></div>";
			$SousCategorie->delete();
			$this->_helper->FlashMessenger($message);
			$this->_redirector->gotoUrl('/categorie/liste/id/'.$id);
		}
		
	}

	public function listeAction(){ // OK
		
		$this->view->title = "Liste des catégories et sous-catégories";
		$TableCategorie = new Categorie();
		if($this->getRequest()->getParam('orderBy'))
			$orderBy = $this->getRequest()->getParam('orderBy');
		else
			$orderBy = "Id_Asc";

		$requete = $TableCategorie->select()->from($TableCategorie);

		if($this->getRequest()->getParam('id'))
		{
			$this->view->idCategorie = $this->getRequest()->getParam('id');
			$TableSousCategorie = new SousCategorie();
			$requete1 = $TableSousCategorie->select()->from($TableSousCategorie)->where('id_categorie=?',$this->getRequest()->getParam('id'));

			switch ($orderBy)
			{
				case "Id1_Asc": $requete1->order("id_souscategorie asc"); break;
				case "Id1_Desc": $requete1->order("id_souscategorie desc"); break;
				case "Nom1_Asc": $requete1->order("libelle asc"); break;
				case "Nom1_Desc": $requete1->order("libelle desc"); break;
			}
			$categories = $TableSousCategorie->fetchAll($requete1);
			if($categories->count() > 0)
				$this->view->sousCategorie = $categories;
			$this->view->HeadId1 = Application_Tableau_OrderColumn::orderColumns($this, "Id1",$orderBy,"idLigneSousCategorie","Id");
			$this->view->HeadNom1 = Application_Tableau_OrderColumn::orderColumns($this,"Nom1",$orderBy,"designationLigneSousCategorie","Nom");
		}

		switch ($orderBy)
		{
			case "Id_Asc": $requete->order("id_categorie asc"); break;
			case "Id_Desc": $requete->order("id_categorie desc"); break;
			case "Nom_Asc": $requete->order("libelle asc"); break;
			case "Nom_Desc": $requete->order("libelle desc"); break;
		}

		$this->view->HeadId = Application_Tableau_OrderColumn::orderColumns($this, "Id",$orderBy,"idLigneCategorie","Id");
		$this->view->HeadNom = Application_Tableau_OrderColumn::orderColumns($this,"Nom",$orderBy,"designationLigneCategorie","Nom");
		$Categories = $TableCategorie->fetchAll($requete);
		$this->view->categories = $Categories;
		
	}

	public function init(){ // OK
		
		$this->_helper->layout->setLayout('administration');
		$this->view->messages = $this->_helper->FlashMessenger->getMessages();
		$this->_redirector = $this->_helper->getHelper('Redirector');

		$SessionRole = new Zend_Session_Namespace('Role');
		$acl = new Application_Acl_Acl();
		if(!($acl->isAllowed($SessionRole->Role,$this->getRequest()->getControllerName(),$this->getRequest()->getActionName())))
			$this->_redirector->gotoUrl('');
		
	}
}
*/