<?php

include_once('mysql.class.php');

class Usuario
{
	private $_id;
	private $_id_comuna;
	private $_nombre;
	private $_correo;
	private $_fecha_nacimiento;
	private $_hombre;
	private $_telefono;
	private $_fecha_vencimiento_licencia;
	private $_fecha_modificacion;
	private $_borrado;
	
	private static $_str_sql = "
  SELECT u.id_usuario AS id, u.id_comuna, u.nombre, u.correo, DATE_FORMAT(u.fecha_nacimiento, '%Y-%m-%d') AS fecha_nacimiento,
  0+u.hombre AS hombre, u.telefono, DATE_FORMAT(u.fecha_vencimiento_licencia, '%Y-%m-%d') AS fecha_vencimiento_licencia,
  DATE_FORMAT(u.fecha_modificacion, '%Y-%m-%d %H:%i:%s') AS fecha_modificacion, 0+u.borrado AS borrado,
  ui.road, ui.suburb, ui.city, ui.state, ui.country
  FROM usuario u
  LEFT JOIN usuario_info ui ON ui.id_usuario = u.id_usuario";
		
	public function __construct() {
		
	}
	
    public function __set($name, $value)
    {
        //echo "Setting '$name' to '$value'\n";
        switch ($name) {
        	case "id" :
        		$this->_id = $value;
        		break;
        	case "id_comuna" :
        		$this->_id_comuna = $value;
        		break;
        	case "nombre" :
        		$this->_nombre = $value;
        		break;
        	case "correo" :
        		$this->_correo = $value;
        		break;
        	case "fecha_nacimiento" :
        		$this->_fecha_nacimiento = $value;
        		break;
        	case "hombre" :
        		$this->_hombre = $value;
        		break;
        	case "telefono" :
        		$this->_telefono = $value;
        		break;
        	case "fecha_vencimiento_licencia" :
        		$this->_fecha_vencimiento_licencia = $value;
        		break;
        	case "fecha_modificacion" :
        		$this->_fecha_modificacion = $value;
        		break;
        	case "borrado" :
        		$this->_borrado = $value;
        		break;
        	default:
		        $trace = debug_backtrace();
		        trigger_error(
		            'Undefined property via __set(): ' . $name .
		            ' in ' . $trace[0]['file'] .
		            ' on line ' . $trace[0]['line'],
		            E_USER_NOTICE);
        }
    }

