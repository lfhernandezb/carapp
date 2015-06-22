<?php

include_once('GenericCommand.php');
include_once('../classes/RespuestaProveedor.php');

class VerConsultas extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		// ayuda en pantalla
		$user_help_desk = 
			"Muestra las las ocasiones en que el proveedor calz&oacute; en la b&uacute;squeda que hacen los usuarios en sus m&oacute;viles.<br>";
			
		$this->addVar('user_help_desk', $user_help_desk);
		
		
		if (isset($fc->request->id)) {
			
			$exito = null;
			
			$parameters = array();
			
			$parameters['id_proveedor'] = $fc->request->id;
			
			$search_result = RespuestaProveedor::seek($db, $parameters, 'rp.fecha_modificacion', 'DESC', 0, 10000);
			
			// var_dump($search_result);
			
			$this->addVar('search_result', $search_result);
			
			$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
			
			$this->addVar('row_number', $row_number);
			
			$exito = true;
			
			$this->addVar('exito', $exito);
			
			if (HTTP_Session::get('search_keyword_proveedor', null) != null) {
				$this->addVar('search_keyword_proveedor', HTTP_Session::get('search_keyword_proveedor'));
			}
			else if (HTTP_Session::get('search_keyword_proveedor_alt', null) != null) {
				$this->addVar('search_keyword_proveedor_alt', HTTP_Session::get('search_keyword_proveedor_alt'));
			}
		}
		
		$fv=array();
		
		$fv[0]="id";

		$this->initFormVars($fv);

		$this->processSuccess();
	}
}
?>