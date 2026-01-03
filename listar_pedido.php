<?php
require_once 'database.php';
session_start();

// Procesar filtros
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$filtro_cliente = isset($_GET['cliente']) ? $_GET['cliente'] : '';

// Construir consulta con filtros
$sql = "SELECT * FROM pedidos WHERE 1=1";
$params = [];
$types = "";

if (!empty($filtro_estado)) {
    $sql .= " AND estado = ?";
    $params[] = $filtro_estado;
    $types .= "s";
}

if (!empty($filtro_cliente)) {
    $sql .= " AND nombre_cliente LIKE ?";
    $params[] = "%$filtro_cliente%";
    $types .= "s";
}

$sql .= " ORDER BY fecha_pedido DESC";

// Preparar y ejecutar consulta
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Pedidos - Pastelería</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-list"></i> Lista de Pedidos</h1>
            <p class="subtitle">Todos los pedidos registrados en el sistema</p>
        </header>

        <div class="card">
            <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Volver al inicio</a>
            
            <!-- Filtros -->
            <div class="filtros">
                <form method="GET" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="estado"><i class="fas fa-filter"></i> Filtrar por estado:</label>
                            <select id="estado" name="estado" onchange="this.form.submit()">
                                <option value="">Todos los estados</option>
                                <option value="recepcionado" <?php echo $filtro_estado == 'recepcionado' ? 'selected' : ''; ?>>Recepcionado</option>
                                <option value="despachado" <?php echo $filtro_estado == 'despachado' ? 'selected' : ''; ?>>Despachado</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="cliente"><i class="fas fa-search"></i> Buscar cliente:</label>
                            <input type="text" id="cliente" name="cliente" 
                                   value="<?php echo htmlspecialchars($filtro_cliente); ?>"
                                   placeholder="Nombre del cliente">
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="listar_pedidos.php" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Tabla de pedidos -->
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Pastel Básico</th>
                            <th>Pastel Mediano</th>
                            <th>Pastel Grande</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            $total_basico = 0;
                            $total_mediano = 0;
                            $total_grande = 0;
                            $total_general = 0;
                            
                            while($row = $result->fetch_assoc()) {
                                $total_pedido = ($row['pastel_basico'] * 8) + ($row['pastel_mediano'] * 12) + ($row['pastel_grande'] * 18);
                                $estado_class = $row['estado'] == 'despachado' ? 'status-despachado' : 'status-recepcionado';
                                
                                $total_basico += $row['pastel_basico'];
                                $total_mediano += $row['pastel_mediano'];
                                $total_grande += $row['pastel_grande'];
                                $total_general += $total_pedido;
                                
                                echo "<tr>";
                                echo "<td>" . $row['id'] . "</td>";
                                echo "<td>" . htmlspecialchars($row['nombre_cliente']) . "</td>";
                                echo "<td class='text-center'>" . $row['pastel_basico'] . "</td>";
                                echo "<td class='text-center'>" . $row['pastel_mediano'] . "</td>";
                                echo "<td class='text-center'>" . $row['pastel_grande'] . "</td>";
                                echo "<td class='text-right'>$" . number_format($total_pedido, 2) . "</td>";
                                echo "<td><span class='status $estado_class'>" . ucfirst($row['estado']) . "</span></td>";
                                echo "<td>" . date('d/m/Y H:i', strtotime($row['fecha_pedido'])) . "</td>";
                                echo "<td class='actions'>";
                                echo "<a href='editar_pedido.php?id=" . $row['id'] . "' class='btn-action edit'><i class='fas fa-edit'></i></a>";
                                echo "<a href='eliminar_pedido.php?id=" . $row['id'] . "' class='btn-action delete' onclick='return confirm(\"¿Estás seguro?\")'><i class='fas fa-trash'></i></a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            
                            // Totales
                            echo "<tfoot>";
                            echo "<tr class='total-row'>";
                            echo "<td colspan='2'><strong>TOTALES:</strong></td>";
                            echo "<td class='text-center'><strong>" . $total_basico . "</strong></td>";
                            echo "<td class='text-center'><strong>" . $total_mediano . "</strong></td>";
                            echo "<td class='text-center'><strong>" . $total_grande . "</strong></td>";
                            echo "<td class='text-right'><strong>$" . number_format($total_general, 2) . "</strong></td>";
                            echo "<td colspan='3'></td>";
                            echo "</tr>";
                            echo "</tfoot>";
                        } else {
                            echo "<tr><td colspan='9' class='text-center'>No se encontraron pedidos</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <div class="export-options">
                <a href="javascript:window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Imprimir Lista
                </a>
            </div>
        </div>
    </div>
    
    <script src="js/scripts.js"></script>
</body>
</html>