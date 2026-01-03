// Funciones de utilidad para la aplicación

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips para iconos
    const tooltips = document.querySelectorAll('[title]');
    tooltips.forEach(el => {
        el.addEventListener('mouseenter', showTooltip);
        el.addEventListener('mouseleave', hideTooltip);
    });
    
    // Validación de formularios
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', validateForm);
    });
    
    // Mostrar confirmación para eliminaciones
    const deleteLinks = document.querySelectorAll('a.delete');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('¿Estás seguro de que deseas eliminar este pedido?')) {
                e.preventDefault();
            }
        });
    });
});

function showTooltip(e) {
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = e.target.getAttribute('title');
    tooltip.style.position = 'absolute';
    tooltip.style.background = '#333';
    tooltip.style.color = 'white';
    tooltip.style.padding = '5px 10px';
    tooltip.style.borderRadius = '4px';
    tooltip.style.zIndex = '1000';
    
    document.body.appendChild(tooltip);
    
    const rect = e.target.getBoundingClientRect();
    tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
    tooltip.style.left = (rect.left + rect.width/2 - tooltip.offsetWidth/2) + 'px';
}

function hideTooltip() {
    const tooltip = document.querySelector('.tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

function validateForm(e) {
    const form = e.target;
    const requiredFields = form.querySelectorAll('[required]');
    
    for (let field of requiredFields) {
        if (!field.value.trim()) {
            alert('Por favor, completa todos los campos obligatorios.');
            field.focus();
            e.preventDefault();
            return false;
        }
    }
    
    return true;
}

// Función para formatear números como moneda
function formatCurrency(amount) {
    return '$' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// Función para exportar datos a CSV
function exportToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    let csv = [];
    
    // Obtener encabezados
    const headers = [];
    table.querySelectorAll('th').forEach(th => {
        headers.push(th.innerText);
    });
    csv.push(headers.join(','));
    
    // Obtener filas de datos
    table.querySelectorAll('tbody tr').forEach(row => {
        const rowData = [];
        row.querySelectorAll('td').forEach(td => {
            // Eliminar etiquetas HTML y espacios extra
            let text = td.innerText.replace(/\n/g, ' ').trim();
            // Escapar comillas
            text = text.replace(/"/g, '""');
            // Si contiene comas, envolver en comillas
            if (text.includes(',')) {
                text = '"' + text + '"';
            }
            rowData.push(text);
        });
        csv.push(rowData.join(','));
    });
    
    // Crear y descargar archivo
    const csvContent = "data:text/csv;charset=utf-8," + csv.join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", filename + ".csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}