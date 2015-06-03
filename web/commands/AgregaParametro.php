<?php

include_once('GenericCommand.php');
include_once('../classes/Parametro.php');

class AgregaParametro extends GenericCommand {
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		// recuerdo la clave de busqueda por si el usuario quisiera 'volver al listado'
		$this->addVar('search_keyword_parametro', HTTP_Session::get('search_keyword_parametro', ''));
		
		$m = "&nbsp;";

		$this->addVar("message", $m);

		$usuarioPuedeAgregar = null;
		
		$usuario = null;
		
		// ayuda en pantalla
		$user_help_desk = 
			"Crea un par&aacute;metro. Los campos marcados con * son obligatorios.<br>Accesos:<br><br>" .
			"Agrega Par&aacute;metro: Permite ingresar par&aacute;metros en forma manual o masiva";
		
		$this->addVar('user_help_desk', $user_help_desk);
		
		if (!isset($fc->request->llave)) {
			// llamado desde 'GestionaParametros'
			
			// muestro controles limpios
			$v = '';

			$this->addVar('llave', $v);
			$this->addVar('valor', $v);

			$usuarioPuedeAgregar = false;
			
		}
		else {
			// submit, agrega parametro... grabamos cambios
			
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
				
				$parametro = new Parametro();
				
				$parametro->llave = $fc->request->llave;
				$parametro->valor = mysql_real_escape_string(html_entity_decode($fc->request->valor));
				
				$status_message = '';
				
				try {
					
					// inicio transaccion
					
					if (!$db->TransactionBegin()) {
						throw new Exception('Error al iniciar transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					$bInTransaction = true;
					
					$parametro->insert($db);
										
					if ($usuarioPuedeAgregar) {
						$parametro->otorgaAcceso($db, 'agregar');
					}

					if ($usuarioPuedeUtilizar) {
						$parametro->otorgaAcceso($db, 'utilizar');
					}
					
					// commit
					if (!$db->TransactionEnd()) {
						throw new Exception('Error al comitear transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					// estatus exito
					$exito = true;
					
					$status_message = 'Par&aacute;metro agregado exitosamente';
					
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
				
				$status_message = 'Par&aacute;metro no pudo ser agregado. Raz&oacute;n: ' . $e->getMessage();
			}
				
			$this->addVar("exito", $exito);
			
			$this->addVar("status_message", $status_message);

			// para que el estado establecido pueda verse post submit
			$this->addVar('usuarioPuedeAgregar', $usuarioPuedeAgregar);
						
			$this->addVar('usuarioPuedeUtilizar', $usuarioPuedeUtilizar);
			
			// cargo en los textboxes los mismos valores pre submit
			$fv=array();
			
			$fv[0]="llave";
			$fv[1]="valor";
			
			$this->initFormVars($fv);
		}
				
		$this->processSuccess();

	}
}
?>