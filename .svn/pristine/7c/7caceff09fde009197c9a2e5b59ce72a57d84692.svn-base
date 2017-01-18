#borramos los archivos anteriores
rm /tmp/CSD.txt /tmp/FoliosCFD.txt


#descargamos los actualizados (cada 24hrs), si no pudimos descargarlos interrumpimos ejecucion
wget ftp://ftp2.sat.gob.mx/agti_servicio_ftp/verifica_comprobante_ftp/FoliosCFD.txt
if [ $? != 0 ];
	then
	echo 'No se pudo descargar el archivo FoliosCFD.txt del SAT'
	exit 1;
fi
mv FoliosCFD.txt /tmp/
if [ $? != 0 ];
	then
	echo 'No se pudo mover el archivo FoliosCFD.txt del SAT, talves se descargo mal'
	exit 1;
fi

wget ftp://ftp2.sat.gob.mx/agti_servicio_ftp/verifica_comprobante_ftp/CSD.txt
if [ $? != 0 ];
	then
	echo 'No se pudo descargar el archivo CSD.txt del SAT'
	exit 1;
fi
mv CSD.txt /tmp/
if [ $? != 0 ];
	then
	echo 'No se pudo mover el archivo CSD.txt del SAT, talves se descargo mal'
	exit 1;
fi


#escribimos en la base de datos
pathToMysql='/opt/lampp/bin'
"$pathToMysql/mysql" -u root < script.sql
