<?php

include_once('GenericCommand.php');
include_once('../classes/Campania.php');

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
			"Crea una campa&ntilde;a. Los campos marcados con * son obligatorios.<br>Accesos:<br><br>" .
			"Agrega Campa&ntilde;a: Permite ingresar campa&ntilde;as en forma manual o masiva";
		
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
			$this->addVar('fecha_fin', $v);
			$this->addVar('periodicidad', $v);
			$this->addVar('numero_impresiones', $v);

			$usuarioPuedeAgregar = false;
			
		}
		else {
			// submit, agrega campania... grabamos cambios
			
			$exito = null;
			
			$usuarioPuedeAgregar = false;
			
			$ar_accesos = $fc->request->accesos;
			
			if (is_array($ar_accesos)) {
				if (array_key_exists(1, $ar_accesos)) {
					// solicitado acceso a agregar
					
					$usuarioPuedeAgregar = true;
				}
			}
			
			
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
					
					$campania->insert($db);
										
					if ($usuarioPuedeAgregar) {
						$campania->otorgaAcceso($db, 'agregar');
					}

					if ($usuarioPuedeUtilizar) {
						$campania->otorgaAcceso($db, 'utilizar');
					}
					
					// commit
					if (!$db->TransactionEnd()) {
						throw new Exception('Error al comitear transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					// estatus exito
					$exito = true;
					
					$status_message = 'Campa&ntilde;a agregado exitosamente';
					
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
				
				$status_message = 'Campa&ntilde;a no pudo ser agregado. Raz&oacute;n: ' . $e->getMessage();
			}
				
			$this->addVar("exito", $exito);
			
			$this->addVar("status_message", $status_message);

			// para que el estado establecido pueda verse post submit
			$this->addVar('usuarioPuedeAgregar', $usuarioPuedeAgregar);
						
			$this->addVar('usuarioPuedeUtilizar', $usuarioPuedeUtilizar);
			
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