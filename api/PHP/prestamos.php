<?php
include 'conexion.php';

$accion = $_GET['accion'] ?? '';
$metodo = $_SERVER['REQUEST_METHOD'];

try {
    if ($metodo == 'GET') {
        if ($accion == 'listar') {
            $resultado = $conexion->query("
                SELECT p.*, u.`Nombre` as usuario, l.`Titulo` as libro 
                FROM prestamos p
                LEFT JOIN usuarios u ON p.`ID usuario` = u.`ID`
                LEFT JOIN libros l ON p.`ID libro` = l.`ID`
                ORDER BY p.`Fecha de préstamo` DESC
            ");
            $prestamos = $resultado->fetch_all(MYSQLI_ASSOC);

            // 🔧 corregido: quitar acento
            echo json_encode(['exito' => true, 'datos' => $prestamos]);
        }
        elseif ($accion == 'obtener') {
            $id = $_GET['id'] ?? 0;

            $stmt = $conexion->prepare("
                SELECT p.*, u.`Nombre` as usuario, l.`Titulo` as libro 
                FROM prestamos p
                LEFT JOIN usuarios u ON p.`ID usuario` = u.`ID`
                LEFT JOIN libros l ON p.`ID libro` = l.`ID`
                WHERE p.`ID` = ?
            ");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $resultado = $stmt->get_result();
            $prestamo = $resultado->fetch_assoc();

            echo json_encode(['exito' => true, 'datos' => $prestamo]);
            $stmt->close();
        }
    }

    elseif ($metodo == 'POST') {
        $datos = json_decode(file_get_contents('php://input'), true);

        if ($accion == 'crear') {

            // 🔧 SIN CAMBIAR NOMBRES DEL JS
            $usuario_id = $datos['usuario_id'] ?? $datos['idUsuario'] ?? $datos['IDUsuario'] ?? null;
            $libro_id = $datos['libro_id'] ?? $datos['idLibro'] ?? $datos['IDLibro'] ?? null;
            $fecha_prestamo = $datos['fecha_prestamo'] ?? date('Y-m-d');
            $fecha_devolucion = $datos['fecha_devolucion'] ?? '';
            $estado = $datos['estado'] ?? 'Activo';

            if (empty($usuario_id) || empty($libro_id)) {
                echo json_encode(['exito' => false, 'mensaje' => 'Usuario y Libro son requeridos']);
                exit;
            }

            $stmt = $conexion->prepare("
                INSERT INTO prestamos (`ID usuario`, `ID libro`, `Fecha de préstamo`, `Fecha de devolución`, `Estado`) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("iisss", $usuario_id, $libro_id, $fecha_prestamo, $fecha_devolucion, $estado);

            if ($stmt->execute()) {
                echo json_encode(['exito' => true, 'id' => $conexion->insert_id, 'mensaje' => 'Préstamo creado exitosamente']);
            } else {
                echo json_encode(['exito' => false, 'mensaje' => 'Error al crear préstamo']);
            }

            $stmt->close();
        }
    }

    elseif ($metodo == 'PUT') {
        $datos = json_decode(file_get_contents('php://input'), true);

        if ($accion == 'actualizar') {

            // 🔧 corregido: usar minúsculas del JS
            $id = $datos['id'] ?? 0;
            $estado = $datos['estado'] ?? '';
            $fecha_devolucion = $datos['fecha_devolucion'] ?? '';

            $stmt = $conexion->prepare("
                UPDATE prestamos 
                SET `Estado` = ?, `Fecha de devolución` = ? 
                WHERE `ID` = ?
            ");
            $stmt->bind_param("ssi", $estado, $fecha_devolucion, $id);

            if ($stmt->execute()) {
                echo json_encode(['exito' => true, 'mensaje' => 'Préstamo actualizado exitosamente']);
            } else {
                echo json_encode(['exito' => false, 'mensaje' => 'Error al actualizar préstamo']);
            }

            $stmt->close();
        }
    }

    elseif ($metodo == 'DELETE') {
        if ($accion == 'eliminar') {
            $id = $_GET['id'] ?? 0;

            $stmt = $conexion->prepare("DELETE FROM prestamos WHERE `ID` = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo json_encode(['exito' => true, 'mensaje' => 'Préstamo eliminado exitosamente']);
            } else {
                echo json_encode(['exito' => false, 'mensaje' => 'Error al eliminar préstamo']);
            }

            $stmt->close();
        }
    }

} catch (Exception $e) {
    echo json_encode(['exito' => false, 'error' => $e->getMessage()]);
}

$conexion->close();
?>
