@extends(backpack_view('blank'))

@section('header')
<section class="container-fluid">
    {{-- <h2>
        <i class="la la-money-check text-primary"></i>
        Pending Finance Info (Invoiced Cases)

    </h2> --}}
</section>
@endsection
@push('after_styles')
<link rel="stylesheet" href="https://unpkg.com/ag-grid-community/styles/ag-theme-quartz.css">

<style>
    .ag-theme-quartz .center-header .ag-header-cell-label {
        justify-content: center !important;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">

            {{-- HEADER --}}
            <div
                class="card-header bg-gradient-primary d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h3 class="card-title mb-0 fw-bold text-black text-nowrap">
                    Pending Finance Info (Invoiced Cases)
                </h3>

                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-black mb-0 text-nowrap">Retail Status:</label>
                        <select id="retail_type" class="form-control form-select" style="min-width: 220px;">
                            <option value="pending" {{ request('retail_type', 'pending' )==='pending' ? 'selected' : ''
                                }}>
                                Pending Retail
                            </option>
                            <option value="completed" {{ request('retail_type')==='completed' ? 'selected' : '' }}>
                                Completed / Retailed
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- BODY --}}
            <div class="card-body p-0 bg-light">

                {{-- TOOLBAR --}}
                <div
                    class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3 border-bottom bg-white">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <input type="text" id="quickFilter" class="form-control" style="width: 360px; min-width: 260px;"
                            placeholder="Smart Search...">
                        <button id="resetAll" class="btn btn-outline-danger btn-sm">Reset</button>
                    </div>

                    <div class="d-flex gap-2 flex-wrap justify-content-center">

                        <button id="btnDefaultHeaders" class="btn btn-secondary btn-sm text-nowrap">
                            Default Headers
                        </button>

                        <div class="position-relative">
                            <button id="btnCustomiseHeaders" class="btn btn-success btn-sm text-nowrap">
                                Customise Headers
                            </button>

                            <!-- Customise Bubble -->
                            <div id="columnBubble" style="
                                display:none;
                                position:absolute;
                                top:110%;
                                left:0;
                                width:260px;
                                background:#fff;
                                border:1px solid #ddd;
                                border-radius:6px;
                                box-shadow:0 8px 20px rgba(0,0,0,.15);
                                z-index:9999;
                            ">
                                <div class="d-flex justify-content-between px-2 py-1 border-bottom">
                                    <strong style="font-size:13px;">Customise Headers</strong>
                                    <button id="closeColumnBubble"
                                        class="btn btn-sm btn-link text-danger p-0">✕</button>
                                </div>

                                <div style="max-height:260px; overflow:auto;">
                                    <table class="table table-sm mb-0">
                                        <tbody id="columnBubbleBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <button id="btnAllHeaders" class="btn btn-info btn-sm text-nowrap">
                            All Headers
                        </button>

                    </div>



                    <div class="d-flex gap-2 flex-wrap">
                        <button id="exportCsv" class="btn btn-sm text-nowrap d-flex align-items-center gap-2">
                            <img src="{{ asset('images/export-excel.png') }}" alt="Excel"
                                style="height:30px; width:auto;">
                        </button>

                        <button id="exportExcel" class="btn btn-sm text-nowrap d-flex align-items-center gap-2">
                            <img src="{{ asset('images/export-pdf.png') }}" alt="PDF" style="height:30px; width:auto;">
                        </button>
                    </div>
                </div>

                {{-- GRID --}}
                <div id="myGrid" class="ag-theme-quartz" style="height: calc(110vh - 260px); width: 100%;"></div>
            </div>


        </div>
    </div>
</div>
@endsection

@push('after_styles')
<link rel="stylesheet" href="https://unpkg.com/ag-grid-community/styles/ag-theme-quartz.css">
@endpush

@push('after_scripts')
<script src="https://unpkg.com/ag-grid-community/dist/ag-grid-community.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

<script>
    const gridConfig = @json($gridConfig ?? []);
let gridApi;

/* =========================
   DEFAULT VISIBLE HEADERS
========================= */
const DEFAULT_VISIBLE_FIELDS = [
    'serial_no',
    'booking_no',
    'booking_date',
    'created_at',
    'branch_name',
    'location_name',
    'name',
    'mobile',
    'segment',
    'model',
    'consultant',
    'invoice_no',
    'retail_status',
    'action'
];

/* =========================
   COLUMN DEFINITIONS
========================= */
const columnDefs = (gridConfig.columns || []).map(col => ({
    headerName: col.headerName,
    field: col.field,
    sortable: true,
    filter: true,
    resizable: true,
    pinned: col.pinned || false,
    width: col.width || 150,
    cellRenderer: col.field === 'action' ? params => params.value || '' : null,
    cellClass: col.cellClass || '',
}));

/* =========================
   GRID OPTIONS
========================= */
const gridOptions = {
    columnDefs,
    rowData: gridConfig.data || [],
    pagination: true,
    paginationPageSize: 50,
    rowHeight: 30,
    animateRows: true,
    defaultColDef: {
    sortable: true,
    filter: true,
    resizable: true,
    headerClass: 'center-header',
    cellStyle: { textAlign: 'center' }
},
    onGridReady: params => {
    gridApi = params.api;

    const allCols = gridApi.getColumnDefs().map(c => c.field);
    gridApi.setColumnsVisible(allCols, false);
    gridApi.setColumnsVisible(DEFAULT_VISIBLE_FIELDS, true);

    // 🔥 Auto column width
    setTimeout(() => {
        const allColumnIds = [];
        gridApi.getAllDisplayedColumns().forEach(column => {
            allColumnIds.push(column.getColId());
        });
        gridApi.autoSizeColumns(allColumnIds);
    }, 300);
}
};

