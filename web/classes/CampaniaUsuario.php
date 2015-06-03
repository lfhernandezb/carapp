<?php

include_once('mysql.class.php');
//include_once('Acceso.php');

class CampaniaUsuario
{
	private $_id;
	private $_id_campania;
	private $_id_usuario;
	private $_fecha_envio;
	private $_fecha_modificacion;
	private $_borrado;
	
	private static $_str_sql = "SELECT cu.id_campania_usuario AS id, cu.id_usuario, DATE_FORMAT(cu.fecha_envio, '%Y-%m-%d %H:%i:%s') AS fecha_envio, DATE_FORMAT(cu.fecha_modificacion, '%Y-%m-%d %H:%i:%s') AS fecha_modificacion, cu.borrado
  FROM campania_usuario cu";

	public function __construct() {
		
	}
	
    public function __set($name, $value)
    {
        //echo "Setting '$name' to '$value'\n";
        switch ($name) {
        	case "id" :
        		$this->_id = $value;
        		break;
        	case "id_campania" :
        		$this->_id_campania = $value;
        		break;
        	case "id_usuario" :
        		$this->_id_usuario = $value;
        		break;
        	case "fecha_envio" :
        		$this->_fecha_envio = $value;
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
        	case "id_campania" :
        		return $this->_id_campania;
        	case "id_usuario" :
        		return $this->_id_usuario;
        	case "fecha_envio" :
        		return $this->_fecha_envio;
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
		$ret = new CampaniaUsuario();
		
		$ret->_id = $p_ar[0]['id'];
		$ret->_id_campania = $p_ar[0]['id_campania'];
		$ret->_id_usuario = $p_ar[0]['id_usuario'];
		$ret->_fecha_envio = $p_ar[0]['fecha_envio'];
		$ret->_fecha_modificacion = $p_ar[0]['fecha_modificacion'];
		$ret->_borrado = $p_ar[0]['borrado'];
				
		return $ret;
	}
    
	public static function getByID($p_db, $p_id) {
		$ret = null;
		
		$str_sql = self::$_str_sql .
			"  WHERE cu.id_campania_usuario = '$p_id'";
		
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
	                $array_clauses[] = "cu.id_repuesto = $value";
	            }
	    		if ($key == 'id_usuario') {
	                $array_clauses[] = "cu.id_usuario = $value";
	            }
	            else if ($key == 'borrado') {
	                $array_clauses[] = "cu.borrado = b'1'";
	            }
	            else if ($key == 'no borrado') {
	                $array_clauses[] = "cu.borrado = b'0'";
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
			"  UPDATE campania_usuario" .
			"  SET id_campania = " . (isset($this->_id_campania) ? "'{$this->_id_campania}'" : 'null') . ',' .
			"  fecha_envio = " . (isset($this->_fecha_envio) ? "'{$this->_fecha_envio}'" : 'null') . ',' .
			"  combustible = " . (isset($this->_combustible) ? "'{$this->_combustible}'" : 'null') . ',' .
			"  traccion = " . (isset($this->_traccion) ? "'{$this->_traccion}'" : 'null') . ',' .
			"  patente = " . (isset($this->_patente) ? "'{$this->_patente}'" : 'null') . ',' .
			"  fecha_modificacion = " . (isset($this->_fecha_modificacion) ? "b'{$this->_fecha_modificacion}'" : 'null') . ',' .
			"  borrado = " . (isset($this->_borrado) ? "b'{$this->_borrado}'" : 'null') .
			"  WHERE id_campania_usuario = {$this->_id}";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al actualizar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
	}

	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO campania_usuario" .
			"  (" .
			"  id_campania," .
			"  fecha_envio," .
			"  combustible," .
			"  traccion," .
			"  patente," .
			"  fecha_modificacion" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_id_campania) ? "'{$this->_id_campania}'" : 'null') . ',' .
			"  " . (isset($this->_fecha_envio) ? "'{$this->_fecha_envio}'" : 'null') . ',' .
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