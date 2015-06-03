<?php

	$correos = 'correos.txt';
	$cuerpo = 'correo.html';
	
	// chequeos previos
	
	if (!file_exists($correos)) {
		die("No existe archivo con listado de correos\n");
	}

	if (!is_readable($correos)) {
		die("Archivo con listado de correos no es 'readable'\n");
	}

	if (!file_exists($cuerpo)) {
		die("No existe archivo con cuerpo de correo\n");
	}
	
	if (!is_readable($cuerpo)) {
		die("Archivo con cuerpo de correo no es 'readable'\n");
	}

	$content_template = file_get_contents('correo.html');
	
	$handle = fopen("correos.txt", "r");
	
	if ($handle) {
		while (($line = fgets($handle)) !== false) {
			// process the line read.
			echo($line . "\n");
			$pieces = explode(",", $line);
			$to = $pieces[0];
			$name = htmlentities($pieces[1]);
			// To send HTML mail, the Content-type header must be set
			$headers = '';
			
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
			// Additional headers
			// $headers .= "To: $line\r\n";
			$headers .= 'From: MiAUTO <miauto>' . "\r\n";
			// $headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
			$headers .= "Reply-To: noreply@dsoft.cl\r\n";
			//$headers .= 'Bcc: lfhernandez@dsoft.cl,srivasm1326@gmail.com,cardiles68@gmail.com,sergiotroncosom@gmail.com' . "\r\n";					
			//$headers .= 'Bcc: lfhernandez@dsoft.cl, lfhernandezb@gmail.com' . "\r\n";					
			
			$mail_body = sprintf($content_template, 
				$name);
			
			mail($to, "MiAUTO, Bienvenida", $mail_body, $headers);
			
		}

		fclose($handle);
	} else {
		// error opening the file.
		echo("No se pudo abrir el archivo\n");
	} 
?>
