<?php
class Pilote extends Zend_Db_Table_Abstract
{
	protected $_name='pilote';
	protected $_primary='id_pilote';
	protected $referenceMap=array(
			'code_ville'=>array(
					'columns'=>'code_ville',
					'refTableClass'=>'Ville'));
}