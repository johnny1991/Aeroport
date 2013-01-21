<?php
class Application_Tableau_OrderColumn
{
	
	static public function orderColumns($controller, $nomOrder,$order,$class = null,$nom){ // OK
		$orderAsc = $nomOrder."_Asc";
		$orderDesc = $nomOrder."_Desc";
		$params = $controller->getRequest()->getParams();
		$Html = "<th ";
	
		if( (strstr($order, "_Desc")) && ($orderDesc == $order) )
			$Html .= "id='desc'";
		else if ( (strstr($order, "_Asc")) && ($orderAsc == $order) )
			$Html .= "id='asc'";
	
		$Html .="><a ";
		if($class!=null)
			$Html .=" class='".$class."' ";
		$Html .= "href='";
	
		if( (strstr($order, "_Asc")) && (strstr($order, $nomOrder)) )
			$params["orderBy"] = $nomOrder."_Desc";
		else
			$params["orderBy"] = $nomOrder."_Asc";
	
		$Html .= $controller->view->url($params)."'>".$nom."</a></th>";
		return $Html;
	}
}