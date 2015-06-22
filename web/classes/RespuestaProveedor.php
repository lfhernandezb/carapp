<?php

include_once('mysql.class.php');
//include_once('Acceso.php');

class RespuestaProveedor
{
	private $_id;
	private $_usuario;
	private $_marca;
	private $_modelo;
	private $_combustible;
	private $_traccion;
	private $_mantencion_base;
	private $_patente;
	private $_anio;
	private $_km;
	private $_aire_acondicionado;
	private $_alza_vidrios;
	private $_fecha_modificacion;
	private $_borrado;
	
	private static $_str_sql = "
  SELECT rp.id_respuesta_proveedor AS id, u.nombre AS usuario, ma.descripcion AS marca, mo.descripcion AS modelo, v.anio AS anio, mb.nombre as mantencion_base,
  DATE_FORMAT(rp.fecha_modificacion, '%Y-%m-%d %H:%i:%s') AS fecha_modificacion
  FROM respuesta_proveedor rp
  JOIN consulta_proveedor cp ON cp.id_consulta_proveedor = rp.id_consulta_proveedor
  JOIN mantencion_base mb ON mb.id_mantencion_base = cp.id_mantencion_base
  JOIN vehiculo v ON v.id_vehiculo = cp.id_vehiculo AND v.id_usuario = cp.id_usuario
  JOIN modelo mo ON mo.id_modelo = v.id_modelo
  JOIN marca ma ON ma.id_marca = mo.id_marca
  JOIN usuario u ON u.id_usuario = v.id_usuario
  -- JOIN combustible c ON v.id_combustible = v.id_combustible
  -- JOIN traccion tr ON tr.id_traccion = v.id_traccion\n";

	public function __construct() {
		
	}
	
