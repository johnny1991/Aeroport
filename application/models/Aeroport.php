<?php
class Aeroport extends Zend_Db_Table_Abstract
{
	protected $_name='aeroport';
	protected $_primary='id_aeroport';
	protected $_referenceMap=array(
			'ville'=>array(
					'columns'=>'code_ville',
					'refTableClass'=>'Ville')
	);
}