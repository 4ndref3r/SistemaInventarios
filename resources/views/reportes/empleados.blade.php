<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de pagos</title>
    <style>
        @page {
            size: letter;
            margin-top: 0.5cm;
            margin-left: 1.5cm;
            margin-right: 1.5cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            position: relative;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-left {
            width: 30%;
            font-size: 10px;
            text-align: left;
        }

        .header-center {
            width: 50%;
            text-align: center;
            vertical-align: middle;
        }

        .header-right {
            width: 30%;
            font-size: 10px;
            text-align: right;
            vertical-align: top;
        }

        .header-center h3 {
            margin: 0;
            font-size: 16px;
            justify-content: center;
        }

        #total {
            text-align: right;
            font-size: 12px;
        }

        /* Imagen del logo */
        .logo {
            width: 4cm;
        }

        /* Contenido de la planilla */
        .content {
            margin-top: 20px;
        }

        /* Tabla de pagos */
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: hsl(0, 0%, 99%); /* Color celeste */
        }

        .payment-table th, .payment-table td {
            border: 1px solid #cac6c6;
            padding: 3px;
            text-align: center;
        }

        .payment-table th {
            background-color: #7b1bbb; /* Color de encabezado */
            color: white;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 60px;
        }

        .signature-section div {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-section p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <table class="header-table">
            <tr>
                <td class="header-left">
                    <p>
                        COCHABAMBA - BOLIVIA<br>
                        C. CARITAS ENTRE AV. BLANCO GALINDO Y C. GABRIEL GARCIA MARQUEZ<br>
                        4379951 - 68580337
                    </p>
                </td>

                <td>
                </td>

                <td class="header-right">
                    <img class="logo" src="{{ public_path('images/logo_promaq.png') }}" alt="Logo"><br>
                    <div class="datos-right">
                        <p>
                          <strong>Fecha:</strong> {{ now()->format('d-m-Y H:i:s') }}<br>
                        </p>
                    </div>
                </td>
            </tr>
            <tr><td class="header-center" colspan="3">
                <h2>REPORTE DE EMPLEADOS</h2>
                <h4 style="margin-top: 2px;">
                  {{ isset($fecha_inicio) ? 'Del ' . $fecha_inicio .' al ' : ''}}
                  {{ $fecha_fin }}
                </h4>
            </td></tr>
        </table>

        <br>
        <div class="content">
            <table style="width: 100%">
                <tr>
                  @if ($employee)
                    <td style="width: 50%;"><strong>Empleado: </strong> {{$employee->nombres.' '.$employee->primer_apellido.' '.$employee->segundo_apellido}}</td>
                  @endif
                  @if ($tipo)
                    <td style="width: 50%;"><strong>Tipo: </strong> {{$tipo}}</td>
                  @endif
                </tr>
            </table>
            <table class="payment-table">
                <thead>
                    <tr>
                        <th>Nro</th>
                        <th>Fecha</th>
                        <th>Empleado</th>
                        <th>Tipo</th>
                        <th>Materiales</th>
                        <th>Cantidad</th>
                        <th>Unidad</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $sum = 0;
                    @endphp
                  @foreach($data as $item)
                  <tr>
                      <td style="width: 5%;">{{$loop->iteration}}</td>
                      <td style="width: 10%;">{{ $item->fecha ?? 'N/A' }}</td>
                      <td style="width: 20%;">{{ $item->employee->nombres.' '.$item->employee->primer_apellido ?? 'N/A' }}</td>
                      <td style="width: 15%;">{{ $item->tipo ?? 'N/A' }}</td>
                      <td style="width: 30%;">{{ $item->material_nombre ?? 'N/A' }}</td>
                      <td style="width: 10%;">{{ $item->cantidad ?? 0 }}</td>
                      <td style="width: 10%;">{{ $item->abreviatura ?? 0 }}</td>
                      {{$sum+=$item->cantidad}}
                  </tr>
                  @endforeach
                </tbody>
                <tfoot style="border: none;">
                  <tr>
                    <td colspan="4" style="border: none;"></td>
                    <td style="border: none; text-align: right;"><strong>Total Bs.</strong></td>
                    <td style="border: none; text-align: center;">{{ number_format($sum, 2) }}</td>
                    <td style="border: none;"></td>
                  </tr>
                </tfoot>
            </table>
        </div>

        <div class="signature-section">
          <div>
              <p>______________________________</p>
              <p>REPRESENTANTE</p>
          </div>
          <div>
              <p>______________________________</p>
              <p>CONTADORA</p>
          </div>
      </div>
    </div>
</body>
</html>