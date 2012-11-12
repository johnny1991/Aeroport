<?php
class Aeroport_Fonctions{
	
	/**
	 * Trie un tableau multidimentionnel dans l'ordre décroissant en fonction d'une valeur du tableau
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
	 * Récupère la valeur d'un paramétre dans l'url courant
	 * @param $param
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
}