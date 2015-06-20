<?php

include_once('GenericCommand.php');
include_once('../classes/Log.php');

class VerLogs extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		if (isset($fc->request->id)) {
			
			$exito = null;
			
			// guardo el valor de busqueda para utilizarlo en "volver al listado" en "edita usuario"
			
			HTTP_session::set('id', $fc->request->id);
			
			$search_keyword = $fc->request->id;
			
			$parameters['id_usuario'] = $fc->request->id;
			$parameters['no borrado'] = null;
			
			$search_result = Log::seek($db, $parameters, "l.fecha_modificacion", "DESC", 0, 10000);
			
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