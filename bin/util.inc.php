<?php

function send_mail($recept,$msg)
{
        $fecha_log=date("Ymd_His");
        $base = basename( $argv[0], ".php" );
        mail($recept,"Info $base","$fecha_log: $msg");
        return;
}
//////////////////
function log_str($texto)
{
		echo $texto . "\n";
		return;
        global $argv;
        // Un log diario
        date_default_timezone_set('America/Santiago');
        $fecha_arc=date("Ymd");
        $fecha_log=date("Ymd_His");
        $pid = posix_getpid();
        $base = basename( $argv[0], ".php" );
        // todos los logs, quedan en ./log/
        $nombre_log="log/" . $base . "_" . $fecha_arc . ".log";
        // printf( "$fecha_log: $texto\n" );
        // return;
        // flush();

        $log_file=fopen($nombre_log,"a");
        if ($log_file!=NULL)
        {
        	fputs($log_file,"$fecha_log: $texto\n");
        	// fputs( $log_file, sprintf( "%s %08d %s\n", $fecha_log, $pid, $texto ) );
        	// fputs( $log_file, $texto );
        }
        else 
        {
	        printf("\nERROR GRAVE! No puedo crear el archivo log " . $nombre_log . "\n");
	        mail("lfhernan@noc.cl","Error en " . $argv[ 0 ], $fecha_log . ": No se pudo crear el archivo logr: " . $nombre_log );
        }
        return;
}

function unquote($p_str) {
	$ret = preg_replace("/^'/", '', $p_str);
	$ret = preg_replace("/'$/", '', $ret);
	$ret = str_replace("''", "'", $ret);
	//var_dump($p_str, $ret);
	return $ret;
}
/*
function read_ini($nombre_archivo,&$dato_leido,$nombre_grupo,
                                        $nombre_campo) {
    $stal=0;
    $ini_file=fopen($nombre_archivo,"r");
    if ($ini_file!=NULL) {
                $stal=-1;
                while ($stal==-1 && ($linea=fgets($ini_file))!=NULL) {
                        $linea = trim( $linea ) ;
                        if( strlen($linea) == 0 || $linea[0] == '#')
                                continue;

                    $pos=strpos($linea,$nombre_grupo);
                if (strcmp($pos,"")!=0)
                                $stal=$pos;
                }
                if ($stal==-1) {
                return -2;
            }
            $stal=-1;
            while ($stal==-1 && ($linea=fgets($ini_file))!=NULL) {
                        $linea = trim( $linea ) ;
                        if( strlen($linea) == 0 || $linea[0] == '#')
                                continue;

                        $pos=strpos($linea,$nombre_campo);
                        if (strcmp($pos,"")!=0)
                                $stal=$pos;
            }
            if ($stal==-1) {
                        return -3;
            }
                $datot1=substr($linea,strlen($nombre_campo));
                $datot2=str_replace("\r","",$datot1);
                $dato_leido=str_replace("\n","",$datot2);
    
                return 0;
    }
        printf("\nERROR! No se encontro el archivo $nombre_archivo.");
        logeaStr("ERROR! No se encontro el archivo $nombre_archivo.");
        return -1;
}
*/
?>