<?php
require_once 'database.php';
session_start();

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

// Verificar si el pedido existe
$sql = "SELECT * FROM pedidos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$pedido = $result->fetch_assoc();

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirmar']) && $_POST['confirmar'] == 'si') {
        $sql = "DELETE FROM pedidos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Pedido eliminado exitosamente";
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['mensaje'] = "Error al eliminar el pedido";
            header("Location: index.php");
            exit();
        }
    } else {
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Pedido - Pastelería</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-trash-alt"></i> Eliminar Pedido</h1>
            <p class="subtitle">Confirmar eliminación del pedido</p>
        </header>

        <div class="card">
            <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Volver al inicio</a>
            
            <div class="confirmacion">
                <h2><i class="fas fa-exclamation-triangle"></i> ¿Estás seguro de eliminar este pedido?</h2>
                
                <div class="pedido-info">
                    <h3>Detalles del pedido a eliminar:</h3>
                    <div class="info-row">
                        <span class="info-label">ID del Pedido:</span>
                        <span class="info-value">#<?php echo $pedido['id']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Cliente:</span>
                        <span class="info-value"><?php echo htmlspecialchars($pedido['nombre_cliente']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Pasteles Básicos:</span>
                        <span class="info-value"><?php echo $pedido['pastel_basico']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Pasteles Medianos:</span>
                        <span class="info-value"><?php echo $pedido['pastel_mediano']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Pasteles Grandes:</span>
                        <span class="info-value"><?php echo $pedido['pastel_grande']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Estado:</span>
                        <span class="info-value status <?php echo $pedido['estado'] == 'despachado' ? 'status-despachado' : 'status-recepcionado'; ?>">
                            <?php echo ucfirst($pedido['estado']); ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fecha:</span>
                        <span class="info-value"><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></span>
                    </div>
                </div>
                
                <form method="POST" action="" class="form-confirmacion">
                    <input type="hidden" name="confirmar" value="si">
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Sí, eliminar pedido
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="js/scripts.js"></script>
</body>
</html>