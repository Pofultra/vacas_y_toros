<?php
// Verificar si el controlador PDO SQLite está disponible
if (in_array('sqlite', PDO::getAvailableDrivers())) {
    echo "El controlador PDO SQLite está habilitado en tu PHP.";
} else {
    echo "El controlador PDO SQLite no está habilitado en tu PHP.";
}
