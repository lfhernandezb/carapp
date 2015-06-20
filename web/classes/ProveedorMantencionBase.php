<?php

include_once('mysql.class.php');

class ProveedorMantencionBase
{
	private $_id;
	private $_id_proveedor;
	private $_id_mantencion_base;
	
	private static $_str_sql = "
  SELECT pmb.id_proveedor_mantencion_base AS id, pmb.id_proveedor, pmb.id_mantencion_base
  FROM mantencion_base_proveedor pmb
	";
	
	public function __construct() {
	
	}
	
    public function __set($name, $value)
    {
        //echo "Setting '$name' to '$value'\n";
        switch ($name) {
        	case "id" :
        		$this->_id = $value;
        		break;
        	case "id_proveedor" :
        		$this->_id_proveedor = $value;
        		break;
        	case "id_mantencion_base" :
        		$this->_id_mantencion_base = $value;
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
        	case "id_proveedor" :
        		return $this->_id_proveedor;
        	case "id_mantencion_base" :
        		return $this->_id_mantencion_base;
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }
	
	public static function seekSpecial($p_db, $p_id_usuario) {
		
	$str_sql = "
  SELECT mb.id_mantencion_base, mb.nombre, pmb.id_proveedor
  FROM mantencion_base mb
  LEFT JOIN proveedor_mantencion_base pmb ON pmb.id_mantencion_base = mb.id_mantencion_base AND pmb.id_proveedor = $p_id_usuario
	";
				
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
	    		if ($key == "id") {
	                $array_clauses[] = "pmb.id_proveedor_mantencion_base = $value";
	            }
	            else if ($key == 'id_proveedor') {
				    $array_clauses[] = "pmb.id_proveedor = $value";
	            }
	            else if ($key == 'id_mantencion_base') {
				    $array_clauses[] = "pmb.id_mantencion_base = $value";
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
	        	$str_sql .= " OFFSET $offset LIMIT $limit";
	        }
			
	        //echo '<br>' . $str_sql . '<br>';
		
			$ret = $db->QueryArray($str_sql, MYSQL_ASSOC);
			
			if (!is_array($ret)) {

				if ($db->RowCount() != 0) {
					throw new Exception('Error al obtener registro: ' . $db->Error(), $db->ErrorNumber(), null);
				}
			}
			
			return $ret;
		} catch (Exception $e) {
			throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
		}
	}
	
	public static function getById($p_db, $p_id) {
		$ret = null;
		
		$str_sql = self::$_str_sql .
			"  WHERE pmb.id_proveedor_mantencion_base = '$p_id'";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = new ProveedorMantencionBase();
			
			$ret->_id = $ar[0]['id'];
			$ret->_id_proveedor = $ar[0]['id_proveedor'];
			$ret->_id_mantencion_base = $ar[0]['id_mantencion_base'];
		}
		else if ($p_db->RowCount() != 0) {
			throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
 		 		
		return $ret;
	}

	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO proveedor_mantencion_base" .
			"  (" .
			"  id_proveedor," .
			"  id_mantencion_base" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_id_proveedor) ? "'{$this->_id_proveedor}'" : 'null') . ',' .
			"  " . (isset($this->_id_mantencion_base) ? "'{$this->_id_mantencion_base}'" : 'null') .
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
		 	"  FROM proveedor_mantencion_base" .
			"  WHERE id_proveedor_mantencion_base = {$this->_id}";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al borrar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
				
	}

	public static function deleteByIdProveedor($p_db, $p_id_proveedor) {
		
		$str_sql =
			"  DELETE" .
		 	"  FROM proveedor_mantencion_base" .
			"  WHERE id_proveedor = {$p_id_proveedor}";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al borrar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
				
	}
	
}
?>