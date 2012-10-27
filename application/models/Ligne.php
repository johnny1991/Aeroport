<?php
class Ligne extends Zend_Db_Table_Abstract
{
	protected $_name='ligne';
	protected $_primary='numero_ligne';
	protected $_referenceMap=array(
			'aeroport_origine'=>array(
					'columns'=>'id_aeroport_origine',
					'refTableClass'=>'Aeroport',
					'refColumns'=>'id_aeroport'),
			'aeroport_depart'=>array(
					'columns'=>'id_aeroport_depart',
					'refTableClass'=>'Aeroport',
					'refColumns'=>'id_aeroport'),
			'aeroport_arrivee'=>array(
					'columns'=>'id_aeroport_arrivee',
					'refTableClass'=>'Aeroport',
					'refColumns'=>'id_aeroport')
	);
}