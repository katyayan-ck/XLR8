@extends(backpack_view('blank'))

@section('header')
<section class="container-fluid">
    {{-- <h2>
        <i class="la la-check-circle text-success"></i> Refunded Bookings
        <small class="d-none d-md-inline">Bookings with Refund Processed</small>
    </h2> --}}
</section>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">

            {{-- HEADER --}}
            <div
                class="card-header bg-gradient-success d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h3 class="card-title mb-0 fw-bold text-black text-nowrap">
                    Refunded Bookings Dashboard
                </h3>
            </div>

            {{-- BODY --}}
            <div class="card-body p-0 bg-light">

                {{-- TOOLBAR --}}
                <div
                    class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3 border-bottom bg-white">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <input type="text" id="quickFilter" class="form-control" style="width: 360px; min-width: 260px;"
                            placeholder="Smart Search...">
                        <button id="resetAll" class="btn btn-outline-danger btn-sm">
                            Reset
                        </button>
                    </div>
                    <div class="d-flex gap-2 flex-wrap justify-content-center">
                        <button id="btnDefaultHeaders" class="btn btn-secondary btn-sm">Default Headers</button>

                        <div class="position-relative">
                            <button id="btnCustomiseHeaders" class="btn btn-success btn-sm">
                                Customise Headers
                            </button>

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
                                z-index:9999;">
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
                        <button id="btnAllHeaders" class="btn btn-info btn-sm">All Headers</button>


                    </div>


                    <div class="d-flex gap-2 flex-wrap">
                        <button id="exportCsv" class="btn btn-sm text-nowrap d-flex align-items-center gap-2">
                            <img src="{{ asset('images/export-excel.png') }}" alt="Excel"
                                style="height:30px; width:auto;">
                        </button>

                        <button id="exportPdf" class="btn btn-sm text-nowrap d-flex align-items-center gap-2">
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
<style>
    .ag-theme-quartz .center-header .ag-header-cell-label {
        justify-content: center !important;
    }
</style>
@endpush

@push('after_scripts')
<script src="https://unpkg.com/ag-grid-community/dist/ag-grid-community.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

<script>
    const gridConfig = @json($gridConfig);
let gridApi;

const DEFAULT_VISIBLE_FIELDS = [
    'serial_no',
    'booking_no',
    'booking_date',
    'cancel_date',
    'refund_date',
    'branch_id',
    'location_id',
    'segment',
    'model',
    'variant',
    'color',
    'seating',
    'name',
    'sales',
    'consultant',
    'action'
];

document.addEventListener('DOMContentLoaded', () => {
    const gridDiv = document.querySelector('#myGrid');

    const columnDefs = gridConfig.columns.map(col => {
    let def = {
        headerName: col.headerName,
        field: col.field,
        sortable: true,
        filter: true,
        resizable: true,
    };

    // Special handling for serial_no and action columns
    if (col.field === 'serial_no') {
        def.pinned = 'left';
        def.width = 90;              // optional – looks better pinned
        def.lockPosition = true;     // prevents user from dragging it away
        def.suppressMovable = true;
        def.cellClass = 'fw-bold bg-light'; // optional visual cue
    }

    if (col.field === 'action') {
        def.pinned = 'right';
        def.width = 120;
        def.lockPosition = true;
        def.suppressMovable = true;
        def.cellRenderer = params => params.value || ''; // safe rendering
        def.cellClass = 'text-center p-0';
    }

    return def;
});

    const gridOptions = {
        columnDefs,
        rowData: gridConfig.data,
        pagination: true,
        paginationPageSize: 50,
        rowHeight: 30,
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

    // 🔥 Auto width adjust
    setTimeout(() => {
        const allColumnIds = [];
        gridApi.getAllDisplayedColumns().forEach(column => {
            allColumnIds.push(column.getColId());
        });
        gridApi.autoSizeColumns(allColumnIds);
    }, 300);
}
    };

    agGrid.createGrid(gridDiv, gridOptions);

    /* ---------------- SEARCH & RESET ---------------- */
    document.getElementById('quickFilter')?.addEventListener('input', e =>
        gridApi.setGridOption('quickFilterText', e.target.value)
    );

    document.getElementById('resetAll')?.addEventListener('click', () => {
        gridApi.setFilterModel(null);
        gridApi.setGridOption('quickFilterText', '');
        quickFilter.value = '';
    });

    /* ---------------- HEADERS ---------------- */
    btnAllHeaders.onclick = () => {
    gridApi.setColumnsVisible(
        gridApi.getColumnDefs().map(c => c.field),
        true
    );

    setTimeout(() => {
        const allColumnIds = [];
        gridApi.getAllDisplayedColumns().forEach(column => {
            allColumnIds.push(column.getColId());
        });
        gridApi.autoSizeColumns(allColumnIds);
    }, 200);
};

    btnDefaultHeaders.onclick = () => {
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
};

    btnCustomiseHeaders.onclick = e => {
        e.stopPropagation();
        columnBubbleBody.innerHTML = '';

        const state = gridApi.getColumnState();
        gridApi.getColumnDefs().forEach(col => {
            if (!col.field || col.field === 'action') return;

            const visible = !state.find(s => s.colId === col.field)?.hide;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-center"><input type="checkbox" ${visible ? 'checked' : ''}></td>
                <td>${col.headerName}</td>
            `;
            tr.querySelector('input').onchange = ev =>
                gridApi.applyColumnState({
                    state: [{ colId: col.field, hide: !ev.target.checked }],
                    applyOrder: false
                });

            columnBubbleBody.appendChild(tr);
        });

        columnBubble.style.display = 'block';
    };

    closeColumnBubble.onclick = () => columnBubble.style.display = 'none';
    document.addEventListener('click', () => columnBubble.style.display = 'none');

    /* ---------------- EXCEL ---------------- */
    exportCsv.onclick = () => {
        const cols = gridApi.getAllDisplayedColumns()
            .map(c => c.getColDef())
            .filter(c => c.field !== 'action');

        const rows = [];
        gridApi.forEachNodeAfterFilterAndSort(n => {
            const r = {};
            cols.forEach(c => r[c.headerName] = n.data[c.field]);
            rows.push(r);
        });

        const ws = XLSX.utils.json_to_sheet(rows);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Report');
        XLSX.writeFile(wb, 'report.xlsx');
    };

    /* ---------------- PDF ---------------- */
    exportPdf.onclick = () => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'pt', 'a4');

        const cols = gridApi.getAllDisplayedColumns()
            .map(c => c.getColDef())
            .filter(c => c.field !== 'action')
            .map(c => ({ header: c.headerName, dataKey: c.field }));

        const rows = [];
        gridApi.forEachNodeAfterFilterAndSort(n => rows.push(n.data));

        doc.text('Report', 40, 30);
        doc.autoTable({ columns: cols, body: rows, startY: 50, styles:{fontSize:8} });
        doc.save('report.pdf');
    };
});
</script>

@endpush