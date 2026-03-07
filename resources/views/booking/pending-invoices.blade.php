@extends(backpack_view('blank'))

@section('header')
<section class="container-fluid">
    {{-- <h2>
        <i class="la la-clock text-warning"></i> Pending Invoices
        <small class="d-none d-md-inline">Bookings awaiting invoice generation</small>
    </h2> --}}
</section>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">

            {{-- HEADER --}}
            <div
                class="card-header bg-gradient-warning d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h3 class="card-title mb-0 fw-bold text-black">
                    Pending Invoices Dashboard
                </h3>

                {{-- <div class="d-flex align-items-center gap-2">
                    <label class="text-dark mb-0 text-nowrap">Customer Type:</label>
                    <select id="customer_type_filter" class="form-control form-select" style="min-width: 220px;"
                        name="customer_type">
                        <option value="pending" {{ request('customer_type')==='pending' ? 'selected' : '' }}>Pending
                            Invoices</option>
                        <option value="closed" {{ request('customer_type')==='closed' ? 'selected' : '' }}>Closed
                            Invoices</option>
                    </select>
                </div> --}}
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

                        <button id="btnDefaultHeaders" class="btn btn-secondary btn-sm">
                            Default Headers
                        </button>

                        <div class="position-relative d-inline-block">
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
                                z-index:9999;
                            ">
                                <div class="d-flex justify-content-between align-items-center px-2 py-1 border-bottom">
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

                        <button id="btnAllHeaders" class="btn btn-info btn-sm">
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

<style>
    /* Header center */
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
    const gridConfig = @json($gridConfig ?? []);

    let gridApi;

    const DEFAULT_VISIBLE_FIELDS = [
        'serial_no',
        'booking_no',
        'created_at',
        'name',
        'booking_date',
        'mobile',
        'segment',
        'model',
        'variant',
        'color',
        'booking_amount',
        'action'
    ];

    const columnDefs = (gridConfig.columns || []).map(col => ({
        headerName: col.headerName,
        field: col.field,
        sortable: true,
        filter: true,
        resizable: true,
        pinned: col.pinned || false,
        width: col.width || 150,
        cellRenderer: col.field === 'action'
            ? params => params.value || ''
            : null,
        cellClass: col.cellClass || '',
    }));

    const gridOptions = {
    columnDefs,
    rowData: gridConfig.data || [],
    pagination: true,
    paginationPageSize: 50,
    animateRows: true,
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

        // 🔥 AUTO WIDTH
        setTimeout(() => {
            const allColumnIds = [];
            gridApi.getAllDisplayedColumns().forEach(column => {
                allColumnIds.push(column.getColId());
            });
            gridApi.autoSizeColumns(allColumnIds);
        }, 300);
    }
};


    function openColumnBubble() {
    const bubble = document.getElementById('columnBubble');
    const tbody  = document.getElementById('columnBubbleBody');
    if (!gridApi) return;

    tbody.innerHTML = '';
    const columnState = gridApi.getColumnState();

    gridApi.getColumnDefs().forEach(colDef => {
        if (!colDef.field || colDef.field === 'action') return;

        const state = columnState.find(c => c.colId === colDef.field);
        const isVisible = state ? !state.hide : true;

        const tr = document.createElement('tr');

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.checked = isVisible;

        checkbox.addEventListener('change', () => {
            gridApi.applyColumnState({
                state: [{ colId: colDef.field, hide: !checkbox.checked }],
                applyOrder: false
            });
        });

        tr.innerHTML = `
            <td class="text-center" style="width:30px;"></td>
            <td style="font-size:13px;">${colDef.headerName}</td>
        `;
        tr.children[0].appendChild(checkbox);
        tbody.appendChild(tr);
    });

    bubble.style.display = 'block';
}
    btnCustomiseHeaders.onclick = e => {
    e.stopPropagation();
    openColumnBubble();
};

btnAllHeaders.onclick = () => {
    const allCols = gridApi.getColumnDefs().map(c => c.field);
    gridApi.setColumnsVisible(allCols, true);

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


    document.addEventListener('DOMContentLoaded', () => {
        const gridDiv = document.querySelector('#myGrid');
        gridApi = agGrid.createGrid(gridDiv, gridOptions);

    document.getElementById('closeColumnBubble')
        ?.addEventListener('click', () => {
            document.getElementById('columnBubble').style.display = 'none';
        });

        // Quick Search
        document.getElementById('quickFilter')?.addEventListener('input', e => {
            gridApi.setGridOption('quickFilterText', e.target.value);
        });

        // Reset
        document.getElementById('resetAll')?.addEventListener('click', () => {
            gridApi.setFilterModel(null);
            gridApi.setGridOption('quickFilterText', '');
            document.getElementById('quickFilter').value = '';
        });

        // Excel Export (action exclude)
        document.getElementById('exportCsv')?.addEventListener('click', () => {

    const visibleColumns = gridApi.getAllDisplayedColumns()
        .map(col => col.getColDef())
        .filter(col => col.field && col.field !== 'action');

    const rows = [];
    gridApi.forEachNodeAfterFilterAndSort(node => {
        const row = {};
        visibleColumns.forEach(col => {
            row[col.headerName] = node.data[col.field];
        });
        rows.push(row);
    });

    const worksheet = XLSX.utils.json_to_sheet(rows);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Pending Invoices');
    XLSX.writeFile(workbook, 'pending-invoices.xlsx');
});


        // PDF Export (action exclude)
        document.getElementById('exportExcel')?.addEventListener('click', () => {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'pt', 'a4');

    const visibleColumns = gridApi.getAllDisplayedColumns()
        .map(col => col.getColDef())
        .filter(col => col.field && col.field !== 'action');

    const exportCols = visibleColumns.map(col => ({
        header: col.headerName,
        dataKey: col.field
    }));

    const rows = [];
    gridApi.forEachNodeAfterFilterAndSort(node => {
        const row = {};
        visibleColumns.forEach(col => {
            row[col.field] = node.data[col.field];
        });
        rows.push(row);
    });

    doc.text('Pending Invoices Report', 40, 30);
    doc.autoTable({
        columns: exportCols,
        body: rows,
        startY: 50,
        styles: { fontSize: 8 },
        headStyles: { fillColor: [255, 193, 7] },
    });

    doc.save('pending-invoices.pdf');
});

    });
</script>
@endpush