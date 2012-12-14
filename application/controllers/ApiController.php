<?php
class ApiController extends Zend_Controller_Action{
	
	public function init(){
		parent::init();
	}
	
	public function aeroportAction(){
	
		$this->_helper->layout->disableLayout();
		$table = new Aeroport();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => htmlentities($this->getParam('action'), ENT_QUOTES, 'UTF-8'),
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
	public function astreinteAction(){
		$this->_helper->layout->disableLayout();
		$table = new Astreinte();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => htmlentities($this->getParam('action'), ENT_QUOTES, 'UTF-8'),
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
	public function avionAction(){
		$this->_helper->layout->disableLayout();
		$table = new Avion();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => htmlentities($this->getParam('action'), ENT_QUOTES, 'UTF-8'),
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
	public function clientAction(){
	$this->_helper->layout->disableLayout();
		$table = new Client();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => htmlentities($this->getParam('action'), ENT_QUOTES, 'UTF-8'),
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
	public function etrebreveterAction(){
	$this->_helper->layout->disableLayout();
		$table = new EtreBreveter();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => 'etre_breveter',
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
	public function interventionAction(){
	$this->_helper->layout->disableLayout();
		$table = new Intervention();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => htmlentities($this->getParam('action'), ENT_QUOTES, 'UTF-8'),
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
	public function joursemaineAction(){
	$this->_helper->layout->disableLayout();
		$table = new JourSemaine();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => 'jour_semaine',
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
	public function ligneAction(){
	$this->_helper->layout->disableLayout();
		$table = new Ligne();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => htmlentities($this->getParam('action'), ENT_QUOTES, 'UTF-8'),
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
	public function maintenanceAction(){
	$this->_helper->layout->disableLayout();
		$table = new Maintenance();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => htmlentities($this->getParam('action'), ENT_QUOTES, 'UTF-8'),
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
	public function paysAction(){
	$this->_helper->layout->disableLayout();
		$table = new Pays();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => htmlentities($this->getParam('action'), ENT_QUOTES, 'UTF-8'),
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
	public function periodiciteAction(){
	$this->_helper->layout->disableLayout();
		$table = new Periodicite();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => htmlentities($this->getParam('action'), ENT_QUOTES, 'UTF-8'),
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
	public function piloteAction(){
	$this->_helper->layout->disableLayout();
		$table = new Pilote();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => htmlentities($this->getParam('action'), ENT_QUOTES, 'UTF-8'),
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
	public function remarqueAction(){
	$this->_helper->layout->disableLayout();
		$table = new Remarque();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => htmlentities($this->getParam('action'), ENT_QUOTES, 'UTF-8'),
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
	public function reservationAction(){
	$this->_helper->layout->disableLayout();
		$table = new Reservation();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => htmlentities($this->getParam('action'), ENT_QUOTES, 'UTF-8'),
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
	public function serviceAction(){
	$this->_helper->layout->disableLayout();
		$table = new Service();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => htmlentities($this->getParam('action'), ENT_QUOTES, 'UTF-8'),
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
	public function typeavionAction(){
	$this->_helper->layout->disableLayout();
		$table = new TypeAvion();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => 'type_avion',
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}	
	
	public function typeremarqueAction(){
	$this->_helper->layout->disableLayout();
		$table = new TypeRemarque();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => 'type_remarque',
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
	public function utilisateurAction(){
	$this->_helper->layout->disableLayout();
		$table = new Utilisateur();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => htmlentities($this->getParam('action'), ENT_QUOTES, 'UTF-8'),
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
	public function villeAction(){
	$this->_helper->layout->disableLayout();
		$table = new Ville();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => htmlentities($this->getParam('action'), ENT_QUOTES, 'UTF-8'),
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
	public function volAction(){
	$this->_helper->layout->disableLayout();
		$table = new Vol();
		$db = Zend_Registry::get('db');
		$actions = mb_strtolower(htmlentities($this->getParam('actions'), ENT_QUOTES, 'UTF-8'));
		$get = htmlentities($this->getParam('get'), ENT_QUOTES, 'UTF-8');
		$args = array(	'column' => htmlentities($this->getParam('column'), ENT_QUOTES, 'UTF-8'),
				'from' => htmlentities($this->getParam('action'), ENT_QUOTES, 'UTF-8'),
				'join' => htmlentities($this->getParam('join'), ENT_QUOTES, 'UTF-8'),
				'on' => htmlentities($this->getParam('on'), ENT_QUOTES, 'UTF-8'),
				'where' => htmlentities($this->getParam('where'), ENT_QUOTES, 'UTF-8'),
				'limit' => htmlentities($this->getParam('limit'), ENT_QUOTES, 'UTF-8'),
				'orderdesc' => htmlentities($this->getParam('orderdesc'), ENT_QUOTES, 'UTF-8'),
				'orderasc' => htmlentities($this->getParam('orderasc'), ENT_QUOTES, 'UTF-8'),
				'orwhere' => htmlentities($this->getParam('orwhere'), ENT_QUOTES, 'UTF-8'),
				'values' => htmlentities($this->getParam('values'), ENT_QUOTES, 'UTF-8'),
				'tostring' => htmlentities($this->getParam('tostring'), ENT_QUOTES, 'UTF-8'));
		
		if($actions == 'select'){
			$query = Aeroport_Api::createSelectQuery($args);
			if($args['tostring'] != NULL){
				print(json_encode($query));
			}else{
				print(json_encode($db->fetchAll($query)));
			}
		}elseif($actions == 'insert'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createInsertQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$db->insert($args['from'], $data);
			}
		}elseif($actions == 'update'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createUpdateQuery($args)));
			}else{
				$data = Aeroport_Api::createArrayQuery($args);
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->update($args['from'], $data, $where);
			}
		}elseif($actions == 'delete'){
			if($args['tostring'] != NULL){
				print(json_encode(Aeroport_Api::createDeleteQuery($args)));
			}else{
				$where = Aeroport_Api::concatParam($args['where'], ' AND ');
				$db->delete($args['from'], $where);
			}
		}elseif($get != NULL && $get == 'all'){
			print(json_encode($table->fetchAll()->toArray()));
		}elseif($get != NULL && $get != 'all'){
			print(json_encode($table->find($get)->current()->toArray()));
		}
	}
	
}