    public function __get($name)
    {
        //echo "Getting '$name'\n";
        switch ($name) {
        	case "id" :
        		return $this->_id;
        	case "id_comuna" :
        		return $this->_id_comuna;
        	case "nombre" :
        		return $this->_nombre;
        	case "correo" :
        		return $this->_correo;
        	case "fecha_nacimiento" :
        		return $this->_fecha_nacimiento;
        	case "hombre" :
        		return $this->_hombre;
        	case "telefono" :
        		return $this->_telefono;
        	case "fecha_vencimiento_licencia" :
        		return $this->_fecha_vencimiento_licencia;
        	case "fecha_modificacion" :
        		return $this->_fecha_modificacion;
        	case "borrado" :
        		return $this->_borrado;
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }
    
	public static function fromArray($p_ar) {
		$ret = new Usuario();
		
		$ret->_id = $p_ar[0]['id_usuario'];
		$ret->_id_comuna = $p_ar[0]['id_comuna'];
		$ret->_nombre = $p_ar[0]['nombre'];
		$ret->_correo = $p_ar[0]['correo'];
		$ret->_fecha_nacimiento = $p_ar[0]['fecha_nacimiento'];
		$ret->_hombre = $p_ar[0]['hombre'];
		$ret->_telefono = $p_ar[0]['telefono'];
		$ret->_fecha_vencimiento_licencia = $p_ar[0]['fecha_vencimiento_licencia'];
		$ret->_fecha_modificacion = $p_ar[0]['fecha_modificacion'];
		$ret->_borrado = $p_ar[0]['borrado'];
				
		return $ret;
	}
    
	
	/*
	public static function seek($p_param) {
		global $fc;
		
		$str_sql =
			"  SELECT r.id_repuesto as id, p.descripcion as plataforma, f.descripcion as fabricante, m.descripcion as modelo, re.descripcion as radio_estacion, c.descripcion as ciudad, rg.descripcion as region" .
		 	"  FROM repuesto r" .
			"  JOIN plataforma p ON p.id_plataforma = r.id_plataforma_FK" .
			"  JOIN radio_estacion re ON re.id_radio_estacion = r.id_radio_estacion_FK" .
		    "  JOIN ciudad c ON c.id_ciudad = re.id_ciudad_FK" .
		    "  JOIN region rg ON rg.id_region = c.id_region_FK" .
			"  JOIN modelo m ON m.id_modelo = r.id_modelo_FK" .
			"  JOIN fabricante f ON f.id_fabricante = m.id_fabricante_FK" .
			"  WHERE f.descripcion LIKE '%$p_param%'" .
			"  OR m.descripcion LIKE '%$p_param%'" .
			"  OR r.descripcion LIKE '%$p_param%'" .
			"  OR p.descripcion LIKE '%$p_param%'" .
			"  AND r.ubicacion IS NULL";
		
		// echo $str_sql . '<br>';
		
		return $fc->getLink()->QueryArray($str_sql, MYSQL_ASSOC);
	}
	*/
	public static function getByUsername($p_db, $p_username) {
		$ret = null;
		
		$str_sql = self::$_str_sql .
			"  WHERE u.nombre_usuario = '$p_username'";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = self::fromArray($ar);
 		}
		else if ($p_db->RowCount() != 0) {
			throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
 		 		
		return $ret;
		
	}
	
	public static function getByhombre($p_db, $p_hombre) {
		$ret = null;
		
		$str_sql = self::$_str_sql .
			"  WHERE u.hombre = '$p_hombre'";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = self::fromArray($ar);
 		}
		else if ($p_db->RowCount() != 0) {
			throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
 		 		
		return $ret;
		
	}
	
	public static function getByID($p_db, $p_id) {
		$ret = null;
		
		$str_sql = self::$_str_sql .
			"  WHERE u.id_usuario = '$p_id'";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = self::fromArray($ar);
 		}
		else if ($p_db->RowCount() != 0) {
			throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
 		 		
		return $ret;
		
	}
	/*
	public function tieneAcceso($p_db, $p_acceso) {
		$ret = false;
		
		// usuario 'admin' tienen todos los privilegios
		if ($this->_nombre_usuario == 'admin') {
			$ret = true;
		}
		else {
			$str_sql =
				"  SELECT ua.*" .
			 	"  FROM usuario_acceso ua" .
				"  JOIN usuario u ON u.id_usuario = ua.id_usuario_FK" .
				"  JOIN acceso a ON a.id_acceso = ua.id_acceso_FK" .
				"  WHERE a.descripcion = '$p_acceso'" .
				"  AND u.id_usuario = {$this->_id}";
			
			//echo '<br>' . $str_sql . '<br>';
			
			if ($p_db->Query($str_sql) !== false) {
				if ($p_db->RowCount() != 0) {
					$ret = true;	
				}
	 		}
			else {
				throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
			}
		}
		
		return $ret;
		
	}
	
	public function revocaAcceso($p_db, $p_acceso) {
		$ret = false;
		
		// al usuario 'admin' no se le pueden quitar privilegios
		if ($this->_nombre_usuario != 'admin') {
			
			if ($this->tieneAcceso($p_db, $p_acceso)) {
				
				// debo revocar el acceso
				$acceso = Acceso::getByDescripcion($p_db, $p_acceso);
				
				$id_acceso = $acceso->id;
				
				$str_sql =
					"  DELETE" .
				 	"  FROM usuario_acceso" .
					"  WHERE id_usuario_FK = {$this->_id}" .
					"  AND id_acceso_FK = $id_acceso";
				
				//echo '<br>' . $str_sql . '<br>';
				
				if ($p_db->Query($str_sql) === false) {
					throw new Exception('Error al revocar acceso: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
				}
				
				$ret = true;
			}
		}
		
		return $ret;
	}
	
	public function otorgaAcceso($p_db, $p_acceso) {
		$ret = false;
		
		// al usuario 'admin' no se le pueden otorgar privilegios, por definicion los tiene todos
		if ($this->_nombre_usuario != 'admin') {
			
			if (!$this->tieneAcceso($p_db, $p_acceso)) {
				
				// debo otorgar el acceso
				$acceso = Acceso::getByDescripcion($p_db, $p_acceso);
				
				$id_acceso = $acceso->id;
				
				$str_sql =
					"  INSERT" .
				 	"  INTO usuario_acceso" .
					"  (" .
					"  id_usuario_FK," .
					"  id_acceso_FK" .
					"  )" .
					"  VALUES" .
					"  (" .
					"  {$this->_id}," .
					"  $id_acceso" .
					"  )";
				
				//echo '<br>' . $str_sql . '<br>';
				
				if ($p_db->Query($str_sql) === false) {
					throw new Exception('Error al otorgar acceso: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
				}
				
				$ret = true;
			}
		}
		
		return $ret;
	}
	*/
	public static function seekSpecial($p_db, $p_param, $p_order = null, $p_direction = null, $p_offset = null, $p_limit = null, $p_get_total = false) {
		
		$result         = new stdClass();
		
		$str_sql = self::$_str_sql .
		    "  WHERE";
		
		if (isset($p_param) && $p_param != '') {
			$str_sql .=
			"  (nombre LIKE '%$p_param%'" .
			"  OR correo LIKE '%$p_param%')" .
			"  AND";
			
		}
		
		$str_sql .=
			"  u.borrado = b'0'";
		
		if ($p_get_total) {
		
			$rs = $p_db->Query($str_sql);
			
			$num_rows = mysql_num_rows($rs);
			
	    	$result->total  = $num_rows;
		}
				
        if (isset($p_order) && isset($p_direction)) {
        	$str_sql .= " ORDER BY $p_order $p_direction";
        }
        
        if (isset($p_offset) && isset($p_limit)) {
        	$str_sql .= "  LIMIT $p_offset, $p_limit";
        }
		
        //echo '<br>' . $str_sql . '<br>';
	
		$data = $p_db->QueryArray($str_sql, MYSQL_ASSOC);
		
		if (!is_array($data)) {
			$data = null;

			if ($p_db->RowCount() != 0) {
				throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
			}
		}
				
	    $result->data   = $data;
	 
	    return $result;
		
	}
	
    public static function seek($p_db, $p_parameters, $p_order, $p_direction, $p_offset, $p_limit, $p_get_total = false) {
		
		$result         = new stdClass();
    	$array_clauses  = array();
		
		$str_sql = self::$_str_sql;
							
        foreach($p_parameters as $key => $value) {
    		if ($key == 'id') {
                $array_clauses[] = "u.id_usuario = $value";
            }
            else if ($key == 'activo') {
                $array_clauses[] = "u.id_usuario IN (SELECT DISTINCT(id_usuario) FROM log WHERE fecha_modificacion > DATE_SUB(NOW(), INTERVAL " . $value . " DAY))";
            }
            else if ($key == 'inactivo') {
                $array_clauses[] = "u.id_usuario NOT IN (SELECT DISTINCT(id_usuario) FROM log WHERE fecha_modificacion > DATE_SUB(NOW(), INTERVAL " . $value . " DAY))";
            }
            else if ($key == 'auto') {
                $array_clauses[] = "u.id_usuario IN (SELECT DISTINCT(id_usuario) FROM vehiculo)";
            }
            else if ($key == 'km') {
            	$array_clauses[] = "u.id_usuario IN (SELECT DISTINCT(id_usuario) FROM vehiculo WHERE km > 0)";
            }
            else if ($key == 'identificado') {
                $array_clauses[] = "u.nombre IS NOT NULL AND u.correo IS NOT NULL";
            }
            else if ($key == 'borrado') {
                $array_clauses[] = "u.borrado = b'1'";
            }
            else if ($key == 'no borrado') {
                $array_clauses[] = "u.borrado = b'0'";
            }
            else {
            	throw new Exception('Parametro no soportado: ' . $key, null, null);
            }
        }
        
        $bFirstTime = false;
        foreach($array_clauses as $clause) {
            if (!$bFirstTime) {
                 $bFirstTime = true;
                 $str_sql .= ' WHERE ';
            }
            else {
                 $str_sql .= ' AND ';
            }
            $str_sql .= $clause;
        }
		
		if ($p_get_total) {
		
			$rs = $p_db->Query($str_sql);
			
			$num_rows = mysql_num_rows($rs);
			
	    	$result->total  = $num_rows;
		}
                
		if (isset($p_order) && isset($p_direction)) {
        	$str_sql .= " ORDER BY $p_order $p_direction";
        }
        
        if (isset($p_offset) && isset($p_limit)) {
        	$str_sql .= "  LIMIT $p_offset, $p_limit";
        }
		
        //echo '<br>' . $str_sql . '<br>';
	
		$data = $p_db->QueryArray($str_sql, MYSQL_ASSOC);
		
		if (!is_array($data)) {
			$data = null;

			if ($p_db->RowCount() != 0) {
				throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
			}
		}
		
	    $result->data   = $data;
	 
	    return $result;
	}
	
	public function update($p_db) {

		$str_sql =
			"  UPDATE usuario" .
			"  SET id_comuna = " . (isset($this->_id_comuna) ? "'{$this->_id_comuna}'" : 'null') . ',' .
			"  nombre = " . (isset($this->_nombre) ? "'{$this->_nombre}'" : 'null') . ',' .
			"  correo = " . (isset($this->_correo) ? "'{$this->_correo}'" : 'null') . ',' .
			"  fecha_nacimiento = " . (isset($this->_fecha_nacimiento) ? "'{$this->_fecha_nacimiento}'" : 'null') . ',' .
			"  hombre = " . (isset($this->_hombre) ? "'{$this->_hombre}'" : 'null') . ',' .
			"  telefono = " . (isset($this->_telefono) ? "'{$this->_telefono}'" : 'null') . ',' .
			"  fecha_vencimiento_licencia = " . (isset($this->_fecha_vencimiento_licencia) ? "'{$this->_fecha_vencimiento_licencia}'" : 'null') . ',' .
			"  fecha_modificacion = " . (isset($this->_fecha_modificacion) ? "b'{$this->_fecha_modificacion}'" : 'null') . ',' .
			"  borrado = " . (isset($this->_borrado) ? "b'{$this->_borrado}'" : 'null') .
			"  WHERE id_usuario = {$this->_id}";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al actualizar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
	}

	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO usuario" .
			"  (" .
			"  id_comuna," .
			"  nombre," .
			"  correo," .
			"  fecha_nacimiento," .
			"  hombre," .
			"  telefono," .
			"  fecha_vencimiento_licencia," .
			"  fecha_modificacion" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_id_comuna) ? "'{$this->_id_comuna}'" : 'null') . ',' .
			"  " . (isset($this->_nombre) ? "'{$this->_nombre}'" : 'null') . ',' .
			"  " . (isset($this->_correo) ? "'{$this->_correo}'" : 'null') . ',' .
			"  " . (isset($this->_fecha_nacimiento) ? "'{$this->_fecha_nacimiento}'" : 'null') . ',' .
			"  " . (isset($this->_hombre) ? "'{$this->_hombre}'" : 'null') . ',' .
			"  " . (isset($this->_telefono) ? "'{$this->_telefono}'" : 'null') . ',' .
			"  " . (isset($this->_fecha_vencimiento_licencia) ? "'{$this->_fecha_vencimiento_licencia}'" : 'null') . ',' .
			"  " . (isset($this->_fecha_modificacion) ? "b'{$this->_fecha_modificacion}'" : 'null') .
			"  )";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al insertar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}

		$ar_id = $p_db->QueryArray('SELECT LAST_INSERT_ID()');
		
		$this->_id = $ar_id[0][0];
		
	}
	
}
?>