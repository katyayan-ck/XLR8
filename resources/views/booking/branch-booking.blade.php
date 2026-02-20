@extends(backpack_view('blank'))

@push('after_styles')
<link rel="stylesheet" href="https://unpkg.com/ag-grid-community/styles/ag-theme-quartz.css">

<style>
    body {
        background: #f8f9fa;
    }

    #branchGrid {
        display: block;
    }

    /* Group Header Colors – exact match from your screenshots */
    .group-vehicle-info {
        background-color: #FFCCCB !important;
    }

    /* pink */
    .group-stock {
        background-color: #ADD8E6 !important;
    }

    /* blue */
    .group-selected-branch {
        background-color: #90EE90 !important;
    }

    /* green */
    .group-other {
        background-color: #98FB98 !important;
    }

    /* light green */
    .group-hot-enq {
        background-color: #93DBF7 !important;
    }

    /* light cyan */
    .group-finance {
        background-color: #92D9F5 !important;
    }

    /* cyan */
    .group-exchange {
        background-color: #F0FFF0 !important;
    }

    /* mint */
    .group-global {
        background-color: #FFE4B5 !important;
    }

    /* yellow */
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
        <i class="la la-layer-group text-primary"></i>
        Branch Booking Report
        <small class="d-none d-md-inline">Branch Wise Pending Bookings Dashboard</small>
    </h2>
</section>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">

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
            <div id="branchGrid" class="ag-theme-quartz" style="height: calc(100vh - 260px); width:100%;"></div>
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
        else if (col.headerName === 'STOCK') headerClass = 'group-stock';
        else if (col.headerName === 'SELECTED BRANCH LOCATIONS') headerClass = 'group-selected-branch';
        else if (col.headerName === 'OTHER') headerClass = 'group-other';
        else if (col.headerName === 'HOT ENQ') headerClass = 'group-hot-enq';
        else if (col.headerName === 'INT IN FINANCE') headerClass = 'group-finance';
        else if (col.headerName === 'INT IN EXCH') headerClass = 'group-exchange';
        else if (col.headerName === 'GLOBAL INFO') headerClass = 'group-global';
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
        paginationPageSizeSelector: [10, 20, 50, 100],
        animateRows: true,
        defaultColDef: {
            sortable: true,
            filter: true,
            resizable: true,
        },
        overlayNoRowsTemplate: '<span class="ag-overlay-no-rows-center">No branch booking data available</span>',
    };

    document.addEventListener('DOMContentLoaded', () => {
        const gridDiv = document.querySelector('#branchGrid');
        gridApi = agGrid.createGrid(gridDiv, gridOptions);

        document.getElementById('quickFilter')?.addEventListener('input', e => {
            gridApi.setGridOption('quickFilterText', e.target.value);
        });

        document.getElementById('resetAll')?.addEventListener('click', () => {
            document.getElementById('quickFilter').value = '';
            gridApi.setGridOption('quickFilterText', '');
        });

        // Excel Export
        document.getElementById('exportExcel')?.addEventListener('click', () => {
            const rows = [];
            const exportColumns = columnDefs;

            rows.push(exportColumns.map(col => col.headerName));

            gridApi.forEachNodeAfterFilterAndSort(node => {
                const row = exportColumns.map(col => node.data[col.field] ?? '');
                rows.push(row);
            });

            const worksheet = XLSX.utils.aoa_to_sheet(rows);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Branch Booking');

            const fileName = 'Branch_Booking_Report_' + new Date().toISOString().split('T')[0] + '.xlsx';
            XLSX.writeFile(workbook, fileName);
        });

        // PDF Export
        document.getElementById('exportPdf')?.addEventListener('click', () => {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });

            doc.setFontSize(16);
            doc.text('Branch Booking Report', 40, 40);

            const exportColumns = [];
            columnDefs.forEach(col => {
                if (col.children) {
                    col.children.forEach(c => exportColumns.push({ header: c.headerName, dataKey: c.field }));
                } else {
                    exportColumns.push({ header: col.headerName, dataKey: col.field });
                }
            });

            const body = [];
            gridApi.forEachNodeAfterFilterAndSort(node => {
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
                headStyles: { fillColor: [40, 167, 69], textColor: 255 },
                alternateRowStyles: { fillColor: [245, 245, 245] },
                margin: { top: 60, left: 30, right: 30 },
            });

            doc.save('Branch_Booking_Report.pdf');
        });
    });
</script>
@endpush
