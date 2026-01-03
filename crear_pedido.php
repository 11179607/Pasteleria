<?php
require_once 'database.php';
session_start();

$error = '';
$success = '';

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
        // Insertar en la base de datos
        $sql = "INSERT INTO pedidos (nombre_cliente, pastel_basico, pastel_mediano, pastel_grande, estado) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siiis", $nombre_cliente, $pastel_basico, $pastel_mediano, $pastel_grande, $estado);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Pedido creado exitosamente";
            header("Location: index.php");
            exit();
        } else {
            $error = "Error al crear el pedido: " . $conn->error;
        }
        
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Pedido - Pastelería</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-plus-circle"></i> Nuevo Pedido</h1>
            <p class="subtitle">Registrar un nuevo pedido de pasteles</p>
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
                           value="<?php echo isset($_POST['nombre_cliente']) ? htmlspecialchars($_POST['nombre_cliente']) : ''; ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="pastel_basico"><i class="fas fa-cake"></i> Pastel Básico</label>
                        <div class="input-with-icon">
                            <input type="number" id="pastel_basico" name="pastel_basico" min="0" value="0">
                            <span class="input-icon">$8 c/u</span>
                        </div>
                        <small>Cada pastel básico cuesta $8</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="pastel_mediano"><i class="fas fa-cake"></i> Pastel Mediano</label>
                        <div class="input-with-icon">
                            <input type="number" id="pastel_mediano" name="pastel_mediano" min="0" value="0">
                            <span class="input-icon">$12 c/u</span>
                        </div>
                        <small>Cada pastel mediano cuesta $12</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="pastel_grande"><i class="fas fa-cake"></i> Pastel Grande</label>
                        <div class="input-with-icon">
                            <input type="number" id="pastel_grande" name="pastel_grande" min="0" value="0">
                            <span class="input-icon">$18 c/u</span>
                        </div>
                        <small>Cada pastel grande cuesta $18</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="estado"><i class="fas fa-truck"></i> Estado del Pedido</label>
                    <select id="estado" name="estado">
                        <option value="recepcionado">Recepcionado</option>
                        <option value="despachado">Despachado</option>
                    </select>
                </div>
                
                <div class="form-total">
                    <h3><i class="fas fa-calculator"></i> Resumen del Pedido</h3>
                    <div class="total-row">
                        <span>Pasteles Básicos:</span>
                        <span id="total-basico">$0</span>
                    </div>
                    <div class="total-row">
                        <span>Pasteles Medianos:</span>
                        <span id="total-mediano">$0</span>
                    </div>
                    <div class="total-row">
                        <span>Pasteles Grandes:</span>
                        <span id="total-grande">$0</span>
                    </div>
                    <div class="total-row total">
                        <span><strong>TOTAL:</strong></span>
                        <span id="total-pedido"><strong>$0</strong></span>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Pedido
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Limpiar
                    </button>
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
            
            calcularTotal();
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