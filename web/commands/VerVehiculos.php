<?php

include_once('GenericCommand.php');
include_once('../classes/Vehiculo.php');

class VerVehiculos extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		// ayuda en pantalla
		$user_help_desk = 
			"Busca veh&iacute;culos por marca, modelo, combustible, patente o tipo de tracci&oacute;n.<br>" .
			"Acciones:<br><br>" .
			"<img src=\"images/user_edit.png\" />&nbsp;Edita usuario.<br>" .
			"<img src=\"images/reset_password.png\" />&nbsp;Resetea contrase&ntilde;a.<br>" .
			"<img src=\"images/trash.png\" />&nbsp;Elimina usuario.<br>";
			
		$this->addVar('user_help_desk', $user_help_desk);
		
		
		if (isset($fc->request->id)) {
			
			$exito = null;
			
			$parameters = array();
			
			// guardo el valor de busqueda para utilizarlo en "volver al listado" en "edita usuario"
			
			HTTP_session::set('id', $fc->request->id);
			
			$search_keyword = $fc->request->id;
			
			$parameters['id_usuario'] = $fc->request->id;
			$parameters['no borrado'] = null;
			
			$search_result = Vehiculo::seek($db, $parameters, null, null, 0, 10000);
			
			// var_dump($search_result);
			
			$this->addVar('search_result', $search_result);
			
			$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
			
			$this->addVar('row_number', $row_number);
			
			$exito = true;
			
			$this->addVar('exito', $exito);
			
			if (HTTP_Session::get('search_keyword_usuario', null) != null) {
				$this->addVar('search_keyword_usuario', HTTP_Session::get('search_keyword_usuario'));
			}
			else if (HTTP_Session::get('search_keyword_usuario_alt', null) != null) {
				$this->addVar('search_keyword_usuario_alt', HTTP_Session::get('search_keyword_usuario_alt'));
			}
		}
		
		$fv=array();
		
		$fv[0]="id";

		$this->initFormVars($fv);

		$this->processSuccess();
	}
}
?>