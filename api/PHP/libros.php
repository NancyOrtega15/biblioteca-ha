<?php
include 'conexion.php';

$accion = $_GET['accion'] ?? '';
$metodo = $_SERVER['REQUEST_METHOD'];

try {
    if ($metodo == 'GET') {
        if ($accion == 'listar') {
            $resultado = $conexion->query("SELECT * FROM libros ORDER BY `Titulo`");
            $libros = $resultado->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['exito' => true, 'datos' => $libros]);
        }
        elseif ($accion == 'obtener') {
            $id = $_GET['id'] ?? 0;
            $stmt = $conexion->prepare("SELECT * FROM libros WHERE `ID` = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $libro = $resultado->fetch_assoc();
            echo json_encode(['exito' => true, 'datos' => $libro]);
            $stmt->close();
        }
    }
    elseif ($metodo == 'POST') {
        $datos = json_decode(file_get_contents('php://input'), true);
        
        if ($accion == 'crear') {
            $ID = $datos['ID'] ?? 0;
            $titulo = $datos['Titulo'] ?? '';
            $autor = $datos['Autor'] ?? '';
            $isbn = $datos['ISBN'] ?? '';
            $paginas = $datos['Paginas'] ?? 0;
            $editorial = $datos['Editorial'] ?? '';
            $categoria = $datos['Categoria'] ?? '';
            
            if (empty($titulo) || empty($autor)) {
                echo json_encode(['exito' => false, 'mensaje' => 'Título y Autor son requeridos']);
                exit;
            }
            
            $stmt = $conexion->prepare("
                INSERT INTO libros (`Titulo`, `Autor`, `ISBN`, `Paginas`, `Editorial`, `Categoria`) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssiss", $titulo, $autor, $isbn, $paginas, $editorial, $categoria);
            
            if ($stmt->execute()) {
                echo json_encode(['exito' => true, 'id' => $conexion->insert_id, 'mensaje' => 'Libro registrado exitosamente']);
            } else {
                echo json_encode(['exito' => false, 'mensaje' => 'Error al registrar libro']);
            }
            $stmt->close();
        }
    }
    elseif ($metodo == 'PUT') {
        $datos = json_decode(file_get_contents('php://input'), true);
        
        if ($accion == 'actualizar') {
            $id = $datos['ID'] ?? 0;
            $titulo = $datos['Titulo'] ?? '';
            $autor = $datos['Autor'] ?? '';
            $isbn = $datos['ISBN'] ?? '';
            $paginas = $datos['Paginas'] ?? 0;
            $editorial = $datos['Editorial'] ?? '';
            $categoria = $datos['Categoria'] ?? '';
            
            $stmt = $conexion->prepare("
                UPDATE libros 
                SET `Titulo` = ?, `Autor` = ?, `ISBN` = ?, `Paginas` = ?, `Editorial` = ?, `Categoria` = ? 
                WHERE `ID` = ?
            ");
            $stmt->bind_param("sssissi", $titulo, $autor, $isbn, $paginas, $editorial, $categoria, $id);
            
            if ($stmt->execute()) {
                echo json_encode(['exito' => true, 'mensaje' => 'Libro actualizado exitosamente']);
            } else {
                echo json_encode(['exito' => false, 'mensaje' => 'Error al actualizar libro']);
            }
            $stmt->close();
        }
    }
    elseif ($metodo == 'DELETE') {
        if ($accion == 'eliminar') {
            $id = $_GET['ID'] ?? 0;
            
            $stmt = $conexion->prepare("DELETE FROM libros WHERE `ID` = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                echo json_encode(['exito' => true, 'mensaje' => 'Libro eliminado exitosamente']);
            } else {
                echo json_encode(['exito' => false, 'mensaje' => 'Error al eliminar libro']);
            }
            $stmt->close();
        }
    }
} catch (Exception $e) {
    echo json_encode(['exito' => false, 'error' => $e->getMessage()]);
}

$conexion->close();
?>
