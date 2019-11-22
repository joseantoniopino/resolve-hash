## Sobre esta aplicación
Esta sencilla aplicación genera a 
través del comando `php bin/console Generate:Numbers` 
dos csv. Uno con 1000 números de teléfono y el otro con 70 números encriptados
en md5.

Los archivos son generados en la carpeta /public/csv

En las siguientes carpetas se encontrarán las clases que se han creado
para que todo funcione:

En services.yaml se han hecho un par de configuraciones para apuntar a determinados directorios.

En /src hay un Command, un Controller, tres Exceptions y un Service

Por último en /templates está la vista list y la vista index.

### Para usar la aplicación
Simplemente bajar el repo hacer composer install y probar. 
Dentro de la carpeta publi hay un .htaccess, usarlo o no ya depende vuestra configuración
de servidor.

En principio el único requisito es usar php > 7.1.
