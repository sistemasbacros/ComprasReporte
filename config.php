<?php
$serverName = "WIN-44O80L37Q7M\COMERCIAL"; // o "localhost" o "SERVIDOR\INSTANCIA"
$database = "BASENUEVA";
$username = "sa";
$password = "Administrador1*";

// Opciones de conexión
$connectionInfo = array(
    "Database" => $database,
    "UID" => $username,
    "PWD" => $password,
    "CharacterSet" => "UTF-8"
);

// Intentar la conexión
$contpaq = sqlsrv_connect($serverName, $connectionInfo);

// Verificar la conexión
if ($contpaq) {
    //echo "Conexión exitosa a SQL Server.";
} else {
    echo "Error en la conexión:";
    die(print_r(sqlsrv_errors(), true));
}
?>

