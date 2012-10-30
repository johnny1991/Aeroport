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
}