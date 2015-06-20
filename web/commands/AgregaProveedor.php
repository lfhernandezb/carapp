<?php

include_once('GenericCommand.php');
include_once('../classes/Proveedor.php');
include_once('../classes/ProveedorMantencionBase.php');
include_once('../classes/MantencionBase.php');

class AgregaProveedor extends GenericCommand {
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		// recuerdo la clave de busqueda por si el usuario quisiera 'volver al listado'
		$this->addVar('search_keyword_proveedor', HTTP_Session::get('search_keyword_proveedor', ''));
		
		$m = "&nbsp;";

		$this->addVar("message", $m);

		// ayuda en pantalla
		$user_help_desk = 
			"Crea un proveedor. Los campos marcados con * son obligatorios.<br>Accesos:<br><br>" .
			"Agrega Proveedor: Permite ingresar proveedores en forma manual o masiva";
		
		$this->addVar('user_help_desk', $user_help_desk);
		
		if (!isset($fc->request->direccion)) {
			// llamado desde 'GestionaProveedores'
			
			// muestro controles limpios
			$v = '';

			$this->addVar('nombre', $v);
			$this->addVar('direccion', $v);
			$this->addVar('correo', $v);
			$this->addVar('telefono', $v);
			$this->addVar('latitud', $v);
			$this->addVar('longitud', $v);
			$this->addVar('valor_minimo', $v);
			$this->addVar('valor_maximo', $v);
			$this->addVar('detalle_html', $v);
			$this->addVar('url', $v);

			// mantencion_base
			$list_pmb = ProveedorMantencionBase::seekSpecial($db, 0);
			
			$this->addVar('list_pmb', $list_pmb);
		
		}
		else {
			// submit, agrega proveedor... grabamos cambios
			
			$exito = null;
			
			try {
			
				$bInTransaction = false;
				
				$proveedor = new Proveedor();
				
				$proveedor->nombre = $fc->request->nombre;
				$proveedor->direccion = $fc->request->direccion;
				$proveedor->correo = $fc->request->correo;
				$proveedor->telefono = $fc->request->telefono;
				$proveedor->latitud = is_numeric($fc->request->latitud) ? $fc->request->latitud : null;
				$proveedor->longitud = is_numeric($fc->request->longitud) ? $fc->request->longitud : null;
				$proveedor->valor_minimo = is_numeric($fc->request->valor_minimo) ? $fc->request->valor_minimo : null;
				$proveedor->valor_maximo = is_numeric($fc->request->valor_maximo) ? $fc->request->valor_maximo : null;
				$proveedor->detalle_html = utf8_decode($fc->request->detalle_html);
				$proveedor->url = $fc->request->url;
				
				$status_message = '';
				
				try {
					
					// inicio transaccion
					
					if (!$db->TransactionBegin()) {
						throw new Exception('Error al iniciar transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					$bInTransaction = true;
					
					$proveedor->insert($db);
										
					// mantenciones base soportadas por el proveedor
					
					$list_mb = MantencionBase::seek($db, '');
					
					$ar_mantenciones = $fc->request->mantenciones;
					
					foreach ($list_mb as $mb) {
						if (array_key_exists($mb['id'], $ar_mantenciones)) {
							/*
					        $trace = debug_backtrace();
					        trigger_error(
					            "id {$mb['id']} nombre {$mb['nombre']}",
					            E_USER_NOTICE);
					        */
							
							$pmb = new ProveedorMantencionBase();
							
							$pmb->id_proveedor = $proveedor->id;
							$pmb->id_mantencion_base = $mb['id'];
							
							$pmb->insert($db);
						}
					}
										
					// commit
					if (!$db->TransactionEnd()) {
						throw new Exception('Error al comitear transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					// estatus exito
					$exito = true;
					
					$status_message = 'Proveedor agregado exitosamente';
					
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
				
				$status_message = 'Proveedor no pudo ser agregado. Raz&oacute;n: ' . $e->getMessage();
			}
				
			$this->addVar("exito", $exito);
			
			$this->addVar("status_message", $status_message);

			// mantencion_base
			$list_pmb = ProveedorMantencionBase::seekSpecial($db, $proveedor->id);
			
			$this->addVar('list_pmb', $list_pmb);
						
			// cargo en los textboxes los mismos valores pre submit
			$fv=array();
			
			$fv[0]="nombre";
			$fv[1]="direccion";
			$fv[2]="correo";
			$fv[3]="telefono";
			$fv[4]="latitud";
			$fv[5]="longitud";
			$fv[6]="valor_minimo";
			$fv[7]="valor_maximo";
			$fv[8]="detalle_html";
			$fv[9]="url";
			$fv[10]="mantenciones";
			
			$this->initFormVars($fv);
		}
				
		$this->processSuccess();

	}
}
?>