<?php
class Pilote extends Zend_Db_Table_Abstract
{
	protected $_name='pilote';
	protected $_primary='id_pilote';
	protected $_dependentTables = array('Vol');
	protected $referenceMap=array(
			'Ville'=>array(
					'columns'=>'code_ville',
					'refTableClass'=>'Ville'));
}