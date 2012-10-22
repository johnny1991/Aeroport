<?php
class Utilisateur extends Zend_Db_Table_Abstract
{
	protected $_name='utilisateur';
	protected $_primary='login';
	protected $referenceMap=array(
			'code_ville'=>array(
					'columns'=>'code_ville',
					'refTableClass'=>'Ville'),
			'id_service'=>array(
					'columns'=>'id_service',
					'refTableClass'=>'Service')
	);
}