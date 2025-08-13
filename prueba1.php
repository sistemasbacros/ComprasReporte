<?php
include "config.php";

// 1️⃣ Obtener ítems de la REQUI por DocumentID
if (isset($_GET['DocumentID'])) {
    $docId = intval($_GET['DocumentID']);
    if ($docId > 0) {
        $sql = "SELECT LineNumber, DocumentID, Quantity, ProductID, Description, ProductKey, Marca, Modelo, Medidas 
                FROM docDocumentItem 
                WHERE DocumentID = ? AND DeletedBy = 0";
        $stmt = sqlsrv_query($contpaq, $sql, [$docId]);

        $items = [];
        if ($stmt !== false) {
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $items[] = $row;
            }
            sqlsrv_free_stmt($stmt);
        }
        header('Content-Type: application/json');
        echo json_encode($items);
        exit;
    }
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

// 2️⃣ Obtener datos de la PO con base en IDRqui
if (isset($_GET['IDRqui'])) {
    $idrqui = intval($_GET['IDRqui']);
    if ($idrqui > 0) {
        $sql = "SELECT DocumentID AS DocumentIDPO, BusinessEntityName, DocFolio, DateDocument, Title, SubTotal, TotalTax, Total, 
                       PaymentTermName, CostCenterName, StatusDelivery, CreadoPor, CreadoEl, 
                       [Validación D. Operativa], [Fecha Req Entrega], CONVERT(VARCHAR(16), DateDelivery, 120) AS DateDelivery
                FROM vwLBSDocSupplierPOList1 
                WHERE IDRqui = ?";
        $stmt = sqlsrv_query($contpaq, $sql, [$idrqui]);

        $poItems = [];
        if ($stmt !== false) {
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $poItems[] = $row;
            }
            sqlsrv_free_stmt($stmt);
        }
        header('Content-Type: application/json');
        echo json_encode($poItems);
        exit;
    }
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

// 3️⃣ Obtener ítems de la PO por POID
if (isset($_GET['POID'])) {
    $poId = intval($_GET['POID']);
    if ($poId > 0) {
        $sql = "SELECT LineNumber, DocumentID, Quantity, ProductID, Description, ProductKey, UnitPrice, Total, Unit 
                FROM docDocumentItem 
                WHERE DocumentID = ? AND DeletedBy = 0";
        $stmt = sqlsrv_query($contpaq, $sql, [$poId]);

        $itemsPO = [];
        if ($stmt !== false) {
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $itemsPO[] = $row;
            }
            sqlsrv_free_stmt($stmt);
        }
        header('Content-Type: application/json');
        echo json_encode($itemsPO);
        exit;
    }
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Seguimiento de Requis</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables Bootstrap 5 + Responsive + Botones -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <style>
        tr.selected { background-color: #d1fae5 !important; color: #065f46 !important; font-weight: bold; }
        .dataTables_wrapper .dataTables_filter input { border-radius: 0.375rem; padding: 0.4rem; }
        .dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
            background-color: #2563eb; border-color: #2563eb;
        }
    </style>
</head>
<body class="p-6 bg-gray-100">

