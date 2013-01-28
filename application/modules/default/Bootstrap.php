<?php

class Default_Bootstrap extends Zend_Application_Module_Bootstrap
{
	
	/*protected function _initLoad(){
		set_include_path(APPLICATION_PATH.'/modules/shop/models'. PATH_SEPARATOR . get_include_path());
	}*/
	

	protected function _initLibrairie(){
		$autoloader= Zend_Loader_Autoloader::getInstance();
		$autoloader->registerNamespace('Aeroport_');
	}

	protected function _initBreadcrumb(){
		/*$this->bootstrap("layout");
		$layout = $this->getResource("layout");
		$view=$layout->getView();

 
		$config=new Zend_Config(require APPLICATION_PATH.'/configs/navigation.php');
		$navigation=new Zend_Navigation();
		$view->navigation($navigation);
		$navigation->addPage($config);
	*/}

	protected function _initDisponibiliteAvion(){
		$TableMaintenance = new Maintenance();
		$TableAvion = new Avion();
		$currentDate = new Zend_Date();
		$Avions = $TableAvion->fetchAll();
		foreach ($Avions as $Avion)
		{
			$requete = $TableMaintenance->select()->where('date_prevue <=?',$currentDate->get('yyyy-MM-dd'))->where('id_avion =?',$Avion->id_avion);
			$Maintenance = $TableMaintenance->fetchAll($requete);
			if($Maintenance->count())
				$Avion->disponibilite_avion = false;
			else
				$Avion->disponibilite_avion = true;
			$Avion->save();
		}

	}
	
	protected function _initAccess(){
		$SRole = new Zend_Session_Namespace('Role');
		$identity = Zend_Auth::getInstance()->getStorage()->read();
		if(isset($identity->id_service)){
			$SRole->id_service = $identity->id_service;
		}
		else if(isset($identity->id_client)) {
			$SRole->id_service = "MEMBER"; 
		} else
		{
			$SRole->id_service = "NOT_LOGGED";
		}
		
	}


}

