<?php

include_once('mysql.class.php');

class MantencionBase
{
	private $_id;
	private $_nombre;
	
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
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }
	
    public static function seek($p_db, $p_param) {
		
		$str_sql =
			"  SELECT id_mantencion_base AS id, nombre" .
		 	"  FROM mantencion_base mb" .
			"  WHERE mb.nombre LIKE '%$p_param%'";
		
		// echo '<br> . $str_sql . '<br>';
		
		$ret = $p_db->QueryArray($str_sql, MYSQL_ASSOC);
		
		if (!is_array($ret)) {

			if ($p_db->RowCount() != 0) {
				throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
			}
		}
		
		return $ret;
	}
	
	public static function getByNombre($p_db, $p_nombre) {
		$ret = null;
		
		$str_sql =
			"  SELECT id_mantencion_base AS id, nombre" .
		 	"  FROM mantencion_base mb" .
			"  WHERE mb.nombre = '$p_nombre'";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = new MantencionBase();
			
			$ret->_id = $ar[0]['id'];
			$ret->_nombre = $ar[0]['nombre'];
 		}
		else if ($p_db->RowCount() != 0) {
			throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
 		 		
		return $ret;
		
	}
	
	public static function getByID($p_db, $p_id) {
		$ret = null;
		
		$str_sql =
			"  SELECT id_mantencion_base AS id, nombre" .
		 	"  FROM mantencion_base mb" .
			"  WHERE mb.id_mantencion_base = $p_id";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = new MantencionBase();
			
			$ret->_id = $ar[0]['id'];
			$ret->_nombre = $ar[0]['nombre'];
 		}
		else if ($p_db->RowCount() != 0) {
			throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
 		 		
		return $ret;
		
	}
	
	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO mantencion_base" .
			"  (" .
			"  nombre" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_nombre) ? "'{$this->_nombre}'" : 'null') .
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