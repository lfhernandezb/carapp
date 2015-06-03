<?php

include_once('mysql.class.php');
//include_once('Acceso.php');

class Parametro
{
	private $_id;
	private $_llave;
	private $_valor;
	private $_fecha_modificacion;
	
	private static $_str_sql = "SELECT p.id_parametro AS id, p.llave, p.valor,
		DATE_FORMAT(p.fecha_modificacion, '%Y-%m-%d %H:%i:%s') AS fecha_modificacion
  		FROM parametro p";

	public function __construct() {
		
	}
	
    public function __set($name, $value)
    {
        //echo "Setting '$name' to '$value'\n";
        switch ($name) {
        	case "id" :
        		$this->_id = $value;
        		break;
        	case "llave" :
        		$this->_llave = $value;
        		break;
        	case "valor" :
        		$this->_valor = $value;
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
        	case "llave" :
        		return $this->_llave;
        	case "valor" :
        		return $this->_valor;
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
		$ret = new Parametro();
		
		$ret->_id = $p_ar[0]['id'];
		$ret->_llave = $p_ar[0]['llave'];
		$ret->_valor = $p_ar[0]['valor'];
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
		
		return self::getByParameter($p_db, 'id_parametro', $p_id);
		
	}	
	
	public static function seekSpecial($p_db, $p_param) {
		
		$str_sql = self::$_str_sql .
			"  WHERE (p.llave LIKE '%$p_param%'" .
			"  OR p.valor LIKE '%$p_param%')";
		
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
	                $array_clauses[] = "p.id_parametro = $value";
	            }
	    		else if ($key == 'id_llave') {
	                $array_clauses[] = "v.id_llave = $value";
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
			"  UPDATE parametro" .
			"  SET llave = " . (isset($this->_llave) ? "'{$this->_llave}'" : 'null') . ',' .
			"  valor = " . (isset($this->_valor) ? "'{$this->_valor}'" : 'null') .
			"  WHERE id_parametro = {$this->_id}";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al actualizar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
	}

	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO parametro" .
			"  (" .
			"  llave," .
			"  valor" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_llave) ? "'{$this->_llave}'" : 'null') . ',' .
			"  " . (isset($this->_valor) ? "'{$this->_valor}'" : 'null') .
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
		 	"  FROM parametro" .
			"  WHERE id_parametro = {$this->_id}";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al borrar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
				
	}
	
	
}
?>