<?php
require_once 'database.php';
session_start();

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);
$error = '';
$success = '';

// Obtener datos del pedido actual
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

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_cliente = trim($_POST['nombre_cliente']);
    $pastel_basico = intval($_POST['pastel_basico']);
    $pastel_mediano = intval($_POST['pastel_mediano']);
    $pastel_grande = intval($_POST['pastel_grande']);
    $estado = $_POST['estado'];
    
    // Validaciones
    if (empty($nombre_cliente)) {
        $error = "El nombre del cliente es obligatorio";
    } elseif ($pastel_basico < 0 || $pastel_mediano < 0 || $pastel_grande < 0) {
        $error = "Las cantidades no pueden ser negativas";
    } elseif ($pastel_basico == 0 && $pastel_mediano == 0 && $pastel_grande == 0) {
        $error = "Debe pedir al menos un pastel";
    } else {
        // Actualizar en la base de datos
        $sql = "UPDATE pedidos SET 
                nombre_cliente = ?, 
                pastel_basico = ?, 
                pastel_mediano = ?, 
                pastel_grande = ?, 
                estado = ? 
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siiisi", $nombre_cliente, $pastel_basico, $pastel_mediano, $pastel_grande, $estado, $id);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Pedido actualizado exitosamente";
            header("Location: index.php");
            exit();
        } else {
            $error = "Error al actualizar el pedido: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pedido - Pastelería</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-edit"></i> Editar Pedido</h1>
            <p class="subtitle">Modificar pedido #<?php echo $pedido['id']; ?></p>
        </header>

        <div class="card">
            <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Volver al inicio</a>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" class="pedido-form">
                <div class="form-group">
                    <label for="nombre_cliente"><i class="fas fa-user"></i> Nombre del Cliente *</label>
                    <input type="text" id="nombre_cliente" name="nombre_cliente" required 
                           value="<?php echo htmlspecialchars($pedido['nombre_cliente']); ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="pastel_basico"><i class="fas fa-cake"></i> Pastel Básico</label>
                        <div class="input-with-icon">
                            <input type="number" id="pastel_basico" name="pastel_basico" min="0" 
                                   value="<?php echo $pedido['pastel_basico']; ?>">
                            <span class="input-icon">$8 c/u</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="pastel_mediano"><i class="fas fa-cake"></i> Pastel Mediano</label>
                        <div class="input-with-icon">
                            <input type="number" id="pastel_mediano" name="pastel_mediano" min="0" 
                                   value="<?php echo $pedido['pastel_mediano']; ?>">
                            <span class="input-icon">$12 c/u</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="pastel_grande"><i class="fas fa-cake"></i> Pastel Grande</label>
                        <div class="input-with-icon">
                            <input type="number" id="pastel_grande" name="pastel_grande" min="0" 
                                   value="<?php echo $pedido['pastel_grande']; ?>">
                            <span class="input-icon">$18 c/u</span>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="estado"><i class="fas fa-truck"></i> Estado del Pedido</label>
                    <select id="estado" name="estado">
                        <option value="recepcionado" <?php echo $pedido['estado'] == 'recepcionado' ? 'selected' : ''; ?>>Recepcionado</option>
                        <option value="despachado" <?php echo $pedido['estado'] == 'despachado' ? 'selected' : ''; ?>>Despachado</option>
                    </select>
                </div>
                
                <div class="form-total">
                    <h3><i class="fas fa-calculator"></i> Resumen del Pedido</h3>
                    <div class="total-row">
                        <span>Pasteles Básicos:</span>
                        <span id="total-basico">$<?php echo $pedido['pastel_basico'] * 8; ?></span>
                    </div>
                    <div class="total-row">
                        <span>Pasteles Medianos:</span>
                        <span id="total-mediano">$<?php echo $pedido['pastel_mediano'] * 12; ?></span>
                    </div>
                    <div class="total-row">
                        <span>Pasteles Grandes:</span>
                        <span id="total-grande">$<?php echo $pedido['pastel_grande'] * 18; ?></span>
                    </div>
                    <div class="total-row total">
                        <span><strong>TOTAL:</strong></span>
                        <span id="total-pedido"><strong>$<?php echo ($pedido['pastel_basico'] * 8) + ($pedido['pastel_mediano'] * 12) + ($pedido['pastel_grande'] * 18); ?></strong></span>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar Pedido
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="js/scripts.js"></script>
    <script>
        // Calcular total en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[type="number"]');
            inputs.forEach(input => {
                input.addEventListener('input', calcularTotal);
            });
        });
        
        function calcularTotal() {
            const basico = parseInt(document.getElementById('pastel_basico').value) || 0;
            const mediano = parseInt(document.getElementById('pastel_mediano').value) || 0;
            const grande = parseInt(document.getElementById('pastel_grande').value) || 0;
            
            const precioBasico = 8;
            const precioMediano = 12;
            const precioGrande = 18;
            
            const totalBasico = basico * precioBasico;
            const totalMediano = mediano * precioMediano;
            const totalGrande = grande * precioGrande;
            const total = totalBasico + totalMediano + totalGrande;
            
            document.getElementById('total-basico').textContent = '$' + totalBasico;
            document.getElementById('total-mediano').textContent = '$' + totalMediano;
            document.getElementById('total-grande').textContent = '$' + totalGrande;
            document.getElementById('total-pedido').innerHTML = '<strong>$' + total + '</strong>';
        }
    </script>
</body>
</html>