    public function __set($name, $value)
    {
        //echo "Setting '$name' to '$value'\n";
        switch ($name) {
        	case "id" :
        		$this->_id = $value;
        		break;
        	case "usuario" :
        		$this->_usuario = $value;
        		break;
        	case "marca" :
        		$this->_marca = $value;
        		break;
        	case "modelo" :
        		$this->_modelo = $value;
        		break;
        	case "combustible" :
        		$this->_combustible = $value;
        		break;
        	case "traccion" :
        		$this->_traccion = $value;
        		break;
        	case "mantencion_base" :
        		$this->_mantencion_base = $value;
        		break;
        	case "patente" :
        		$this->_patente = $value;
        		break;
        	case "anio" :
        		$this->_anio = $value;
        		break;
        	case "km" :
        		$this->_km = $value;
        		break;
        	case "aire_acondicionado" :
        		$this->_aire_acondicionado = $value;
        		break;
        	case "alza_vidrios" :
        		$this->_alza_vidrios = $value;
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
        	case "usuario" :
        		return $this->_usuario;
        	case "marca" :
        		return $this->_marca;
        	case "modelo" :
        		return $this->_modelo;
        	case "combustible" :
        		return $this->_combustible;
        	case "traccion" :
        		return $this->_traccion;
        	case "mantencion_base" :
        		return $this->_mantencion_base;
        	case "patente" :
        		return $this->_patente;
        	case "anio" :
        		return $this->_anio;
        	case "km" :
        		return $this->_km;
        	case "aire_acondicionado" :
        		return $this->_aire_acondicionado;
        	case "alza_vidrios" :
        		return $this->_alza_vidrios;
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
		$ret = new RespuestaProveedor();
		
		$ret->_id = $p_ar[0]['id'];
		$ret->_usuario = $p_ar[0]['usuario'];
		$ret->_marca = $p_ar[0]['marca'];
		$ret->_modelo = $p_ar[0]['modelo'];
		//$ret->_combustible = $p_ar[0]['combustible'];
		//$ret->_traccion = $p_ar[0]['traccion'];
		$ret->_mantencion_base = $p_ar[0]['mantencion_base'];
		//$ret->_patente = $p_ar[0]['patente'];
		$ret->_anio = $p_a_ar[0]['anio'];
		//$ret->_km = $p_ar[0]['km'];
		//$ret->_aire_acondicionado = $p_ar[0]['aire_acondicionado'];
		//$ret->_alza_vidrios = $p_ar[0]['alza_vidrios'];
		$ret->_fecha_modificacion = $p_ar[0]['fecha_modificacion'];
		//$ret->_borrado = $p_ar[0]['borrado'];
				
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
			"  JOIN modelo m ON m.modelo = r.modelo_FK" .
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
	/*
	public static function getByUsername($p_db, $p_username) {
		$ret = null;
		
		$str_sql =
			"  SELECT id_vehiculo, usuario, modelo, combustible, traccion, patente, 0+fecha_modificacion AS fecha_modificacion, 0+borrado AS borrado" .
		 	"  FROM vehiculo rp" .
			"  WHERE u.modelo_usuario = '$p_username'";
		
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
	*/
	public static function getBypatente($p_db, $p_patente) {
		$ret = null;
		
		$str_sql =
			"  SELECT id_respuesta_proveedor, usuario, modelo, combustible, traccion, patente, 0+fecha_modificacion AS fecha_modificacion, 0+borrado AS borrado" .
		 	"  FROM respuesta_proveedor rp" .
			"  WHERE rp.patente = '$p_patente'";
		
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
		
		$str_sql =
			"  SELECT id_respuesta_proveedor, usuario, modelo, combustible, traccion, patente, 0+fecha_modificacion AS fecha_modificacion, 0+borrado AS borrado" .
		 	"  FROM respuesta_proveedor rp" .
			"  WHERE rp.id_respuesta_proveedor = '$p_id'";
		
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
	
	public static function seekSpecial($p_db, $p_param) {
		
		$str_sql = self::$_str_sql .
			"  WHERE (ma.descripcion LIKE '%$p_param%'" .
			"  OR mo.descripcion LIKE '%$p_param%'" .
			"  OR c.descripcion LIKE '%$p_param%'" .
			"  OR rp.patente LIKE '%$p_param%'" .
			"  OR tr.descripcion LIKE '%$p_param%')" .
			"  AND u.borrado = b'0'";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ret = $p_db->QueryArray($str_sql, MYSQL_ASSOC);
		
		if (!is_array($ret)) {
			$ret = null;

			if ($p_db->RowCount() != 0) {
				throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
			}
		}
		
		return $ret;
		
	}
	
    public static function seek($db, $parameters, $order, $direction, $offset, $limit) {
		
		try {
			$array_clauses = array();
			
			$str_sql = self::$_str_sql;
			//"  SELECT rp.id_vehiculo AS id, rp.id_usuario, rp.id_modelo, rp.id_combustible, rp.id_traccion, rp.patente, 0+rp.fecha_modificacion AS fecha_modificacion, 0+rp.borrado AS borrado" .
		 	//"  FROM vehiculo rp";
								
	        foreach($parameters as $key => $value) {
	    		if ($key == 'id') {
	                $array_clauses[] = "rp.id_respuesta_proveedor = $value";
	            }
	    		else if ($key == 'id_usuario') {
	                $array_clauses[] = "cp.id_usuario = $value";
	            }
	    		else if ($key == 'id_vehiculo') {
	                $array_clauses[] = "cp.id_vehiculo = $value";
	            }
	    		else if ($key == 'id_proveedor') {
	                $array_clauses[] = "rp.id_proveedor = $value";
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
			
	        if (isset($order) && isset($direction)) {
	        	$str_sql .= " ORDER BY $order $direction";
	        }
	        
	        if (isset($offset) && isset($limit)) {
	        	$str_sql .= "  LIMIT $offset, $limit";
	        }
			
	        //echo '<br>' . $str_sql . '<br>';
		
			$ret = $db->QueryArray($str_sql, MYSQL_ASSOC);
			
			if (!is_array($ret)) {
				$ret = null;

				if ($db->RowCount() != 0) {
					throw new Exception('Error al obtener registro: ' . $db->Error(), $db->ErrorNumber(), null);
				}
			}
			
			return $ret;
		} catch (Exception $e) {
			throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
		}
	}
	
	public function update($p_db) {

		$str_sql =
			"  UPDATE respuesta_proveedor" .
			"  SET usuario = " . (isset($this->_usuario) ? "'{$this->_usuario}'" : 'null') . ',' .
			"  modelo = " . (isset($this->_modelo) ? "'{$this->_modelo}'" : 'null') . ',' .
			"  combustible = " . (isset($this->_combustible) ? "'{$this->_combustible}'" : 'null') . ',' .
			"  traccion = " . (isset($this->_traccion) ? "'{$this->_traccion}'" : 'null') . ',' .
			"  patente = " . (isset($this->_patente) ? "'{$this->_patente}'" : 'null') . ',' .
			"  fecha_modificacion = " . (isset($this->_fecha_modificacion) ? "b'{$this->_fecha_modificacion}'" : 'null') . ',' .
			"  borrado = " . (isset($this->_borrado) ? "b'{$this->_borrado}'" : 'null') .
			"  WHERE id_respuesta_proveedor = {$this->_id}";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al actualizar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
	}

	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO respuesta_proveedor" .
			"  (" .
			"  usuario," .
			"  modelo," .
			"  combustible," .
			"  traccion," .
			"  patente," .
			"  fecha_modificacion" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_usuario) ? "'{$this->_usuario}'" : 'null') . ',' .
			"  " . (isset($this->_modelo) ? "'{$this->_modelo}'" : 'null') . ',' .
			"  " . (isset($this->_combustible) ? "'{$this->_combustible}'" : 'null') . ',' .
			"  " . (isset($this->_traccion) ? "'{$this->_traccion}'" : 'null') . ',' .
			"  " . (isset($this->_patente) ? "'{$this->_patente}'" : 'null') . ',' .
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