<div class="bg-white shadow-lg rounded-xl p-4 border border-gray-200">
    <h2 class="text-2xl font-bold mb-6">📋 Seguimiento de Requis</h2>

    <div class="table-responsive">
        <table id="data-table" class="table table-striped table-hover align-middle nowrap" style="width:100%">
            <thead class="table-dark">
                <tr>
                    <th>Id Requi</th>
                    <th>Fecha Documento</th>
                    <th>Título</th>
                    <th>Centro de Costos</th>
                    <th>Tipo de Requi</th>
                    <th>Responsable</th>
                    <th>Departamento</th>
                    <th>Sub Clasificación</th>
                    <th>Creado por</th>
                    <th>Fecha Creación</th>
                    <th>Fecha Requerida de Entrega</th>
                    <th>Validado Por</th>
                    <th>Validado El</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT
                    DocumentID,
                    CONVERT(varchar(10), DateDocument, 103) AS DateDocumentStr,
                    Title,
                    ISNULL(CostCenterName,'') AS CostCenterName,
                    ISNULL(TipoRequi,'') AS TipoRequi,
                    ISNULL(Responsable,'') AS Responsable,
                    ISNULL(Departamento,'') AS Departamento,
                    ISNULL(SubClasificacion,'') AS SubClasificacion,
                    ISNULL(CreadoPor,'') AS CreadoPor,
                    CONVERT(varchar(10), CreadoEl, 103) AS CreadoElStr,
                    ISNULL(ValidadoPor,'') AS ValidadoPor,
                    CONVERT(varchar(10), [Validado El], 103) AS ValidadoElStr,
                    CONVERT(varchar(10), [Fecha Req Entrega], 103) AS FechaRequerida
                FROM vwLBSDocSupplierRequestOrderList1
                WHERE CreadoEl >= '2025-06-01'  
                  AND CreadoEl < '2025-08-12'
                ORDER BY CreadoEl DESC";

                $stmt = sqlsrv_query($contpaq, $sql);
                if ($stmt !== false) {
                    $found = false;
                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        $found = true;
                        echo "<tr data-hasrequi='" . (!empty($row['TipoRequi']) ? "true" : "false") . "' data-id='{$row['DocumentID']}'>";
                        echo "<td>{$row['DocumentID']}</td>";
                        echo "<td>{$row['DateDocumentStr']}</td>";
                        echo "<td>{$row['Title']}</td>";
                        echo "<td>{$row['CostCenterName']}</td>";
                        echo "<td>{$row['TipoRequi']}</td>";
                        echo "<td>{$row['Responsable']}</td>";
                        echo "<td>{$row['Departamento']}</td>";
                        echo "<td>{$row['SubClasificacion']}</td>";
                        echo "<td>{$row['CreadoPor']}</td>";
                        echo "<td>{$row['CreadoElStr']}</td>";
                        echo "<td>{$row['FechaRequerida']}</td>";
                        echo "<td>{$row['ValidadoPor']}</td>";
                        echo "<td>{$row['ValidadoElStr']}</td>";
                        echo "</tr>";
                    }
                    if (!$found) {
                        echo "<tr><td colspan='13' class='text-center'>No se encontraron registros.</td></tr>";
                    }
                    sqlsrv_free_stmt($stmt);
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div id="requi-info" class="mt-6 hidden p-4 rounded-lg font-semibold text-lg"></div>

<div class="mt-6 p-4 bg-white rounded-lg border border-gray-300 shadow">
    <div class="font-bold mb-2">Proceso asociado al documento seleccionado:</div>
    <div id="process-content" class="text-sm text-gray-700">
        <em>Selecciona una fila para ver el flujo del proceso aquí.</em>
    </div>
</div>

