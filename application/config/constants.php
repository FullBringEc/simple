<?php

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*
|--------------------------------------------------------------------------
| Webservices
|--------------------------------------------------------------------------
|
|
*/
define('DIRECTORIO_SUBIDA_DOCUMENTOS', 'uploads/documentos/');
define('WS_TIMEOUT_CONEXION', 10);
define('WS_TIMEOUT_RESPUESTA', 10);
define('WS_FIRMA_DOCUMENTOS', 'http://localhost:8080/AgesicFirmaWS/AgesicFirmaServer'); // -- Tomcat Webapp Firma
define('WS_AGESIC_FIRMA','http://localhost:8080/AgesicFirmaWS/AgesicFirma?wsdl'); // -- Tomcat Webapp Firma
define('WS_AGESIC_FIRMA_OK','http://localhost:80/simple_bpm/trunk/etapas/confirmar_firma'); // -- Apache Simple
define('WS_AGESIC_FIRMA_CODEBASE','http://localhost:80/agesic_firma'); // -- Apache Simple
define('WS_AGESIC_FIRMA_XPATH', "//*[local-name() = 'respuesta']/text()");
define('WS_AGESIC_DOCUMENTO_XPATH', "//*[local-name() = 'doc']/text()");
define('WS_AGESIC_DOCUMENTO_SERVIDOR_XPATH', "//*[local-name() = 'doc']/text()");
define('WS_AGESIC_TRAZABLIDAD_CABEZAL', 'http://localhost:9800/trazabilidad/cabezal3');
define('WS_AGESIC_TRAZABLIDAD_LINEA', 'http://localhost:9800/trazabilidad/linea');
define('WS_VERSION_MODELO', '1');
define('WS_XPATH_COD_TRAZABILIDAD', "//*[local-name() = 'guid']/text()");
define('WS_VARIABLE_COD_TRAZABILIDAD', 'codigo_trazabilidad');

/*
|--------------------------------------------------------------------------
| Autenticación SAML
|--------------------------------------------------------------------------
|
|
*/
define('SIMPLE_SAML_AUTHSOURCE', 'simplesaml');
define('ORIGENES_CONFIABLES', serialize(array(0, 'http://localhost', 'http://127.0.0.1')));

/*
|--------------------------------------------------------------------------
| Application
|--------------------------------------------------------------------------
|
|
*/
define('HOST_SISTEMA', (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '');
define('HOST_SISTEMA_DOMINIO', '');
define('MAX_REGISTROS_PAGINA', 5);
define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', '6379');
define('DENEGAR_REMOVER_CAMPOS_BLOQUES', TRUE);
define('LOGIN_ADMIN_COESYS', FALSE);