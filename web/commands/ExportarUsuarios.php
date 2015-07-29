<?php

include_once('GenericCommand.php');
include_once('../classes/Repuesto.php');
include_once('../classes/Plataforma.php');
include_once('../classes/Region.php');
include_once('../classes/Fabricante.php');
include_once('../classes/Motivo.php');
include_once('../classes/Usuario.php');

class ExportarUsuarios extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();

		$search_keyword = HTTP_session::get('search_keyword_usuario', null);
			
		$search_keyword_alt = HTTP_session::get('search_keyword_usuario_alt', null);

		if (!empty($search_keyword)) {
			// submit
			
			$exito = null;
			
			$search_result = Usuario::seekSpecial($db, $search_keyword, 'u.id_usuario', 'ASC', 0, 10000, false);
			
			// var_dump($search_result);
			
			$this->addVar('search_result', $search_result->data);
			
			$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
			
			$this->addVar('row_number', $row_number);
			
			$exito = true;
			
			$this->addVar('exito', $exito);
			
		}
		else if (!empty($search_keyword_alt)) {
			// submit
			
			$exito = null;
			
			$parameters = array();
			
			if (isset($search_keyword_alt['activo']) && isset($search_keyword_alt['dias'])) {
				$activo = $search_keyword_alt['activo'];
				$dias = $search_keyword_alt['dias'];
				$parameters['activo'] = $dias;
				$this->addVar('activo', $activo);
				$this->addVar('dias', $dias);
			}
			else if (isset($search_keyword_alt['inactivo']) && isset($search_keyword_alt['dias'])) {
				$inactivo = $search_keyword_alt['inactivo'];
				$dias = $search_keyword_alt['dias'];
				$parameters['inactivo'] = $dias;
				$this->addVar('inactivo', $inactivo);
				$this->addVar('dias', $dias);
			}
			
			$auto = $search_keyword_alt['auto'];
			
			if (isset($auto)) {
				$auto = $search_keyword_alt['auto'];
				$parameters['auto'] = '';
				$this->addVar('auto', $auto);
			}
			
			$km = $search_keyword_alt['km'];
			
			if (isset($km)) {
				$km = $search_keyword_alt['km'];
				$parameters['km'] = '';
				$this->addVar('km', $km);
			}
			
			$parameters['no borrado'] = null;
			
			$search_result = Usuario::seek($db, $parameters, 'u.id_usuario', 'ASC', 0, 10000, false);
						
			// var_dump($search_result);
			
			$this->addVar('search_result', $search_result->data);
			
			$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
			
			$this->addVar('row_number', $row_number);
			
			$exito = true;
			
			$this->addVar('exito', $exito);
			
		}
		
		header('Content-type: application/excel');
		
		header('Content-Disposition: attachment; filename="export.xls"');

		$this->processSuccess();
	}
}
?>