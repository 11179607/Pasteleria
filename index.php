<?php
require_once 'database.php';
session_start();

// Verificar si hay mensajes de sesión
$mensaje = '';
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pastelería - Gestión de Pedidos</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-birthday-cake"></i> Pastelería Dulce Sabor</h1>
            <p class="subtitle">Sistema de Gestión de Pedidos</p>
        </header>

        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <div class="dashboard">
            <div class="stats-card">
                <h3><i class="fas fa-users"></i> Total Clientes</h3>
                <?php
                $sql = "SELECT COUNT(DISTINCT nombre_cliente) as total FROM pedidos";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                echo "<p class='stat'>" . $row['total'] . "</p>";
                ?>
            </div>
            
            <div class="stats-card">
                <h3><i class="fas fa-box"></i> Pedidos Recepcionados</h3>
                <?php
                $sql = "SELECT COUNT(*) as total FROM pedidos WHERE estado = 'recepcionado'";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                echo "<p class='stat'>" . $row['total'] . "</p>";
                ?>
            </div>
            
            <div class="stats-card">
                <h3><i class="fas fa-shipping-fast"></i> Pedidos Despachados</h3>
                <?php
                $sql = "SELECT COUNT(*) as total FROM pedidos WHERE estado = 'despachado'";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                echo "<p class='stat'>" . $row['total'] . "</p>";
                ?>
            </div>
        </div>

        <div class="menu">
            <a href="crear_pedido.php" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Nuevo Pedido
            </a>
            <a href="listar_pedidos.php" class="btn btn-secondary">
                <i class="fas fa-list"></i> Ver Todos los Pedidos
            </a>
        </div>

        <div class="recent-orders">
            <h2><i class="fas fa-clock"></i> Pedidos Recientes</h2>
            <?php
            $sql = "SELECT * FROM pedidos ORDER BY fecha_pedido DESC LIMIT 5";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                echo "<table class='data-table'>";
                echo "<thead><tr>
                        <th>Cliente</th>
                        <th>Básico</th>
                        <th>Mediano</th>
                        <th>Grande</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                      </tr></thead>";
                echo "<tbody>";
                
                while($row = $result->fetch_assoc()) {
                    $estado_class = $row['estado'] == 'despachado' ? 'status-despachado' : 'status-recepcionado';
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['nombre_cliente']) . "</td>";
                    echo "<td class='text-center'>" . $row['pastel_basico'] . "</td>";
                    echo "<td class='text-center'>" . $row['pastel_mediano'] . "</td>";
                    echo "<td class='text-center'>" . $row['pastel_grande'] . "</td>";
                    echo "<td><span class='status $estado_class'>" . ucfirst($row['estado']) . "</span></td>";
                    echo "<td>" . date('d/m/Y', strtotime($row['fecha_pedido'])) . "</td>";
                    echo "<td class='actions'>";
                    echo "<a href='editar_pedido.php?id=" . $row['id'] . "' class='btn-action edit'><i class='fas fa-edit'></i></a>";
                    echo "<a href='eliminar_pedido.php?id=" . $row['id'] . "' class='btn-action delete' onclick='return confirm(\"¿Estás seguro?\")'><i class='fas fa-trash'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                }
                
                echo "</tbody></table>";
            } else {
                echo "<p class='no-data'>No hay pedidos registrados.</p>";
            }
            ?>
        </div>
        
        <footer>
            <p>Sistema de Gestión de Pedidos - Pastelería Dulce Sabor &copy; <?php echo date('Y'); ?></p>
        </footer>
    </div>
    
    <script src="js/scripts.js"></script>
</body>
</html>