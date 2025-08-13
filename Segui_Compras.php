<?php
include "config.php";


?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Proceso de Selección con Tailwind + DataTables</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- DataTables Tailwind CSS -->
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css" rel="stylesheet" />
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

  <style>
    tr.selected {
      background-color: #d1fae5 !important;
      color: #065f46 !important;
      font-weight: bold;
    }
    .highlight-id {
      @apply text-blue-600 underline cursor-pointer;
    }
  </style>
</head>
<body class="p-6 bg-gray-100">

  <h2 class="text-2xl font-bold mb-6">Proceso de Selección de Filas</h2>

  <div class="overflow-x-auto">
    <table id="data-table" class="stripe hover w-full text-sm">
      <thead class="bg-gray-800 text-white">
        <tr>
          <th>Área</th>
          <th>Fecha</th>
          <th>Título</th>
          <th>ID Documento</th>
          <th>Documento</th>
          <th>Total</th>
          <th>Centro de Costo</th>
          <th>Costo</th>
          <th>¿Tiene Requi?</th>
        </tr>
      </thead>
      <tbody>
        <tr data-hasrequi="true" data-id="359016">
          <td>ABASTECEDORA</td>
          <td>04-abr-25</td>
          <td>AUT-EXT-SEDESA-BACROS-H. GREC...</td>
          <td><span class="highlight-id">359016</span></td>
          <td>5027108</td>
          <td>7,887.15</td>
          <td>2485_CNG_IMSS_MICH_CS0029</td>
          <td>12,053.47</td>
          <td class="text-green-600 font-bold text-center">✔</td>
        </tr>
        <tr data-hasrequi="false" data-id="334456">
          <td>ABASTECEDORA</td>
          <td>04-abr-25</td>
          <td>AUT-INT-ADMIN-999-012 SENADO</td>
          <td><span class="highlight-id">334456</span></td>
          <td>5027108</td>
          <td>0</td>
          <td>0</td>
          <td>1,051.80</td>
          <td class="text-red-600 font-bold text-center">✘</td>
        </tr>
        <tr data-hasrequi="true" data-id="321176">
          <td>PROJECTS SEINCO</td>
          <td>24-ene-25</td>
          <td>AUT-EXTERNO IMSS MICHOACAN-CSS MI...</td>
          <td><span class="highlight-id">321176</span></td>
          <td>5027108</td>
          <td>92,698.28</td>
          <td>2258_SNC_IMSS_MICH_C44077</td>
          <td>65,344.00</td>
          <td class="text-green-600 font-bold text-center">✔</td>
        </tr>
        <tr data-hasrequi="false" data-id="311154">
          <td>ABASTECEDORA</td>
          <td>21-ene-25</td>
          <td>PAPELERÍA</td>
          <td><span class="highlight-id">311154</span></td>
          <td>5026914</td>
          <td>6,049.27</td>
          <td>2995 Gerencia Administrativa</td>
          <td>41.83</td>
          <td class="text-red-600 font-bold text-center">✘</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Info Box -->
  <div id="requi-info" class="mt-6 hidden p-4 rounded-lg font-semibold text-lg"></div>

  <!-- Proceso asociado -->
  <div class="mt-6 p-4 bg-white rounded-lg border border-gray-300 shadow">
    <div class="font-bold mb-2">Proceso asociado al documento seleccionado:</div>
    <div id="process-content" class="text-sm text-gray-700">
      <em>Selecciona una fila para ver el flujo del proceso aquí.</em>
    </div>
  </div>

  <script>
    $(document).ready(function () {
      // Inicializar DataTable con estilo Tailwind
      const table = $('#data-table').DataTable({
        language: {
          url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
      });

      const requiInfo = $('#requi-info');
      const processContent = $('#process-content');

      const processes = {
        '359016': `
          <div class="mb-1">Requisiciones Internas ID 332627</div>
          <div class="text-blue-600 font-bold">&rarr;</div>
          <div class="mb-1">Órdenes de Compra ID 332676</div>
          <div class="text-blue-600 font-bold">&rarr;</div>
          <div class="mb-1">Recepciones de Compra ID 334342</div>
          <div class="text-blue-600 font-bold">&rarr;</div>
          <div class="text-blue-700 font-bold">Salidas ID 359016</div>
        `,
        '334456': `<div>No existe proceso definido para este documento.</div>`,
        '321176': `
          <div class="mb-1">Requisiciones Internas ID 321176</div>
          <div class="text-blue-600 font-bold">&rarr;</div>
          <div class="mb-1">Órdenes de Compra ID 321180</div>
          <div class="text-blue-600 font-bold">&rarr;</div>
          <div class="mb-1">Recepciones de Compra ID 321190</div>
          <div class="text-blue-600 font-bold">&rarr;</div>
          <div class="text-blue-700 font-bold">Salidas ID 321176</div>
        `,
        '311154': `<div>Este documento no tiene proceso relacionado.</div>`
      };

      $('#data-table tbody').on('click', 'tr', function () {
        $('#data-table tbody tr').removeClass('selected');
        $(this).addClass('selected');

        const hasRequi = $(this).data('hasrequi');
        const docId = $(this).data('id');

        if (hasRequi) {
          requiInfo
            .removeClass('hidden bg-red-100 text-red-800')
            .addClass('bg-green-100 text-green-800')
            .text(`El documento con ID ${docId} tiene una requisición interna asociada.`)
            .show();
        } else {
          requiInfo
            .removeClass('hidden bg-green-100 text-green-800')
            .addClass('bg-red-100 text-red-800')
            .text(`El documento con ID ${docId} no tiene una requisición interna asociada.`)
            .show();
        }

        processContent.html(processes[docId] || '<div>Flujo de proceso no disponible para este documento.</div>');
      });
    });
  </script>
</body>
</html>
