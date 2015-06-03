<?php

include_once('GenericCommand.php');
include_once('../classes/Parametro.php');

class EditaParametro extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		$id = $_GET['id'];
		$fid = $fc->request->id;
		
		// recuerdo la clave de busqueda por si el usuario quisiera 'volver al listado'
		$this->addVar('search_keyword_parametro', HTTP_Session::get('search_keyword_parametro', ''));
		
		$m = "&nbsp;";

		$this->addVar("message", $m);

		$usuarioPuedeAgregar = false;
		$usuarioPuedeUtilizar = false;
		
		$parametro = null;
		
		$user_help_desk = 
			"Edita los datos de un par&aacute;metro.<br>Accesos:<br><br>" .
			"Agrega Par&aacute;metro: Permite agregar par&aacute;metroes en forma manual o masiva";
		
		$this->addVar('user_help_desk', $user_help_desk);
		
		if (isset($id)) {
			// llamado desde home
			$parametro = Parametro::getByID($db, $id);
			// cargo en los controles los parametros del parametro elegido
			$this->addVar('llave', $parametro->llave);
			$this->addVar('valor', $parametro->valor);
			
			// recuerdo el id para grabar cambios en caso de utilizar repuesto
			$this->addVar('id', $id, null);
			
		}
		else if (isset($fid)) {
			// submit, actualiza parametro... grabamos cambios
			
			$exito = null;
			
			// recuerdo el id para validar si el parametro actualiza mas de una vez
			$this->addVar('id', $fid, null);
			
			try {
			
				$bInTransaction = false;
				
				$parametro = Parametro::getById($db, $fid);
				
				$parametro->llave = $fc->request->llave;
				$parametro->valor = mysql_real_escape_string(html_entity_decode($fc->request->valor));
				
				$status_message = '';
				
				try {
					
					// inicio transaccion
					
					if (!$db->TransactionBegin()) {
						throw new Exception('Error al iniciar transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					$bInTransaction = true;
					
					$parametro->update($db);
															
					// para que el estado establecido pueda verse post submit
					// $this->addVar('usuarioPuedeAgregar', $usuarioPuedeAgregar);
								
					//$this->addVar('usuarioPuedeUtilizar', $usuarioPuedeUtilizar);
					
					// commit
					if (!$db->TransactionEnd()) {
						throw new Exception('Error al comitear transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					// estatus exito
					$exito = true;
					
					$status_message = 'Par&aacute;metro modificado exitosamente';
					
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
				
				$status_message = 'Par&aacute;metro no pudo ser modificado. Raz&oacute;n: ' . $e->getMessage();
			}
				
			$this->addVar("exito", $exito);
			
			$this->addVar("status_message", $status_message);
			
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