<?php 
include 'conexion.php';

$accion = $_GET['accion'] ?? '';
$metodo = $_SERVER['REQUEST_METHOD'];

try {
    if ($metodo == 'GET') {
        if ($accion == 'listar') {
            $resultado = $conexion->query("SELECT * FROM usuarios ORDER BY `Nombre`");
            $usuarios = $resultado->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['exito' => true, 'datos' => $usuarios]);
        } 
        elseif ($accion == 'obtener') {
            $id = $_GET['id'] ?? 0;
            $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE `ID` = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $usuario = $resultado->fetch_assoc();
            echo json_encode(['exito' => true, 'datos' => $usuario]);
            $stmt->close();
        }
    } 
    elseif ($metodo == 'POST') {
        $datos = json_decode(file_get_contents('php://input'), true);
        
        if ($accion == 'crear') {
            $nombre = $datos['nombre'] ?? '';
            $correo = $datos['correo'] ?? '';
            $telefono = $datos['telefono'] ?? '';
            $rol = $datos['rol'] ?? '';
            
            if (empty($nombre) || empty($correo) || empty($rol)) {
                echo json_encode(['exito' => false, 'mensaje' => 'Campos requeridos faltantes']);
                exit;
            }
            
            $stmt = $conexion->prepare("INSERT INTO usuarios (`Nombre`, `Correo`, `Telefono`, `Rol`) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre, $correo, $telefono, $rol);
            
            if ($stmt->execute()) {
                echo json_encode(['exito' => true, 'id' => $conexion->insert_id, 'mensaje' => 'Usuario creado exitosamente']);
            } else {
                echo json_encode(['exito' => false, 'mensaje' => 'Error al crear usuario: ' . $conexion->error]);
            }
            $stmt->close();
        }
    }
    elseif ($metodo == 'PUT') {
        $datos = json_decode(file_get_contents('php://input'), true);
        
        if ($accion == 'actualizar') {
            $id = $datos['id'] ?? 0;
            $nombre = $datos['nombre'] ?? '';
            $correo = $datos['correo'] ?? '';
            $telefono = $datos['telefono'] ?? '';
            $rol = $datos['rol'] ?? '';
            
            if (empty($nombre) || empty($correo) || empty($rol)) {
                echo json_encode(['exito' => false, 'mensaje' => 'Campos requeridos faltantes']);
                exit;
            }
            
            $stmt = $conexion->prepare("UPDATE usuarios SET `Nombre` = ?, `Correo` = ?, `Telefono` = ?, `Rol` = ? WHERE `ID` = ?");
            $stmt->bind_param("ssssi", $nombre, $correo, $telefono, $rol, $id);
            
            if ($stmt->execute()) {
                echo json_encode(['exito' => true, 'mensaje' => 'Usuario actualizado exitosamente']);
            } else {
                echo json_encode(['exito' => false, 'mensaje' => 'Error al actualizar usuario']);
            }
            $stmt->close();
        }
    }
    elseif ($metodo == 'DELETE') {
        if ($accion == 'eliminar') {
            $id = $_GET['id'] ?? 0;
            
            $stmt = $conexion->prepare("DELETE FROM usuarios WHERE `ID` = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                echo json_encode(['exito' => true, 'mensaje' => 'Usuario eliminado exitosamente']);
            } else {
                echo json_encode(['exito' => false, 'mensaje' => 'Error al eliminar usuario']);
            }
            $stmt->close();
        }
    }
} catch (Exception $e) {
    echo json_encode(['exito' => false, 'error' => $e->getMessage()]);
}

$conexion->close();
?>
