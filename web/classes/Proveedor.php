<?php

include_once('mysql.class.php');
//include_once('Acceso.php');

class Proveedor
{
	private $_id;
	private $_nombre;
	private $_direccion;
	private $_correo;
	private $_telefono;
	private $_latitud;
	private $_longitud;
	private $_existen_valores;
	private $_valor_minimo;
	private $_valor_maximo;
	private $_detalle_html;
	private $_calificacion;
	private $_url;
	private $_fecha_modificacion;
	
	private static $_str_sql = "SELECT p.id_proveedor AS id, p.nombre, p.direccion, p.correo, p.telefono, p.latitud, p.longitud, 
  0+p.existen_valores AS existen_valores, p.valor_minimo, p.valor_maximo, p.detalle_html, p.calificacion, p.url, 
  DATE_FORMAT(p.fecha_modificacion, '%Y-%m-%d %H:%i:%s') AS fecha_modificacion
  FROM proveedor p";

	public function __construct() {
		
	}
	
    public function __set($name, $value)
    {
        //echo "Setting '$name' to '$value'\n";
        switch ($name) {
        	case "id" :
        		$this->_id = $value;
        		break;
        	case "nombre" :
        		$this->_nombre = $value;
        		break;
        	case "direccion" :
        		$this->_direccion = $value;
        		break;
        	case "correo" :
        		$this->_correo = $value;
        		break;
        	case "telefono" :
        		$this->_telefono = $value;
        		break;
        	case "latitud" :
        		$this->_latitud = $value;
        		break;
        	case "longitud" :
        		$this->_longitud = $value;
        		break;
        	case "existen_valores" :
        		$this->_existen_valores = $value;
        		break;
        	case "valor_minimo" :
        		$this->_valor_minimo = $value;
        		break;
        	case "valor_maximo" :
        		$this->_valor_maximo = $value;
        		break;
        	case "detalle_html" :
        		$this->_detalle_html = $value;
        		break;
        	case "calificacion" :
        		$this->_calificacion = $value;
        		break;
        	case "url" :
        		$this->_url = $value;
        		break;
        	case "fecha_modificacion" :
        		$this->_fecha_modificacion = $value;
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
        	case "nombre" :
        		return $this->_nombre;
        	case "direccion" :
        		return $this->_direccion;
        	case "correo" :
        		return $this->_correo;
        	case "telefono" :
        		return $this->_telefono;
        	case "latitud" :
        		return $this->_latitud;
        	case "longitud" :
        		return $this->_longitud;
        	case "existen_valores" :
        		return $this->_existen_valores;
        	case "valor_minimo" :
        		return $this->_valor_minimo;
        	case "valor_maximo" :
        		return $this->_valor_maximo;
        	case "detalle_html" :
        		return $this->_detalle_html;
        	case "calificacion" :
        		return $this->_calificacion;
        	case "url" :
        		return $this->_url;
        	case "fecha_modificacion" :
        		return $this->_fecha_modificacion;
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
		$ret = new Proveedor();
		
		$ret->_id = $p_ar[0]['id'];
		$ret->_nombre = $p_ar[0]['nombre'];
		$ret->_direccion = $p_ar[0]['direccion'];
		$ret->_correo = $p_ar[0]['correo'];
		$ret->_telefono = $p_ar[0]['telefono'];
		$ret->_latitud = $p_ar[0]['latitud'];
		$ret->_longitud = $p_ar[0]['longitud'];
		$ret->_existen_valores = $p_ar[0]['existen_valores'];
		$ret->_valor_minimo = $p_a_ar[0]['valor_minimo'];
		$ret->_valor_maximo = $p_ar[0]['valor_maximo'];
		$ret->_detalle_html = $p_ar[0]['detalle_html'];
		$ret->_calificacion = $p_ar[0]['calificacion'];
		$ret->_url = $p_ar[0]['url'];
		$ret->_fecha_modificacion = $p_ar[0]['fecha_modificacion'];
				
		return $ret;
	}

	public static function getByParameter($p_db, $p_key, $p_value) {
		$ret = null;
		
		$str_sql = self::$_str_sql .
			"  WHERE p.$p_key = $p_value";
		
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
		
		return self::getByParameter($p_db, 'id_proveedor', $p_id);
		
	}	
	
	public static function seekSpecial($p_db, $p_param) {
		
		$str_sql = self::$_str_sql .
			"  WHERE (p.nombre LIKE '%$p_param%'" .
			"  OR p.direccion LIKE '%$p_param%'" .
			"  OR p.correo LIKE '%$p_param%'" .
			"  OR p.telefono LIKE '%$p_param%')";
		
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
								
	        foreach($parameters as $key => $value) {
	    		if ($key == 'id') {
	                $array_clauses[] = "p.id_proveedor = $value";
	            }
	    		else if ($key == 'id_nombre') {
	                $array_clauses[] = "v.id_nombre = $value";
	            }
	            else if ($key == 'borrado') {
	                $array_clauses[] = "v.borrado = b'1'";
	            }
	            else if ($key == 'no borrado') {
	                $array_clauses[] = "v.borrado = b'0'";
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
			"  UPDATE proveedor" .
			"  SET nombre = " . (isset($this->_nombre) ? "'{$this->_nombre}'" : 'null') . ',' .
			"  direccion = " . (isset($this->_direccion) ? "'{$this->_direccion}'" : 'null') . ',' .
			"  correo = " . (isset($this->_correo) ? "'{$this->_correo}'" : 'null') . ',' .
			"  telefono = " . (isset($this->_telefono) ? "'{$this->_telefono}'" : 'null') . ',' .
			"  latitud = " . (isset($this->_latitud) ? "{$this->_latitud}" : 'null') . ',' .
			"  longitud = " . (isset($this->_longitud) ? "{$this->_longitud}" : 'null') . ',' .
			"  existen_valores = " . (isset($this->_existen_valores) ? "b'{$this->_existen_valores}'" : 'null') . ',' .
			"  valor_minimo = " . (isset($this->_valor_minimo) ? "{$this->_valor_minimo}" : 'null') . ',' .
			"  valor_maximo = " . (isset($this->_valor_maximo) ? "{$this->_valor_maximo}" : 'null') . ',' .
			"  detalle_html = " . (isset($this->_detalle_html) ? "'{$this->_detalle_html}'" : 'null') . ',' .
			"  calificacion = " . (isset($this->_calificacion) ? "{$this->_calificacion}" : 'null') . ',' .
			"  url = " . (isset($this->_url) ? "'{$this->_url}'" : 'null') .
			"  WHERE id_proveedor = {$this->_id}";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al actualizar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
	}

	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO proveedor" .
			"  (" .
			"  nombre," .
			"  direccion," .
			"  correo," .
			"  telefono," .
			"  latitud," .
			"  longitud," .
			"  existen_valores," .
			"  valor_minimo," .
			"  valor_maximo," .
			"  detalle_html," .
			"  calificacion," .
			"  url" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_nombre) ? "'{$this->_nombre}'" : 'null') . ',' .
			"  " . (isset($this->_direccion) ? "'{$this->_direccion}'" : 'null') . ',' .
			"  " . (isset($this->_correo) ? "'{$this->_correo}'" : 'null') . ',' .
			"  " . (isset($this->_telefono) ? "'{$this->_telefono}'" : 'null') . ',' .
			"  " . (isset($this->_latitud) ? "{$this->_latitud}" : 'null') . ',' .
			"  " . (isset($this->_longitud) ? "{$this->_longitud}" : 'null') . ',' .
			"  " . (isset($this->_existen_valores) ? "b'{$this->_existen_valores}'" : 'null') . ',' .
			"  " . (isset($this->_valor_minimo) ? "{$this->_valor_minimo}" : 'null') . ',' .
			"  " . (isset($this->_valor_maximo) ? "{$this->_valor_maximo}" : 'null') . ',' .
			"  " . (isset($this->_detalle_html) ? "'{$this->_detalle_html}'" : 'null') . ',' .
			"  " . (isset($this->_calificacion) ? "{$this->_calificacion}" : 'null') . ',' .
			"  " . (isset($this->_url) ? "'{$this->_url}'" : 'null') .
			"  )";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al insertar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}

		$ar_id = $p_db->QueryArray('SELECT LAST_INSERT_ID()');
		
		$this->_id = $ar_id[0][0];
		
	}
	
	public function delete($p_db) {
		
		$str_sql =
			"  DELETE" .
		 	"  FROM proveedor" .
			"  WHERE id_proveedor = {$this->_id}";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al borrar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
				
	}
	
}
?>