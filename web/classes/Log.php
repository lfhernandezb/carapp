<?php

include_once('mysql.class.php');
include_once('Acceso.php');

class Log
{
	private $_id;
	private $_id_usuario;
	private $_latitud;
	private $_longitud;
	private $_data;
	private $_hombre;
	private $_telefono;
	private $_fecha_vencimiento_licencia;
	private $_fecha_modificacion;
	private $_borrado;
	
	private static $_str_sql = "  SELECT id_log AS id, id_usuario, latitud, longitud, data, DATE_FORMAT(fecha_modificacion, '%Y-%m-%d %H:%i:%s') AS fecha_modificacion, 0+borrado AS borrado
	FROM log l";
	
	public function __construct() {
		
	}
	
    public function __set($name, $value)
    {
        //echo "Setting '$name' to '$value'\n";
        switch ($name) {
        	case "id" :
        		$this->_id = $value;
        		break;
        	case "id_usuario" :
        		$this->_id_usuario = $value;
        		break;
        	case "latitud" :
        		$this->_latitud = $value;
        		break;
        	case "longitud" :
        		$this->_longitud = $value;
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
        	case "id_usuario" :
        		return $this->_id_usuario;
        	case "latitud" :
        		return $this->_latitud;
        	case "longitud" :
        		return $this->_longitud;
        	case "data" :
        		return $this->_data;
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
		$ret = new Log();
		
		$ret->_id = $p_ar[0]['id'];
		$ret->_id_usuario = $p_ar[0]['id_usuario'];
		$ret->_latitud = $p_ar[0]['latitud'];
		$ret->_longitud = $p_ar[0]['longitud'];
		$ret->_data = $p_ar[0]['data'];
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
	
	public static function getByID($p_db, $p_id) {
		$ret = null;
		
		$str_sql = self::$_str_sql .
			"  WHERE l.id_log = '$p_id'";
		
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
	
    public static function seek($db, $parameters, $order, $direction, $offset, $limit) {
		
		try {
			$array_clauses = array();
			
			$str_sql = self::$_str_sql;
								
	        foreach($parameters as $key => $value) {
	    		if ($key == 'id') {
	                $array_clauses[] = "l.id_log = $value";
	            }
	    		if ($key == 'id_usuario') {
	                $array_clauses[] = "l.id_usuario = $value";
	            }
	            else if ($key == 'borrado') {
	                $array_clauses[] = "l.borrado = b'1'";
	            }
	            else if ($key == 'no borrado') {
	                $array_clauses[] = "l.borrado = b'0'";
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
			"  UPDATE log" .
			"  SET id_usuario = " . (isset($this->_id_usuario) ? "'{$this->_id_usuario}'" : 'null') . ',' .
			"  latitud = " . (isset($this->_latitud) ? "'{$this->_latitud}'" : 'null') . ',' .
			"  longitud = " . (isset($this->_longitud) ? "'{$this->_longitud}'" : 'null') . ',' .
			"  data = " . (isset($this->_data) ? "'{$this->_data}'" : 'null') . ',' .
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
			"  INSERT INTO log" .
			"  (" .
			"  id_usuario," .
			"  latitud," .
			"  longitud," .
			"  data," .
			"  fecha_modificacion" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_id_usuario) ? "'{$this->_id_usuario}'" : 'null') . ',' .
			"  " . (isset($this->_latitud) ? "'{$this->_latitud}'" : 'null') . ',' .
			"  " . (isset($this->_longitud) ? "'{$this->_longitud}'" : 'null') . ',' .
			"  " . (isset($this->_data) ? "'{$this->_data}'" : 'null') . ',' .
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