@extends(backpack_view('blank'))

@push('after_styles')
<link rel="stylesheet" href="https://unpkg.com/ag-grid-community/styles/ag-theme-quartz.css">

<style>
    body {
        background: #f8f9fa;
    }

    #myGrid {
        display: block;
    }

    /* Group Header Colors – exact match from photo */
    .group-vehicle-info {
        background-color: #FFCCCB !important;
    }

    /* pink */
    .group-booking {
        background-color: #A6F1A6 !important;
    }

    /* light green */
    .group-pending {
        background-color: #DDA0DD !important;
    }

    /* lavender/purple */

    .ag-header-cell-label {
        font-weight: bold !important;
        font-size: 13px;
        justify-content: center !important;
    }

    .ag-cell {
        font-size: 13px !important;
        padding: 6px 8px;
    }

    .text-right {
        text-align: right !important;
    }
</style>
@endpush

@section('header')
<section class="container-fluid">
    <h2>
        <i class="la la-exclamation-triangle text-warning"></i>
        Pending Actions Report
        <small class="d-none d-md-inline">Bookings Pending Tasks</small>
    </h2>
</section>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">

            {{-- HEADER --}}


            {{-- TOOLBAR --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3 border-bottom bg-white">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <input type="text" id="quickFilter" class="form-control" style="width:360px; min-width:260px;"
                        placeholder="Quick search...">
                    <button id="resetAll" class="btn btn-outline-danger btn-sm">Reset</button>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button id="exportExcel" class="btn btn-success btn-sm">
                        <i class="la la-file-excel-o"></i> Excel
                    </button>
                    <button id="exportPdf" class="btn btn-danger btn-sm">
                        <i class="la la-file-pdf-o"></i> PDF
                    </button>
                </div>
            </div>

            {{-- GRID --}}
            <div id="myGrid" class="ag-theme-quartz" style="height: calc(100vh - 220px); width:100%;"></div>
        </div>

        @if(session('info'))
        <div class="alert alert-info mt-3">
            {{ session('info') }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('after_scripts')
<script src="https://unpkg.com/ag-grid-community/dist/ag-grid-community.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

<script>
    const gridConfig = @json($gridConfig ?? []);

    let gridApi;

    const columnDefs = (gridConfig.columns || []).map(col => {
        let headerClass = '';
        if (col.headerName === 'Vehicle Info') headerClass = 'group-vehicle-info';
        else if (col.headerName === 'Bookings') headerClass = 'group-booking';
        else if (col.headerName === 'PENDING ACTIONS') headerClass = 'group-pending';

        return {
            headerName: col.headerName,
            field: col.field,
            children: col.children ? col.children.map(child => ({
                ...child,
                cellClass: child.cellClass || 'text-right'
            })) : null,
            headerClass: headerClass || '',
            sortable: true,
            filter: true,
            resizable: true,
            width: col.width || 150,
        };
    });

    const gridOptions = {
        columnDefs,
        rowData: gridConfig.data || [],
        pagination: true,
        paginationPageSize: 20,
        rowHeight:30,
        paginationPageSizeSelector: [10, 20, 50, 100],
        animateRows: true,
        defaultColDef: {
            sortable: true,
            filter: true,
            resizable: true,
        },
        overlayNoRowsTemplate: '<span class="ag-overlay-no-rows-center">No pending actions data available</span>',
    };

    document.addEventListener('DOMContentLoaded', () => {
        const gridDiv = document.querySelector('#myGrid');
        gridApi = agGrid.createGrid(gridDiv, gridOptions);

        // Quick Filter
        document.getElementById('quickFilter')?.addEventListener('input', e => {
            gridApi.setGridOption('quickFilterText', e.target.value);
        });

        // Reset – full reset with filter model clear
        document.getElementById('resetAll')?.addEventListener('click', () => {
            document.getElementById('quickFilter').value = '';
            gridApi.setGridOption('quickFilterText', '');
            gridApi.setFilterModel(null); // ← Yeh important line add ki – ab reset perfect kaam karega
        });

        // Excel Export – Full data (no filter applied)
        document.getElementById('exportExcel')?.addEventListener('click', () => {
    // Step 1: Collect leaf columns in visual left-to-right order
    const headers = [];
    const fields = [];

    function collectLeaves(cols) {
        cols.forEach(col => {
            if (col.children) {
                collectLeaves(col.children);
            } else if (col.field) { // only real data columns
                headers.push(col.headerName);
                fields.push(col.field);
            }
        });
    }

    collectLeaves(columnDefs);

    // Step 2: Build rows – ALL data (ignoring filters/sorts as you want full export)
    const rows = [headers]; // first row = headers

    gridApi.forEachNode(node => {
        if (node.group) return; // skip group rows if any row-grouping is active

        const row = fields.map(field => node.data?.[field] ?? '');
        rows.push(row);
    });

    // Step 3: Create sheet from array of arrays (aoa)
    const worksheet = XLSX.utils.aoa_to_sheet(rows);

    // Optional: better column widths
    const colWidths = headers.map((header, i) => {
        let maxWidth = header.length;
        rows.slice(1).forEach(r => {
            const val = String(r[i] || '');
            if (val.length > maxWidth) maxWidth = val.length;
        });
        return { wch: Math.min(Math.max(maxWidth + 3, 8), 70) };
    });
    worksheet['!cols'] = colWidths;

    // Step 4: Export
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Pending Actions');
    const today = new Date().toISOString().split('T')[0];
    XLSX.writeFile(workbook, `Pending_Actions_Report_${today}.xlsx`);
});

        // PDF Export – same as before
        document.getElementById('exportPdf')?.addEventListener('click', () => {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });

            doc.setFontSize(16);
            doc.text('Pending Actions Report', 40, 40);

            const exportColumns = [];
            columnDefs.forEach(col => {
                if (col.children) {
                    col.children.forEach(c => exportColumns.push({ header: c.headerName, dataKey: c.field }));
                } else {
                    exportColumns.push({ header: col.headerName, dataKey: col.field });
                }
            });

            const body = [];
            gridApi.forEachNode(node => {  // Full data for PDF too
                const row = {};
                exportColumns.forEach(c => {
                    row[c.dataKey] = node.data[c.dataKey] ?? '';
                });
                body.push(row);
            });

            doc.autoTable({
                columns: exportColumns,
                body: body,
                startY: 60,
                styles: { fontSize: 8, cellPadding: 4, overflow: 'linebreak' },
                headStyles: { fillColor: [255, 255, 0] },
                alternateRowStyles: { fillColor: [245, 245, 245] },
                margin: { top: 60, left: 30, right: 30 },
            });

            doc.save('Pending_Actions_Report.pdf');
        });
    });
</script>
@endpush
