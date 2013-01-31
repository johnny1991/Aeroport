<?php
class Aeroport_Fonctions{
	
	/**
	 * Trie un tableau multidimentionnel dans l'ordre dÃ©croissant en fonction d'une valeur du tableau
	 * @param $array, $subkey
	 * @return array
	 */
	public static function array_arsort($array, $subkey){
	
		foreach($array as $key => $value) {
			$b[$key] = strtolower($value[$subkey]);
		}
		arsort($b);
		foreach($b as $key=>$value) {
			$c[] = $array[$key];
		}
		return $c;
		
	}
	
	/**
	 * Trie un tableau multidimentionnel dans l'ordre croissant en fonction d'une valeur du tableau
	 * @param $array, $subkey
	 * @return array
	 */
	public static function array_asort($array, $subkey){
	
		foreach($array as $key => $value) {
			$b[$key] = strtolower($value[$subkey]);
		}
		asort($b);
		foreach($b as $key=>$value) {
			$c[] = $array[$key];
		}
		return $c;
	
	}
	
	/**
	 * RÃ©cupÃ¨re la valeur d'un paramÃ©tre dans l'url courant
	 * @param $name
	 * @return value
	 */
	public static function getParam($name){
		
		$uri = $_SERVER['REQUEST_URI'];
		$explode = explode('/', $uri);
		$size = count($explode) - 1;
		
		$params['controller'] = $explode[1];
		
		if(isset($explode[2]))
			$params['action'] = $explode[2];
		else
			$params['action'] = 'index';

		for($i = 3; $i < $size; $i += 2){
			$params[$explode[$i]] = $explode[$i+1];
		}
		
		if(array_key_exists($name, $params))
			return $params[$name];
		else
			return false;
	}
	
	/**
	 * VÃ©rifie si les paramÃ¨tre passÃ©s sont valides
	 * @param array $param
	 * @return bool
	 */
	public static function validParam($params){

		$regex = array(
					'date' => '^[0-9]{4}([-][0-9]{2}){2}$',
					'idaeroport' => '^[a-zA-Z]{3}$',
					'int' => '^[0-9]*$',
					'error' => '^[0-9]{3}([-][0-9]{3})*$',
					'bool' => '^true$|^false$'
				);
		
		foreach($params as $param => $format){
			if(!preg_match('#'.$regex[$format].'#', $param)){
				return false;
			}	
		}
		
		return true;
	}
	
	public static function redirector($link){
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
		$redirector->gotoUrl($link);
	}
	
	public static function getResteDivision($number){
		$explode = explode('.', $number);
		return '0.'.$explode[1];
	}
	
	public static function getTemps($heure){
		$heureArrondit = floor($heure);
		$minute = self::getResteDivision($heure) * 60;
		$minuteArrondit = floor($minute);
		
		return $heureArrondit.'h'.$minuteArrondit.'min';
	}
	
	public static function filter($in) {
		$search = array ('@[éèêëÊË]@i','@[àâäÂÄ]@i','@[îïÎÏ]@i','@[ûùüÛÜ]@i','@[ôöÔÖ]@i','@[ç]@i');
		$replace = array ('e','a','i','u','o','c');
		return preg_replace($search, $replace, $in);
	}
	
	public static function getReste($int){
		$explode = explode('.', $int);
		$reste = '0.'.$explode[1];
	
		return $reste;
	}
}