<script>
$(document).ready(function() {
    const table = $('#data-table').DataTable({
        responsive: true,
        language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
        pageLength: 10,
        order: [[0, 'desc']],
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', text: '📋 Copiar' },
            { extend: 'excel', text: '📊 Excel' },
            { extend: 'pdf', text: '📄 PDF' },
            { extend: 'print', text: '🖨️ Imprimir' }
        ]
    });

    const requiInfo = $('#requi-info');
    const processContent = $('#process-content');

    $('#data-table tbody').on('click', 'tr', function() {
        $('#data-table tbody tr').removeClass('selected');
        $(this).addClass('selected');

        const hasRequi = $(this).data('hasrequi');
        const docId = $(this).data('id');

        // Mensaje de validación REQUI
        if (hasRequi) {
            requiInfo.removeClass('hidden bg-red-100 text-red-800')
                     .addClass('bg-green-100 text-green-800')
                     .text(`✅ El documento con ID ${docId} tiene una requisición interna asociada.`)
                     .show();
        } else {
            requiInfo.removeClass('hidden bg-green-100 text-green-800')
                     .addClass('bg-red-100 text-red-800')
                     .text(`⚠️ El documento con ID ${docId} no tiene una requisición interna asociada.`)
                     .show();
        }

        processContent.html(`<div>📌 Items referentes a la REQUI ${docId}...</div>`);

        // 1️⃣ Obtener ítems de la REQUI
        $.ajax({
            url: '',
            method: 'GET',
            data: { DocumentID: docId },
            dataType: 'json',
            success: function(items) {
                if (items.length > 0) {
                    let html = `<h5>📦 Detalle de REQUI</h5>
                        <table class="table table-sm table-bordered mt-2">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>ID Requi</th>
                                    <th>Cantidad</th>
                                    <th>ID Producto</th>
                                    <th>Descripción</th>
                                    <th>Clave Producto</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Unidad</th>
                                </tr>
                            </thead>
                            <tbody>`;
                    items.forEach(item => {
                        html += `<tr>
                            <td>${item.LineNumber}</td>
                            <td>${item.DocumentID}</td>
                            <td>${item.Quantity}</td>
                            <td>${item.ProductID}</td>
                            <td>${item.Description}</td>
                            <td>${item.ProductKey}</td>
                            <td>${item.Marca}</td>
                            <td>${item.Modelo}</td>
                            <td>${item.Medidas}</td>
                        </tr>`;
                    });
                    html += `</tbody></table>`;
                    processContent.html(html);
                } else {
                    processContent.html(`<div>⚠️ No se encontraron ítems para la REQUI ${docId}.</div>`);
                }
            },
            error: function() {
                processContent.html(`<div>❌ Error al obtener los ítems de REQUI.</div>`);
            }
        });

        // 2️⃣ Obtener datos de TODAS las PO usando el IDRqui (docId)
        $.ajax({
            url: '',
            method: 'GET',
            data: { IDRqui: docId },
            dataType: 'json',
            success: function(poItems) {
                if (poItems.length > 0) {
                    let poHtml = `<h5 class="mt-4">📄 Información de las PO asociadas</h5>
                        <table class="table table-sm table-bordered mt-2">
                            <thead>
                                <tr>
                                    <th>DocumentID</th>
                                    <th>BusinessEntityName</th>
                                    <th>DocFolio</th>
                                    <th>DateDocument</th>
                                    <th>Título</th>
                                    <th>SubTotal</th>
                                    <th>TotalTax</th>
                                    <th>Total</th>
                                    <th>PaymentTermName</th>
                                    <th>CostCenterName</th>
                                    <th>StatusDelivery</th>
                                    <th>CreadoPor</th>
                                    <th>CreadoEl</th>
                                    <th>Validación D. Operativa</th>
                                    <th>Fecha Req Entrega</th>
                                    <th>DateDelivery</th>
                                </tr>
                            </thead>
                            <tbody>`;
                    poItems.forEach(poItem => {
                        poHtml += `<tr>
                            <td>${poItem.DocumentIDPO}</td>
                            <td>${poItem.BusinessEntityName}</td>
                            <td>${poItem.DocFolio}</td>
                            <td>${poItem.DateDocument}</td>
                            <td>${poItem.Title}</td>
                            <td>${poItem.SubTotal}</td>
                            <td>${poItem.TotalTax}</td>
                            <td>${poItem.Total}</td>
                            <td>${poItem.PaymentTermName}</td>
                            <td>${poItem.CostCenterName}</td>
                            <td>${poItem.StatusDelivery}</td>
                            <td>${poItem.CreadoPor}</td>
                            <td>${poItem.CreadoEl}</td>
                            <td>${poItem['Validación D. Operativa']}</td>
                            <td>${poItem['Fecha Req Entrega']}</td>
                            <td>${poItem.DateDelivery}</td>
                        </tr>`;
                    });
                    poHtml += `</tbody></table>`;
                    processContent.append(poHtml);

                    // 3️⃣ Recorrer todas las PO y traer su detalle
                    poItems.forEach(po => {
                        $.ajax({
                            url: '',
                            method: 'GET',
                            data: { POID: po.DocumentIDPO },
                            dataType: 'json',
                            success: function(itemsPO) {
                                if (itemsPO.length > 0) {
                                    let htmlPOItems = `<h5 class="mt-4">📦 Detalle de la PO ${po.DocumentIDPO}</h5>
                                        <table class="table table-sm table-bordered mt-2">
                                            <thead>
                                                <tr>
                                                    <th>Número de Línea</th>
                                                    <th>DocumentID</th>
                                                    <th>Cantidad</th>
                                                    <th>ProductID</th>
                                                    <th>Descripción</th>
                                                    <th>Clave Producto</th>
                                                    <th>Precio Unitario</th>
                                                    <th>Total</th>
                                                    <th>Unidad</th>
                                                </tr>
                                            </thead>
                                            <tbody>`;
                                    itemsPO.forEach(it => {
                                        htmlPOItems += `<tr>
                                            <td>${it.LineNumber}</td>
                                            <td>${it.DocumentID}</td>
                                            <td>${it.Quantity}</td>
                                            <td>${it.ProductID}</td>
                                            <td>${it.Description}</td>
                                            <td>${it.ProductKey}</td>
                                            <td>${it.UnitPrice}</td>
                                            <td>${it.Total}</td>
                                            <td>${it.Unit}</td>
                                        </tr>`;
                                    });
                                    htmlPOItems += `</tbody></table>`;
                                    processContent.append(htmlPOItems);
                                } else {
                                    processContent.append(`<div>⚠️ No se encontraron ítems para la PO ${po.DocumentIDPO}.</div>`);
                                }
                            },
                            error: function() {
                                processContent.append(`<div>❌ Error al obtener los ítems de la PO ${po.DocumentIDPO}.</div>`);
                            }
                        });
                    });

                } else {
                    processContent.append(`<div>⚠️ No se encontró PO asociada a la requisición.</div>`);
                }
            },
            error: function() {
                processContent.append(`<div>❌ Error al obtener datos de la PO.</div>`);
            }
        });

    });
});
</script>

</body>
</html>
