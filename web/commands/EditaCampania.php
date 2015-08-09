<?php

include_once('GenericCommand.php');
include_once('../classes/Campania.php');
include_once('../classes/Util.php');
include_once('../classes/class.datetimecalc.php');

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
			"Edita los datos de una campa&ntilde;a.<br>"; //Accesos:<br><br>" .
			//"Agrega campa&ntilde;a: Permite agregar campa&ntilde;as en forma manual o masiva";
		
		$this->addVar('user_help_desk', $user_help_desk);
		
		if (isset($id)) {
			// llamado desde home
			$campania = Campania::getByID($db, $id);
			
			$reenviar = false;
			
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
			$this->addVar('reenviar', $reenviar);
			$this->addVar('condicion', $campania->condicion);
			// para correcto despliegue, htmlenties
			$this->addVar('detalle', htmlentities($campania->detalle, ENT_QUOTES));
			$this->addVar('fecha_inicio', $campania->fecha_inicio);
			
			// debo calcular la diferencia en dias entre fecha_final y fecha_inicial
			$dtc_i = new Date_Time_Calc($campania->fecha_inicio, 'Y-m-d');
			
			Util::write_to_log("timestamp inicial " . $dtc_i->date_time_stamp);
			
			$dtc_f = new Date_Time_Calc($campania->fecha_fin, 'Y-m-d');
			
			$dias = intval(($dtc_f->date_time_stamp - $dtc_i->date_time_stamp) / ( 60 * 60 * 24 ));
			
			Util::write_to_log("dias " . $dias);
			
			//$this->addVar('fecha_fin', $campania->fecha_fin);
			
			$this->addVar('dias', $dias);
			$this->addVar('periodicidad', $campania->periodicidad);
			$this->addVar('numero_impresiones', $campania->numero_impresiones);
			
			// recuerdo el id para grabar cambios en caso de modificar campania
			$this->addVar('id', $id, null);
						
			//var_dump($campania);
			
		}
		else if (isset($fid)) {
			// submit, actualiza campania... grabamos cambios
			
			Util::write_to_log($fc->request->condicion);
			Util::write_to_log($fc->request->detalle);
			
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
				$campania->condicion = mysql_real_escape_string($fc->request->condicion);
				$campania->detalle = $fc->request->detalle;
				$campania->fecha_inicio = $fc->request->fecha_inicio;
				
				// 2015-06-18 calculamos la fecha final en base a la fecha inicial y los dias ingresados
				$dtc = new Date_Time_Calc($campania->fecha_inicio, 'Y-m-d');
				$dtc->add("d", $fc->request->dias);
				
				Util::write_to_log("fecha_final " . $dtc->date_time);
				
				$campania->fecha_fin = $dtc->date_time;
				$campania->periodicidad = $fc->request->periodicidad;
				$campania->numero_impresiones = $fc->request->numero_impresiones;
				
				$status_message = '';
				
				try {
					
					// valido el JSON de detalle
					$json_detalle = json_decode($fc->request->detalle);
					
					if ($json_detalle == null) {
						throw new Exception('Error, el JSON no es v&aacute;lido');
					}
					
					if (isset($campania->condicion) && strlen($campania->condicion) > 0) {
						// valido la condicion SQL
						$str_sql = "SELECT * FROM usuario WHERE " . $campania->condicion;
						
						$ret = $db->QueryArray($str_sql, MYSQL_ASSOC);
						
						if (!is_array($ret)) {
			
							if ($db->RowCount() != 0) {
								throw new Exception('Error en SQL: ' . $db->Error(), $db->ErrorNumber(), null);
							}
						}
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
					
					$ar_reenviar = $fc->request->reenviar;
					
					if (is_array($ar_reenviar)) {
						if (array_key_exists(0, $ar_reenviar)) {
							// debo borrar campania_usuario
							foreach ($campania->getCampaniaUsuario($db) as $cu) {
								
								$c_u = CampaniaUsuario::getByID($db, $cu['id']);
								
								$c_u->delete($db);
							}
						}
					}					
					
					// commit
					if (!$db->TransactionEnd()) {
						throw new Exception('Error al comitear transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					// estatus exito
					$exito = true;
					
					$status_message = 'Campa&ntilde;a modificada exitosamente';
					
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
				
				$status_message = 'Campa&ntilde;a no pudo ser modificada. Raz&oacute;n: ' . $e->getMessage();
			}
				
			$this->addVar("exito", $exito);
			
			$this->addVar("status_message", $status_message);
			
			// cargo en los textboxes los mismos valores pre submit
			$fv=array();
			
			// para el correcto despliegue
			$fc->request->detalle = htmlentities($fc->request->detalle, ENT_QUOTES);
			
			Util::write_to_log("fc->request->detalle post save " . $fc->request->detalle);
			
			$fv[]="descripcion";
			$fv[]="activa";
			$fv[]="reenviar";
			$fv[]="condicion";
			$fv[]="detalle";
			$fv[]="fecha_inicio";
			$fv[]="dias";
			$fv[]="periodicidad";
			$fv[]="numero_impresiones";
						
			$this->initFormVars($fv);
		}
		
		$this->processSuccess();

	}
}
?>