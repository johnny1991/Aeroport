<?php
class Aeroport extends Zend_Db_Table_Abstract
{
	protected $_name='aeroport';
	protected $_primary='id_aeroport';
	protected $_dependentTables = array('Vol','Ligne');
	protected $_referenceMap=array(
			'Ville'=>array(
					'columns'=>'code_ville',
					'refTableClass'=>'Ville')
	);
}