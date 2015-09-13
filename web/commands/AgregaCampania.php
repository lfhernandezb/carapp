<?php

include_once('GenericCommand.php');
include_once('../classes/Campania.php');
include_once('../classes/Util.php');
include_once('../classes/class.datetimecalc.php');

class AgregaCampania extends GenericCommand {
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		// recuerdo la clave de busqueda por si el usuario quisiera 'volver al listado'
		$this->addVar('search_keyword_campania', HTTP_Session::get('search_keyword_campania', ''));
		
		$m = "&nbsp;";

		$this->addVar("message", $m);

		$usuarioPuedeAgregar = null;
		
		$usuario = null;
		
		// ayuda en pantalla
		$user_help_desk = 
			"Crea una campa&ntilde;a. Los campos marcados con * son obligatorios.<br>"; //Accesos:<br><br>" .
			//"Agrega Campa&ntilde;a: Permite ingresar campa&ntilde;as en forma manual o masiva";
		
		$this->addVar('user_help_desk', $user_help_desk);
		
		if (!isset($fc->request->descripcion)) {
			// llamado desde 'GestionaCampanias'
			
			// muestro controles limpios
			$v = '';

			$this->addVar('descripcion', $v);
			$this->addVar('activa', $v);
			$this->addVar('condicion', $v);
			$this->addVar('detalle', $v);
			$this->addVar('fecha_inicio', $v);
			$this->addVar('dias', $v);
			$this->addVar('periodicidad', $v);
			$this->addVar('numero_impresiones', $v);

			$usuarioPuedeAgregar = false;
			
		}
		else {
			// submit, agrega campania... grabamos cambios
			
			$exito = null;
			
			try {
			
				$bInTransaction = false;
				
				$campania = new Campania();
				
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
				$campania->manual = 1; // siempre en campanias agregadas por el mantenedor
								
				$status_message = '';
				
				try {
					
					// valido el JSON de detalle
					$json_detalle = json_decode($fc->request->detalle);
					
					if ($json_detalle == null) {
						throw new Exception('Error, el JSON no es v&aacute;lido');
					}
					
					// valido la condicion SQL
					$str_sql = 
						"  SELECT DISTINCT(u.id_usuario)" .
						"  FROM usuario u" .
						"  LEFT JOIN vehiculo v ON v.id_usuario = u.id_usuario" .
						"  LEFT JOIN usuario_info ui ON ui.id_usuario = u.id_usuario" .
						"  LEFT JOIN region r ON r.region = ui.state" .
						"  WHERE u.id_usuario NOT IN (SELECT cu.id_usuario FROM campania_usuario cu WHERE cu.id_campania = $fid)" .
						"  AND ({$campania->condicion})";
					
					$ret = $db->QueryArray($str_sql, MYSQL_ASSOC);
					
					Util::write_to_log("is_array : " . is_array($ret));
					
					if ($db->Error()) {
		
						//if ($db->RowCount() != 0) {
							throw new Exception('Error en SQL: ' . $db->Error(), $db->ErrorNumber(), null);
						//}
					}
										
					// inicio transaccion
					
					if (!$db->TransactionBegin()) {
						throw new Exception('Error al iniciar transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					$bInTransaction = true;
					
					$campania->insert($db);
										
					// commit
					if (!$db->TransactionEnd()) {
						throw new Exception('Error al comitear transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					// estatus exito
					$exito = true;
					
					$status_message = 'Campa&ntilde;a agregada exitosamente';
					
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
				
				$status_message = 'Campa&ntilde;a no pudo ser agregada. Raz&oacute;n: ' . $e->getMessage();
			}
				
			$this->addVar("exito", $exito);
			
			$this->addVar("status_message", $status_message);

			// para que el estado establecido pueda verse post submit
			$this->addVar('usuarioPuedeAgregar', $usuarioPuedeAgregar);
						
			$this->addVar('usuarioPuedeUtilizar', $usuarioPuedeUtilizar);
			
			// cargo en los textboxes los mismos valores pre submit
			$fv=array();
			
			// para el correcto despliegue
			$fc->request->detalle = htmlentities($fc->request->detalle, ENT_QUOTES);
			
			$fv[0]="descripcion";
			$fv[1]="activa";
			$fv[2]="condicion";
			$fv[3]="detalle";
			$fv[4]="fecha_inicio";
			$fv[5]="dias";
			$fv[6]="periodicidad";
			$fv[7]="numero_impresiones";
			
			$this->initFormVars($fv);
		}
				
		$this->processSuccess();

	}
}
?>