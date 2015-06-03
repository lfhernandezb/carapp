# primeros 50
# mysql --batch --skip-column-names --disable-pager --silent -u car -p car --execute="SELECT CONCAT(correo, ',', nombre) FROM usuario WHERE correo IS NOT NULL AND nombre IS NOT NULL ORDER BY fecha_modificacion ASC LIMIT 0,50"
# el resto
mysql --batch --skip-column-names --disable-pager --silent -u car -p car --execute="SELECT CONCAT(correo, ',', nombre) FROM usuario WHERE correo IS NOT NULL AND nombre IS NOT NULL AND correo NOT IN (SELECT correo FROM aux_correo) ORDER BY fecha_modificacion ASC"
