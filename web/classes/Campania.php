<?php

include_once('mysql.class.php');
include_once('CampaniaUsuario.php');

class Campania
{
	private $_id;
	private $_descripcion;
	private $_activa;
	private $_condicion;
	private $_detalle;
	private $_fecha_inicio;
	private $_fecha_fin;
	private $_periodicidad;
	private $_numero_impresiones;
	private $_manual;
	private $_fecha_modificacion;
	
	private static $_str_sql = "SELECT c.id_campania AS id, c.descripcion, 0+c.activa AS activa, c.condicion, c.detalle, 
  DATE_FORMAT(c.fecha_inicio, '%Y-%m-%d') AS fecha_inicio,   DATE_FORMAT(c.fecha_fin, '%Y-%m-%d') AS fecha_fin, c.periodicidad AS periodicidad, 
  c.numero_impresiones, 0+c.manual AS manual, DATE_FORMAT(c.fecha_modificacion, '%Y-%m-%d %H:%i:%s') AS fecha_modificacion
  FROM campania c";

	public function __construct() {
		
	}
	
    public function __set($name, $value)
    {
        //echo "Setting '$name' to '$value'\n";
        switch ($name) {
        	case "id" :
        		$this->_id = $value;
        		break;
        	case "descripcion" :
        		$this->_descripcion = $value;
        		break;
        	case "activa" :
        		$this->_activa = $value;
        		break;
        	case "condicion" :
        		$this->_condicion = $value;
        		break;
        	case "detalle" :
        		$this->_detalle = $value;
        		break;
        	case "fecha_inicio" :
        		$this->_fecha_inicio = $value;
        		break;
        	case "fecha_fin" :
        		$this->_fecha_fin = $value;
        		break;
        	case "periodicidad" :
        		$this->_periodicidad = $value;
        		break;
        	case "numero_impresiones" :
        		$this->_numero_impresiones = $value;
        		break;
        	case "manual" :
        		$this->_manual = $value;
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
        	case "descripcion" :
        		return $this->_descripcion;
        	case "activa" :
        		return $this->_activa;
        	case "condicion" :
        		return $this->_condicion;
        	case "detalle" :
        		return $this->_detalle;
        	case "fecha_inicio" :
        		return $this->_fecha_inicio;
        	case "fecha_fin" :
        		return $this->_fecha_fin;
        	case "periodicidad" :
        		return $this->_periodicidad;
        	case "numero_impresiones" :
        		return $this->_numero_impresiones;
        	case "manual" :
        		return $this->_manual;
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
		$ret = new Campania();
		
		$ret->_id = $p_ar[0]['id'];
		$ret->_descripcion = $p_ar[0]['descripcion'];
		$ret->_activa = $p_ar[0]['activa'];
		$ret->_condicion = $p_ar[0]['condicion'];
		$ret->_detalle = $p_ar[0]['detalle'];
		$ret->_fecha_inicio = $p_ar[0]['fecha_inicio'];
		$ret->_fecha_fin = $p_ar[0]['fecha_fin'];
		$ret->_periodicidad = $p_ar[0]['periodicidad'];
		$ret->_numero_impresiones = $p_ar[0]['numero_impresiones'];
		$ret->_manual = $p_ar[0]['manual'];
		$ret->_fecha_modificacion = $p_ar[0]['fecha_modificacion'];
				
		return $ret;
	}

	public static function getByParameter($p_db, $p_key, $p_value) {
		$ret = null;
		
		$str_sql = self::$_str_sql .
			"  WHERE c.$p_key = $p_value";
		
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
		
		return self::getByParameter($p_db, 'id_campania', $p_id);
		
	}	
	
	public static function seekSpecial($p_db, $p_param) {
		
		$str_sql = self::$_str_sql .
			"  WHERE (c.descripcion LIKE '%$p_param%') AND c.manual = true";
		
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
	                $array_clauses[] = "c.id_campania = $value";
	            }
	    		else if ($key == 'activa') {
	                $array_clauses[] = "c.activa = $value";
	            }
	    		else if ($key == 'manual') {
	                $array_clauses[] = "c.manual = b'1'";
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
	        	$str_sql .= " ORDER BY c.{$order} $direction";
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
	
	public function getCampaniaUsuario($p_db) {
		$parameters = array();
		
		$parameters['id_campania'] = $this->_id;
		
		return CampaniaUsuario::seek($p_db, $parameters, 'id_campania_usuario', 'ASC', 0, 10000);
	}
	
	public function update($p_db) {

		$str_sql =
			"  UPDATE campania" .
			"  SET descripcion = " . (isset($this->_descripcion) ? "'{$this->_descripcion}'" : 'null') . ',' .
			"  activa = " . (isset($this->_activa) ? "'{$this->_activa}'" : 'null') . ',' .
			"  condicion = " . (isset($this->_condicion) ? "'{$this->_condicion}'" : 'null') . ',' .
			"  detalle = " . (isset($this->_detalle) ? "'{$this->_detalle}'" : 'null') . ',' .
			"  fecha_inicio = " . (isset($this->_fecha_inicio) ? "STR_TO_DATE('{$this->_fecha_inicio}', '%Y-%m-%d')" : 'null') . ',' .
			"  fecha_fin = " . (isset($this->_fecha_fin) ? "STR_TO_DATE('{$this->_fecha_fin}', '%Y-%m-%d')" : 'null') . ',' .
			"  periodicidad = " . (isset($this->_periodicidad) ? "'{$this->_periodicidad}'" : 'null') . ',' .
			"  numero_impresiones = " . (isset($this->_numero_impresiones) ? "{$this->_numero_impresiones}" : 'null') . ',' .
			"  manual = " . (isset($this->_activa) ? "{$this->_activa}" : 'null') .
			"  WHERE id_campania = {$this->_id}";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al actualizar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
	}

	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO campania" .
			"  (" .
			"  descripcion," .
			"  activa," .
			"  condicion," .
			"  detalle," .
			"  fecha_inicio," .
			"  fecha_fin," .
			"  periodicidad," .
			"  numero_impresiones," .
			"  manual" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_descripcion) ? "'{$this->_descripcion}'" : 'null') . ',' .
			"  " . (isset($this->_activa) ? "'{$this->_activa}'" : 'null') . ',' .
			"  " . (isset($this->_condicion) ? "'{$this->_condicion}'" : 'null') . ',' .
			"  " . (isset($this->_detalle) ? "'{$this->_detalle}'" : 'null') . ',' .
			"  " . (isset($this->_fecha_inicio) ? "STR_TO_DATE('{$this->_fecha_inicio}', '%Y-%m-%d')" : 'null') . ',' .
			"  " . (isset($this->_fecha_fin) ? "STR_TO_DATE('{$this->_fecha_fin}', '%Y-%m-%d')" : 'null') . ',' .
			"  " . (isset($this->_periodicidad) ? "b'{$this->_periodicidad}'" : 'null') . ',' .
			"  " . (isset($this->_numero_impresiones) ? "{$this->_numero_impresiones}" : 'null') . ',' .
			"  " . (isset($this->_manual) ? "{$this->_manual}" : 'null') .
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
		 	"  FROM campania" .
			"  WHERE id_campania = {$this->_id}";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al borrar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
				
	}

}
?>