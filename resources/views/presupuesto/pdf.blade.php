<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Presupuesto - {{ $presupuesto->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
        }
        .header p {
            font-size: 14px;
            margin: 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
        }
        .total {
            margin-top: 20px;
            text-align: right;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Presupuesto #{{ $presupuesto->id }}</h1>
        <p>Fecha: {{ $presupuesto->created_at->format('d/m/Y') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID Producto</th>
                <th>Nombre del Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($presupuesto->items as $item)
                <tr>
                    <td>{{ $item['producto'] }}</td>
                    <td>{{ $item['nombre_producto'] }}</td>
                    <td>{{ $item['cantidad'] }}</td>
                    <td>${{ number_format($item['precio_producto'], 2) }}</td>
                    <td>${{ number_format($item['cantidad'] * $item['precio_producto'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <strong>Total: ${{ number_format($presupuesto->total, 2) }}</strong>
    </div>
</body>
</html>