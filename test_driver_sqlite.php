<?php
// Verificar si el controlador PDO SQLite está disponible
if (in_array('sqlite', PDO::getAvailableDrivers())) {
    echo "El controlador PDO SQLite está habilitado en tu PHP.";
} else {
    echo "El controlador PDO SQLite no está habilitado en tu PHP.";
}
// Conectar a la base de datos SQLite
$pdo = new PDO('sqlite://media/ultra/Datos/proyectos_personales/vacas_y_toros/database/database.sqlite');

// Ejecutar una consulta SQL para obtener los registros
$query = "SELECT * FROM games";
$stmt = $pdo->query($query);

// Recorrer los resultados y mostrarlos
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Aquí puedes acceder a los valores de cada columna
    echo "ID: " . $row['id'] . ", user_name: " . $row['user_name'] . ", age: " . $row['user_age'] . "<br>";
}

// Cerrar la conexión a la base de datos
$pdo = null;