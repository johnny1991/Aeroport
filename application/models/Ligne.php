<?php
class Ligne extends Zend_Db_Table_Abstract
{
	protected $_name='ligne';
	protected $_primary=array('numero_ligne');
	protected $_referenceMap=array(
			'id_aeroport_origine'=>array(
					'columns'=>'id_aeroport',
					'refTableClass'=>'Aeroport'),
			'id_aeroport_depart'=>array(
					'columns'=>'id_aeroport',
					'refTableClass'=>'Aeroport'),
			'id_aeroport_arrivee'=>array(
					'columns'=>'id_aeroport',
					'refTableClass'=>'Aeroport')
	);
}