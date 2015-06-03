<?php

include_once('GenericCommand.php');
include_once('../classes/Campania.php');

class EditaCampania extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		$id = $_GET['id'];
		$fid = $fc->request->id;
		
		// recuerdo la clave de busqueda por si el usuario quisiera 'volver al listado'
		$this->addVar('search_keyword_campania', HTTP_Session::get('search_keyword_campania', ''));
		
		$m = "&nbsp;";

		$this->addVar("message", $m);

		$usuarioPuedeAgregar = false;
		$usuarioPuedeUtilizar = false;
		
		$campania = null;
		
		$user_help_desk = 
			"Edita los datos de una campa&ntilde;a.<br>Accesos:<br><br>" .
			"Agrega campa&ntilde;a: Permite agregar campa&ntilde;as en forma manual o masiva";
		
		$this->addVar('user_help_desk', $user_help_desk);
		
		if (isset($id)) {
			// llamado desde home
			$campania = Campania::getByID($db, $id);
			
			/*
			// valido el JSON de detalle
			$json_detalle = json_decode($campania->detalle, true);
			
			if ($json_detalle != null) {
				var_dump($json_detalle);
			}
			
	        $trace = debug_backtrace();
	        trigger_error(
	            "detalle {$campania->detalle}",
	            E_USER_NOTICE);
			
			mysql_set_charset('latin1');
				
			echo $campania->detalle . '<br>';
			echo mysql_real_escape_string($campania->detalle) . '<br>';
			*/
			// cargo en los controles los parametros del campania elegido
			$this->addVar('descripcion', $campania->descripcion);
			$this->addVar('activa', $campania->activa);
			$this->addVar('condicion', $campania->condicion);
			$this->addVar('detalle', $campania->detalle);
			$this->addVar('fecha_inicio', $campania->fecha_inicio);
			$this->addVar('fecha_fin', $campania->fecha_fin);
			$this->addVar('periodicidad', $campania->periodicidad);
			$this->addVar('numero_impresiones', $campania->numero_impresiones);
			
			// recuerdo el id para grabar cambios en caso de modificar campania
			$this->addVar('id', $id, null);
						
			//var_dump($campania);
			
		}
		else if (isset($fid)) {
			// submit, actualiza campania... grabamos cambios
			
			$exito = null;
			
			// recuerdo el id para validar si el campania actualiza mas de una vez
			$this->addVar('id', $fid, null);
			
			try {
			
				$bInTransaction = false;
				
				$campania = Campania::getById($db, $fid);
				
				$campania->descripcion = $fc->request->descripcion;
				
				//$campania->activa = $fc->request->activa;
				
				$ar_activa = $fc->request->activa;
				
				if (is_array($ar_activa)) {
					if (array_key_exists(0, $ar_activa)) {
						$campania->activa = 1;
					}
				}
				else {
					$campania->activa = 0;
				}
								
				/*
		        $trace = debug_backtrace();
		        trigger_error(
		            "detalle fc {$fc->request->detalle}",
		            E_USER_NOTICE);
				
		        $trace = debug_backtrace();
		        trigger_error(
		            "detalle " . mysql_real_escape_string(html_entity_decode($fc->request->detalle)),
		            E_USER_NOTICE);
		        
				
				mysql_set_charset('latin1');
				
				echo $fc->request->detalle . '<br>';
				echo mysql_real_escape_string($fc->request->detalle) . '<br>';
				*/
				$campania->condicion = $fc->request->condicion;
				$campania->detalle = mysql_real_escape_string(html_entity_decode($fc->request->detalle));
				$campania->fecha_inicio = $fc->request->fecha_inicio;
				$campania->fecha_fin = $fc->request->fecha_fin;
				$campania->periodicidad = $fc->request->periodicidad;
				$campania->numero_impresiones = $fc->request->numero_impresiones;
				
				$status_message = '';
				
				try {
					
					// valido el JSON de detalle
					$json_detalle = json_decode(html_entity_decode($fc->request->detalle), true);
					
					if ($json_detalle == null) {
						throw new Exception('Error, el JSON no es v&aacute;lido');
					}
					
					
					// inicio transaccion
					
					if (!$db->TransactionBegin()) {
						throw new Exception('Error al iniciar transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					$bInTransaction = true;
					
					$campania->update($db);
															
					// para que el estado establecido pueda verse post submit
					// $this->addVar('usuarioPuedeAgregar', $usuarioPuedeAgregar);
								
					//$this->addVar('usuarioPuedeUtilizar', $usuarioPuedeUtilizar);
					
					// commit
					if (!$db->TransactionEnd()) {
						throw new Exception('Error al comitear transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					// estatus exito
					$exito = true;
					
					$status_message = 'Campa&ntilde;a modificado exitosamente';
					
				} catch (Exception $e) {
					// rollback
					if ($bInTransaction) {
						$db->TransactionRollback();
					}
					
					throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
				}
			} catch (Exception $e) {
				// estatus fracaso
				$exito = false;
				
				$status_message = 'Campa&ntilde;a no pudo ser modificado. Raz&oacute;n: ' . $e->getMessage();
			}
				
			$this->addVar("exito", $exito);
			
			$this->addVar("status_message", $status_message);
			
			// cargo en los textboxes los mismos valores pre submit
			$fv=array();
			
			$fv[0]="descripcion";
			$fv[1]="activa";
			$fv[2]="condicion";
			$fv[3]="detalle";
			$fv[4]="fecha_inicio";
			$fv[5]="fecha_fin";
			$fv[6]="periodicidad";
			$fv[7]="numero_impresiones";
						
			$this->initFormVars($fv);
		}
		
		$this->processSuccess();

	}
}
?>