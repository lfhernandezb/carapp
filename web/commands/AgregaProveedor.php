<?php

include_once('GenericCommand.php');
include_once('../classes/Proveedor.php');

class AgregaProveedor extends GenericCommand {
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		// recuerdo la clave de busqueda por si el usuario quisiera 'volver al listado'
		$this->addVar('search_keyword_proveedor', HTTP_Session::get('search_keyword_proveedor', ''));
		
		$m = "&nbsp;";

		$this->addVar("message", $m);

		$usuarioPuedeAgregar = null;
		
		$usuario = null;
		
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

			$usuarioPuedeAgregar = false;
			
		}
		else {
			// submit, agrega proveedor... grabamos cambios
			
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
			
				$acceso_utilizar = Acceso::getByDescripcion($db, 'utilizar');
				
				$acceso_agregar = Acceso::getByDescripcion($db, 'agregar');
				
				if (!isset($acceso_agregar)) {
					throw new Exception('Error al obtener acceso de agregar: registro no existe', null);
				}
				
				$bInTransaction = false;
				
				$proveedor = new Proveedor();
				
				$proveedor->nombre = $fc->request->nombre;
				$proveedor->direccion = $fc->request->direccion;
				$proveedor->correo = $fc->request->correo;
				$proveedor->telefono = $fc->request->telefono;
				$proveedor->latitud = $fc->request->latitud;
				$proveedor->longitud = $fc->request->longitud;
				$proveedor->valor_minimo = $fc->request->valor_minimo;
				$proveedor->valor_maximo = $fc->request->valor_maximo;
				$proveedor->detalle_html = $fc->request->detalle_html;
				$proveedor->url = $fc->request->url;
				
				$status_message = '';
				
				try {
					
					// inicio transaccion
					
					if (!$db->TransactionBegin()) {
						throw new Exception('Error al iniciar transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					$bInTransaction = true;
					
					$proveedor->insert($db);
										
					if ($usuarioPuedeAgregar) {
						$proveedor->otorgaAcceso($db, 'agregar');
					}

					if ($usuarioPuedeUtilizar) {
						$proveedor->otorgaAcceso($db, 'utilizar');
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

			// para que el estado establecido pueda verse post submit
			$this->addVar('usuarioPuedeAgregar', $usuarioPuedeAgregar);
						
			$this->addVar('usuarioPuedeUtilizar', $usuarioPuedeUtilizar);
			
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
			
			$this->initFormVars($fv);
		}
				
		$this->processSuccess();

	}
}
?>