/* =========================
   INIT GRID (ONCE)
========================= */
document.addEventListener('DOMContentLoaded', () => {

    const gridDiv = document.querySelector('#myGrid');
    agGrid.createGrid(gridDiv, gridOptions);

    /* ===== Quick Search ===== */
    document.getElementById('quickFilter')?.addEventListener('input', e => {
        gridApi.setGridOption('quickFilterText', e.target.value);
    });

    /* ===== Reset ===== */
    document.getElementById('resetAll')?.addEventListener('click', () => {
        gridApi.setFilterModel(null);
        gridApi.setGridOption('quickFilterText', '');
        document.getElementById('quickFilter').value = '';
    });

    /* ===== Retail Switch ===== */
    document.getElementById('retail_type')?.addEventListener('change', function () {
        const url = new URL(window.location);
        url.searchParams.set('retail_type', this.value);
        window.location = url;
    });

    /* =========================
       HEADER CONTROLS
    ========================= */

    // All Headers
    document.getElementById('btnAllHeaders')?.addEventListener('click', () => {
    const allCols = gridApi.getColumnDefs().map(c => c.field);
    gridApi.setColumnsVisible(allCols, true);

    setTimeout(() => {
        const allColumnIds = [];
        gridApi.getAllDisplayedColumns().forEach(column => {
            allColumnIds.push(column.getColId());
        });
        gridApi.autoSizeColumns(allColumnIds);
    }, 200);
});

    // Default Headers
    document.getElementById('btnDefaultHeaders')?.addEventListener('click', () => {
    const allCols = gridApi.getColumnDefs().map(c => c.field);
    gridApi.setColumnsVisible(allCols, false);
    gridApi.setColumnsVisible(DEFAULT_VISIBLE_FIELDS, true);

    setTimeout(() => {
        const allColumnIds = [];
        gridApi.getAllDisplayedColumns().forEach(column => {
            allColumnIds.push(column.getColId());
        });
        gridApi.autoSizeColumns(allColumnIds);
    }, 200);
});

    // Customise Headers
    function openColumnBubble() {
        const tbody = document.getElementById('columnBubbleBody');
        if (!tbody) return;

        tbody.innerHTML = '';
        const state = gridApi.getColumnState();

        gridApi.getColumnDefs().forEach(col => {
            if (!col.field || col.field === 'action') return;

            const visible = !state.find(s => s.colId === col.field)?.hide;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-center" style="width:30px">
                    <input type="checkbox" ${visible ? 'checked' : ''}>
                </td>
                <td style="font-size:13px">${col.headerName}</td>
            `;

            tr.querySelector('input').addEventListener('change', e => {
                gridApi.applyColumnState({
                    state: [{ colId: col.field, hide: !e.target.checked }],
                    applyOrder: false
                });
            });

            tbody.appendChild(tr);
        });

        document.getElementById('columnBubble').style.display = 'block';
    }

    document.getElementById('btnCustomiseHeaders')?.addEventListener('click', e => {
        e.stopPropagation();
        openColumnBubble();
    });

    document.getElementById('closeColumnBubble')?.addEventListener('click', () => {
        document.getElementById('columnBubble').style.display = 'none';
    });

    document.getElementById('columnBubble')?.addEventListener('click', e => e.stopPropagation());

    document.addEventListener('click', () => {
        const bubble = document.getElementById('columnBubble');
        if (bubble) bubble.style.display = 'none';
    });

    /* =========================
       EXPORT SECTION
    ========================= */

    // Excel (Visible Columns Only)
    document.getElementById('exportCsv')?.addEventListener('click', () => {

        const visibleColumns = gridApi.getAllDisplayedColumns()
            .map(c => c.getColDef())
            .filter(c => c.field && c.field !== 'action');

        const rows = [];
        gridApi.forEachNodeAfterFilterAndSort(node => {
            const row = {};
            visibleColumns.forEach(col => {
                row[col.headerName] = node.data[col.field] ?? '';
            });
            rows.push(row);
        });

        const ws = XLSX.utils.json_to_sheet(rows);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Finance Retail');
        XLSX.writeFile(wb, 'finance-retail-report.xlsx');
    });

    // PDF (Visible Columns Only)
    document.getElementById('exportExcel')?.addEventListener('click', () => {

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'pt', 'a4');

        const visibleColumns = gridApi.getAllDisplayedColumns()
            .map(c => c.getColDef())
            .filter(c => c.field && c.field !== 'action');

        const cols = visibleColumns.map(c => ({
            header: c.headerName,
            dataKey: c.field
        }));

        const rows = [];
        gridApi.forEachNodeAfterFilterAndSort(node => {
            const r = {};
            visibleColumns.forEach(c => r[c.field] = node.data[c.field]);
            rows.push(r);
        });

        doc.text('Finance Retail Report', 40, 30);

        doc.autoTable({
            columns: cols,
            body: rows,
            startY: 50,
            styles: { fontSize: 8 },
            headStyles: { fillColor: [13, 110, 253] }
        });

        doc.save('finance-retail-report.pdf');
    });

});
</script>

@endpush