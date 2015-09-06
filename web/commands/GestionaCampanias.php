<?php

include_once('GenericCommand.php');
include_once('../classes/Campania.php');

class GestionaCampanias extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		// ayuda en pantalla
		$user_help_desk = 
			"Busca campanias por nombre,direcci&oacute;n, correo o tel&eacute;fono.<br>" .
			"Acciones:<br><br>" .
			"<img src=\"images/detail.png\" border=0 width=16 height=16 />&nbsp;Detalle Campania.<br>" .
			"<img src=\"images/edit.png\" border=0 width=16 height=16 />&nbsp;Edita Campania.<br>" .
			"<img src=\"images/trash.png\" border=0 width=16 height=16 />&nbsp;Elimina Campania.<br>";
			
		$this->addVar('user_help_desk', $user_help_desk);
		
		$fv=array();
		
		ob_start();
		var_dump($fc->request);
		$result = ob_get_clean();
		
		$trace = debug_backtrace();
        trigger_error(
            'fc->request: ' . $result .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
		
		
		if (empty($_POST)) {
			// limpio variables
			HTTP_session::set('search_keyword_campania', null);
			
			HTTP_session::set('search_keyword_campania_alt', null);
		}
		else if (isset($fc->request->search_keyword_campania)) {
			// submit desde busqueda rapida, a la izquierda
			
			$exito = null;
			
			// guardo el valor de busqueda para utilizarlo en "volver al listado" en "edita campania"
			
			HTTP_session::set('search_keyword_campania', $fc->request->search_keyword_campania);
			
			// elimino el valor del form alternativo, para saber como se hizo la busqueda al volver a esta pantalla
			
			HTTP_session::set('search_keyword_campania_alt', null);
			
			$search_keyword = $fc->request->search_keyword_campania;
			
			$search_result = Campania::seekSpecial($db, $search_keyword);
			
			// var_dump($search_result);
			
			$this->addVar('search_result', $search_result);
			
			$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
			
			$this->addVar('row_number', $row_number);
			
			$exito = true;
			
			$this->addVar('exito', $exito);
			
			$fv[0]="search_keyword_campania";
			
			$this->initFormVars($fv);
			
		}
		else if (isset($fc->request->dummy)) {
			// submit desde form con controles
			
			$exito = null;
			
			$activa = true;
			
			$campania_activa = 1;
			
			if (!isset($fc->request->activa)) {
				$campania_activa = 0;
				$activa = false;
			}
			
			//var_dump($activa);
			
			$this->addVar('activa', $activa);
			
			$parameters = array();
			
			// guardo el valor de busqueda para utilizarlo en "volver al listado" en "utiliza repuesto"
			
			HTTP_session::set('search_keyword_campania_alt', array(
				'activa'			=> $campania_activa,
			));
						
			// elimino el valor del form alternativo, para saber como se hizo la busqueda al volver a esta pantalla
			
			HTTP_session::set('search_keyword_campania', null);
			
			$parameters['activa'] = $campania_activa;
			$parameters['manual'] = null;
			
			/*
			if (isset($fc->request->activo) && $dias != '') {
				$parameters['activo'] = $dias;
				$fv[]="activo";
			}
			
			$auto = $fc->request->auto;
			
			if (isset($auto)) {
				$parameters['auto'] = '';
				$fv[]="auto";
			}
			
			$km = $fc->request->km;
			
			if (isset($km)) {
				$parameters['km'] = '';
				$fv[]="km";
			}
			
			$parameters['no borrado'] = null;
			
			$parameters['identificado'] = null;
			*/
			
			$search_result = Campania::seek($db, $parameters, 'fecha_modificacion', 'ASC', 0, 10000);
			
			// var_dump($search_result);
			
			$this->addVar('search_result', $search_result);
			
			$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
			
			$this->addVar('row_number', $row_number);
			
			$exito = true;
			
			$this->addVar('exito', $exito);
			/*
			// opciones en combo de ciudades
			$this->addVar('options_ciudades', HTTP_Session::get('options_ciudades', null), null);
			
			// opciones en combo de radio estaciones
			$this->addVar('options_res', HTTP_Session::get('options_res', null), null);
		
			// opciones en combo de modelos
			$this->addVar('options_modelos', HTTP_Session::get('options_modelos', null), null);
			*/
			
			//$fv[]="activa";

			//$this->initFormVars($fv);
			
			
		}
		else if (isset($fc->request->search)) {
			// submit desde AgregaCampania o EditaCampania (volver al listado)
			
			$exito = null;
			
			// recupero el valor de busqueda
			
			$search_keyword = HTTP_session::get('search_keyword_campania');
			
			// elimino el valor del form alternativo, para saber como se hizo la busqueda al volver a esta pantalla
			
			HTTP_session::set('search_keyword_campania_alt', null);
			
			$search_result = Campania::seekSpecial($db, $search_keyword);
			
			// var_dump($search_result);
			
			$this->addVar('search_result', $search_result);
			
			$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
			
			$this->addVar('row_number', $row_number);
			
			$exito = true;
			
			$this->addVar('exito', $exito);
			
			// para escribir en textbox el valor de la busqueda
			
			$this->addVar('search_keyword_campania', $search_keyword);
								
		}
		else if (isset($fc->request->search_alt)) {
			// submit desde AgregaCampania o EditaCampania (volver al listado)
			
			$this->addVar('dias', $dias);
			$exito = null;
			$activa = false;
			
			$parameters = array();
			
			// recupero el valor de busqueda
			
			$search_keyword = HTTP_session::get('search_keyword_campania_alt');
			
			// elimino el valor del form alternativo, para saber como se hizo la busqueda al volver a esta pantalla
			
			HTTP_session::set('search_keyword_campania', null);
			
			if (isset($search_keyword['activa'])) {
				$activa = true;
				$parameters['activa'] = 1;
			}
			else {
				$parameters['activa'] = 0;
			}
			
			$this->addVar('activa', $activa);
			
			$search_result = Campania::seek($db, $parameters, 'fecha_modificacion', 'ASC', 0, 10000);
			
			// var_dump($search_result);
			
			$this->addVar('search_result', $search_result);
			
			$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
			
			$this->addVar('row_number', $row_number);
			
			$exito = true;
			
			$this->addVar('exito', $exito);
			/*
			// opciones en combo de ciudades
			$this->addVar('options_ciudades', HTTP_Session::get('options_ciudades', null), null);
			
			// opciones en combo de radio estaciones
			$this->addVar('options_res', HTTP_Session::get('options_res', null), null);
		
			// opciones en combo de modelos
			$this->addVar('options_modelos', HTTP_Session::get('options_modelos', null), null);
			*/
			/*
			// para que el text box mantenga su valor post submit
			$this->addVar('dias', $dias);
			
			// para que los checkboxes mantengan su estado post submit
			$this->addVar('activo_checado', $activo_checado);
			$this->addVar('auto_checado', $auto_checado);
			$this->addVar('km_checado', $km_checado);
			*/
			
			//$fv[]="activa";

			//$this->initFormVars($fv);
			
						
		}
		
		$this->processSuccess();
	}
}
?>