<?php
class Application_Url
{
	static public function Rewrite($nom){
		$temp = str_replace(array(' ',"'",'"'),'_',$nom);
		$temp = str_replace(array('[',']','(',')'),'',$temp);
		$temp = str_replace(array('é','è','ê','ë'),'e',$temp);
		$temp = str_replace(array('à','â','@'),'a',$temp);
		$temp = str_replace(array('ï','î'),'i',$temp);
		$temp = str_replace(array('+','-','*','/'),'',$temp);
		return $temp;
	}
	
	static public function getUrlProduit($produit){

		$categorieProduit = $produit->findDependentRowset('Shop_Model_CategorieProduit')->current();
		$sousCategorieProduit = $produit->findDependentRowset('Shop_Model_SousCategorieProduit')->current();
		$NomCat = new Application_Url();
		$Url = "";
		if($categorieProduit != NULL)
		{
			$Categorie = $categorieProduit->findParentRow('Categorie');
			$Url .= $NomCat::Rewrite($Categorie->libelle).'/';
		}
		else if($sousCategorieProduit != NULL)
		{
			$SousCategorie = $sousCategorieProduit->findParentRow('SousCategorie');
			$Categorie = $SousCategorie->findParentRow('Categorie');
				
			$Url .= $NomCat::Rewrite($Categorie->libelle).'/';
				
			$Url .= $NomCat::Rewrite($SousCategorie->libelle).'/';
		}
			
		$Url .= $NomCat::Rewrite($produit->designation);
		return $Url;
	}
	
}