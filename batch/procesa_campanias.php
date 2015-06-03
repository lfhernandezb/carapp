<?php

include_once('../web/classes/mysql.class.php');
include_once('../web/classes/Campania.php');

	$bCampaniaEjecutada = false;

	$db = new MySQL(false, 'car', 'localhost', 'car', 'car', 'latin1');
	
	if (!$db->Open()) {
		die("No es posible conectarse a la base de datos\n");
	}

	$parametros = array(
		'activo'	=> null,
		'iniciada'	=> null
	);
	
	try {
		
		$campanias = Campania::seek($db, $parametros, null, null, 0, 10000);
		
		if (is_array($campanias)) {
			foreach ($campanias as $campania) {
				// corresponde ejecutar esta campania?
				// el crontab corre al minuto 30
				// $campania['inicio'] tiene fecha/hora con minutos y segundos en cero
				
				var_dump($campania);
				
				$bEjecutar = false;
				
				$str_sql = "SELECT TIMESTAMPDIFF(HOUR, STR_TO_DATE('" . $campania['inicio'] . "', '%Y-%m-%d %H:%i:%s'), CURRENT_TIMESTAMP) AS dif";
				
				$dif = $db->QuerySingleRowArray($str_sql);
				
				var_dump($dif);
				
				if (is_array($dif)) {
					if ($campania['periodicidad_dias'] == null && $dif['dif'] == 0) {
						$bEjecutar = true;
					}
					else if ($campania['periodicidad_dias'] > 0 && $dif['dif'] % 24 == 0) {
						$bEjecutar = true;
					}
				}
				else {
					echo "Error al obtener la diferencia de horas\n";
				}
				
				if ($bEjecutar) {
					echo "Corresponde ejecutar la campania '{$campania['descripcion']}'\n";
					
					$str_sql_c = 
						"  SELECT id_usuario, nombre, correo" .
					    "  FROM usuario" .
					    "  WHERE borrado = b'0'" .
					    "  AND nombre IS NOT NULL" .
					    "  AND correo IS NOT NULL";
					
					if ($campania['por_sql'] == 1) {
						// debo ejecutar una sentencia SELECT * FROM usuario con la clausula WHERE especificada en 'condicion_sql'
						// debo excluir a los que estan en campania_usuario con este id_campania
						
						$str_sql_c .=
						    "  AND id_usuario NOT IN (SELECT id_usuario FROM campania_usuario WHERE id_campania = " . $campania['id'] . ")";
						
						if (isset($campania['condicion_sql']) && strlen($campania['condicion_sql'] > 0)) {
							$str_sql_c .=
						        "  AND " . $campania['condicion_sql'];
						}
					}
					else {
						// debo enviar correo a los que estan en campania_usuario con este id_campania

						$str_sql_c .= 
						    "  AND id_usuario IN (SELECT id_usuario FROM campania_usuario WHERE id_campania = " . $campania['id'] . ")";
					}
					
					$usuarios_c = $db->QueryArray($str_sql_c);
					
					if (is_array($usuarios_c)) {
						foreach ($usuarios_c as $usuario_c) {
							var_dump($usuario_c);
							
							if ($campania['por_sql'] == 1) {
								// inserto en campania_usuario
							}
						}
					}
					
					$bCampaniaEjecutada = true;
				}
			}
		}
		
		if (!$bCampaniaEjecutada) {
			echo "No es el momento de ejecutar ninguna campania\n";
		}
	} catch (Exception $e) {
		echo "Excepcion: " . $e->getMessage() . "\n";
	}
	
	
	$db->Close();
?>