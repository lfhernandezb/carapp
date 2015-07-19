<?php

include_once('GenericCommand.php');
include_once('../classes/Proveedor.php');
include_once('../classes/ProveedorMantencionBase.php');
include_once('../classes/MantencionBase.php');

class EditaProveedor extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		$id = $_GET['id'];
		$fid = $fc->request->id;
		
		// recuerdo la clave de busqueda por si el usuario quisiera 'volver al listado'
		$this->addVar('search_keyword_proveedor', HTTP_Session::get('search_keyword_proveedor', ''));
		
		$m = "&nbsp;";

		$this->addVar("message", $m);

		$usuarioPuedeAgregar = false;
		$usuarioPuedeUtilizar = false;
		
		$proveedor = null;
		
		$user_help_desk = 
			"Edita los datos de un proveedor.<br>Accesos:<br><br>" .
			"Agrega Proveedor: Permite agregar proveedores en forma manual o masiva";
		
		$this->addVar('user_help_desk', $user_help_desk);
		
		if (isset($id)) {
			// llamado desde home
			$proveedor = Proveedor::getByID($db, $id);
			// cargo en los controles los parametros del proveedor elegido
			$this->addVar('nombre', $proveedor->nombre);
			$this->addVar('direccion', $proveedor->direccion);
			$this->addVar('correo', $proveedor->correo);
			$this->addVar('telefono', $proveedor->telefono);
			$this->addVar('latitud', $proveedor->latitud);
			$this->addVar('longitud', $proveedor->longitud);
			$this->addVar('valor_minimo', $proveedor->valor_minimo);
			$this->addVar('valor_maximo', $proveedor->valor_maximo);
			$this->addVar('detalle_html', htmlentities($proveedor->detalle_html, ENT_QUOTES));
			$this->addVar('url', $proveedor->url);
			
			// recuerdo el id para grabar cambios en caso de utilizar repuesto
			$this->addVar('id', $id, null);
			
			// mantencion_base
			$list_pmb = ProveedorMantencionBase::seekSpecial($db, $id);
			
			$this->addVar('list_pmb', $list_pmb);
			
		}
		else if (isset($fid)) {
			// submit, actualiza proveedor... grabamos cambios
			
			$exito = null;
			
			// recuerdo el id para validar si el proveedor actualiza mas de una vez
			$this->addVar('id', $fid, null);
			
			try {
			
				$bInTransaction = false;
				
				$proveedor = Proveedor::getById($db, $fid);
				
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
					
					$proveedor->update($db);
					
					// mantenciones base soportadas por el proveedor
					
					$list_mb = MantencionBase::seek($db, '');
					
					$ar_mantenciones = $fc->request->mantenciones;
					
					ProveedorMantencionBase::deleteByIdProveedor($db, $fid);
					
					foreach ($list_mb as $mb) {
						if (array_key_exists($mb['id'], $ar_mantenciones)) {
							/*
					        $trace = debug_backtrace();
					        trigger_error(
					            "id {$mb['id']} nombre {$mb['nombre']}",
					            E_USER_NOTICE);
					        */
							
							$pmb = new ProveedorMantencionBase();
							
							$pmb->id_proveedor = $fid;
							$pmb->id_mantencion_base = $mb['id'];
							
							$pmb->insert($db);
						}
					}
															
					// para que el estado establecido pueda verse post submit
					// $this->addVar('usuarioPuedeAgregar', $usuarioPuedeAgregar);
								
					//$this->addVar('usuarioPuedeUtilizar', $usuarioPuedeUtilizar);
					
					// commit
					if (!$db->TransactionEnd()) {
						throw new Exception('Error al comitear transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					// estatus exito
					$exito = true;
					
					$status_message = 'Proveedor modificado exitosamente';
					
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
				
				$status_message = 'Proveedor no pudo ser modificado. Raz&oacute;n: ' . $e->getMessage();
			}
			
			$this->addVar("exito", $exito);
			
			$this->addVar("status_message", $status_message);
			
			// mantencion_base
			$list_pmb = ProveedorMantencionBase::seekSpecial($db, $fid);
			
			$this->addVar('list_pmb', $list_pmb);
			
			
			// cargo en los textboxes los mismos valores pre submit
			$fv=array();
			
			// para el correcto despliegue
			$fc->request->detalle_html = htmlentities($fc->request->detalle_html, ENT_QUOTES);
			
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