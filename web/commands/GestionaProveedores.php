<?php

include_once('GenericCommand.php');
include_once('../classes/Proveedor.php');

class GestionaProveedores extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		// ayuda en pantalla
		$user_help_desk = 
			"Busca proveedores por nombre,direcci&oacute;n, correo o tel&eacute;fono.<br>" .
			"Acciones:<br><br>" .
			"<img src=\"images/detail.png\" border=0 width=16 height=16 />&nbsp;Detalle Proveedor.<br>" .
			"<img src=\"images/edit.png\" border=0 width=16 height=16 />&nbsp;Edita Proveedor.<br>" .
			"<img src=\"images/help.png\" border=0 width=16 height=16 />&nbsp;Consultas donde fue incluido el proveedor.<br>" .
			"<img src=\"images/trash.png\" border=0 width=16 height=16 />&nbsp;Elimina Proveedor.<br>";
			
		$this->addVar('user_help_desk', $user_help_desk);
		
		$fv=array();
		
		if (empty($_POST)) {
			// limpio variables
			HTTP_session::set('search_keyword_proveedor', null);
			
			HTTP_session::set('search_keyword_proveedor_alt', null);
		}
		else if (isset($fc->request->search_keyword_proveedor)) {
			// submit desde busqueda rapida, a la izquierda
			
			$exito = null;
			
			// guardo el valor de busqueda para utilizarlo en "volver al listado" en "edita proveedor"
			
			HTTP_session::set('search_keyword_proveedor', $fc->request->search_keyword_proveedor);
			
			// elimino el valor del form alternativo, para saber como se hizo la busqueda al volver a esta pantalla
			
			HTTP_session::set('search_keyword_proveedor_alt', null);
			
			$search_keyword = $fc->request->search_keyword_proveedor;
			
			$search_result = Proveedor::seekSpecial($db, $search_keyword);
			
			// var_dump($search_result);
			
			$this->addVar('search_result', $search_result);
			
			$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
			
			$this->addVar('row_number', $row_number);
			
			$exito = true;
			
			$this->addVar('exito', $exito);
			
			$fv[0]="search_keyword_proveedor";
			
			$this->initFormVars($fv);
			
		}
		else if (isset($fc->request->id_mantencion_base)) {
			// submit desde form con controles
			
			$exito = null;
			
			$id_mantencion_base = $fc->request->id_mantencion_base;
			
			$parameters = array();
			
			// guardo el valor de busqueda para utilizarlo en "volver al listado" en "utiliza repuesto"
			
			HTTP_session::set('search_keyword_proveedor_alt', array(
				'id_mantencion_base'			=> $fc->request->id_mantencion_base,
			));
						
			// elimino el valor del form alternativo, para saber como se hizo la busqueda al volver a esta pantalla
			
			HTTP_session::set('search_keyword_proveedor', null);
			
			$parameters['id_mantencion_base'] = $id_mantencion_base;
			
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
			
			$search_result = Proveedor::seek($db, $parameters, 'p.fecha_modificacion', 'ASC', 0, 10000);
			
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
			
			$fv[]="id_mantencion_base";

			$this->initFormVars($fv);
			
			
		}
		else if (isset($fc->request->search)) {
			// submit desde AgregaProveedor o EditaProveedor (volver al listado)
			
			$exito = null;
			
			// recupero el valor de busqueda
			
			$search_keyword = HTTP_session::get('search_keyword_proveedor');
			
			// elimino el valor del form alternativo, para saber como se hizo la busqueda al volver a esta pantalla
			
			HTTP_session::set('search_keyword_proveedor_alt', null);
			
			$search_result = Proveedor::seekSpecial($db, $search_keyword);
			
			// var_dump($search_result);
			
			$this->addVar('search_result', $search_result);
			
			$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
			
			$this->addVar('row_number', $row_number);
			
			$exito = true;
			
			$this->addVar('exito', $exito);
			
			// para escribir en textbox el valor de la busqueda
			
			$this->addVar('search_keyword_proveedor', $search_keyword);
								
		}
		else if (isset($fc->request->search_alt)) {
			// submit desde AgregaProveedor o EditaProveedor (volver al listado)
			
			$this->addVar('dias', $dias);
			$exito = null;
			$activo = null;
			$dias = null;
			$auto = null;
			$km = null;
			
			$dias = $fc->request->dias;
			
			$parameters = array();
			
			// recupero el valor de busqueda
			
			$search_keyword = HTTP_session::get('search_keyword_proveedor_alt');
			
			// elimino el valor del form alternativo, para saber como se hizo la busqueda al volver a esta pantalla
			
			HTTP_session::set('search_keyword_proveedor', null);
			
			if (isset($search_keyword['activo']) && isset($search_keyword['dias'])) {
				$activo = $search_keyword['activo'];
				$dias = $search_keyword['dias'];
				$parameters['activo'] = $dias;
				$this->addVar('activo', $activo);
				$this->addVar('dias', $dias);
			}
			
			$auto = $search_keyword['auto'];
			
			if (isset($auto)) {
				$auto = $search_keyword['auto'];
				$parameters['auto'] = '';
				$this->addVar('auto', $auto);
			}
			
			$km = $search_keyword['km'];
			
			if (isset($km)) {
				$km = $search_keyword['km'];
				$parameters['km'] = '';
				$this->addVar('km', $km);
			}
			
			$parameters['no borrado'] = null;
			
			$parameters['identificado'] = null;
			
			$search_result = Proveedor::seek($db, $parameters, 'p.fecha_modificacion', 'ASC', 0, 10000);
			
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
						
		}
		
		$this->processSuccess();
	}
}
?>