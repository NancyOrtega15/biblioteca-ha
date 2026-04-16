<?php 
include 'conexion.php';

$accion = $_GET['accion'] ?? '';
$metodo = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json; charset=utf-8');

try {

    // =======================
    // GET
    // =======================
    if ($metodo == 'GET') {

        if ($accion == 'listar') {
            $resultado = $conexion->query("SELECT * FROM usuarios ORDER BY `Nombre`");
            $usuarios = $resultado->fetch_all(MYSQLI_ASSOC);

            echo json_encode(['exito' => true, 'datos' => $usuarios]);
            exit;
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
            exit;
        }

        // ❌ respuesta segura si GET no coincide
        echo json_encode(['exito' => false, 'mensaje' => 'Acción GET no válida']);
        exit;
    } 

    // =======================
    // POST
    // =======================
    elseif ($metodo == 'POST') {

        $datos = json_decode(file_get_contents('php://input'), true);

        // 🔐 LOGIN
        if ($accion == 'login') {

            $nombre = $datos['nombre'] ?? '';
            $password = $datos['password'] ?? '';

            if (empty($nombre) || empty($password)) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Campos requeridos faltantes'
                ]);
                exit;
            }

            $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuario = ?");
            $stmt->bind_param("s", $nombre);
            $stmt->execute();

            $resultado = $stmt->get_result();

            if ($usuario = $resultado->fetch_assoc()) {

                if (md5($password) === $usuario['contraseña']) {

                    echo json_encode([
                        'exito' => true,
                        'mensaje' => 'Login correcto',
                        'usuario' => [
                            'id' => $usuario['ID'],
                            'nombre' => $usuario['usuario'],
                            'rol' => $usuario['Rol']
                        ]
                    ]);
                } else {
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Contraseña incorrecta'
                    ]);
                }

            } else {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Usuario no encontrado'
                ]);
            }

            $stmt->close();
            exit;
        }

        // ❌ si POST no es login
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Acción POST no válida'
        ]);
        exit;
    }

    // =======================
    // DELETE
    // =======================
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
            exit;
        }

        echo json_encode(['exito' => false, 'mensaje' => 'Acción DELETE no válida']);
        exit;
    }

} catch (Exception $e) {

    echo json_encode([
        'exito' => false,
        'error' => $e->getMessage()
    ]);
}

$conexion->close();
?>
