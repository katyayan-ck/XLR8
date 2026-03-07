@extends(backpack_view('blank'))

@section('header')
<section class="container-fluid">
    {{-- <h2>
        <i class="la la-exchange text-primary"></i> Int in Exchange
        <small class="d-none d-md-inline">Bookings Interested in Exchange</small>
    </h2> --}}
</section>

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

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">

            {{-- HEADER --}}
            <div class="card-header bg-gradient-primary
                        d-flex justify-content-between align-items-center
                        flex-nowrap flex-md-nowrap flex-wrap">
                <h3 class="card-title mb-0 fw-bold text-black text-nowrap">
                    Int in Exchange Dashboard
                </h3>

                {{-- <div class="d-flex align-items-center gap-2
                            flex-nowrap flex-md-nowrap flex-wrap
                            mt-2 mt-md-0">
                    <label class="text-white mb-0 text-nowrap">Status:</label>
                    <select id="status_filter" class="form-control w-100 w-md-auto" style="min-width:200px;">
                        <option value="" {{ request('status_filter')=='' ? 'selected' : '' }}>All</option>
                        <option value="some_status" {{ request('status_filter')=='some_status' ? 'selected' : '' }}>
                            Filter 1</option>
                        <option value="all">All</option>
                    </select>
                </div> --}}
            </div>

            {{-- BODY --}}
            <div class="card-body p-0" style="background:#f8fafc">

                {{-- TOOLBAR --}}
                <div class="d-flex justify-content-between align-items-center
                            flex-wrap gap-2
                            p-3 border-bottom bg-white">

                    {{-- LEFT --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <input type="text" id="quickFilter" class="form-control" style="width:340px;"
                            placeholder="Smart Search...">
                        <button id="resetAll" class="btn btn-sm btn-outline-danger">Reset</button>
                    </div>

                    <div class="d-flex gap-2 flex-wrap justify-content-center">

                        <button id="btnDefaultHeaders" class="btn btn-secondary btn-sm">
                            Default Headers
                        </button>

                        <div class="position-relative">
                            <button id="btnCustomiseHeaders" class="btn btn-success btn-sm">
                                Customise Headers
                            </button>

                            <!-- Bubble -->
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

                        <button id="btnAllHeaders" class="btn btn-info btn-sm">
                            All Headers
                        </button>

                    </div>




                    {{-- RIGHT --}}
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
                <div id="myGrid" class="ag-theme-quartz" style="height: calc(110vh - 260px); width:100%;"></div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('after_scripts')
<script src="https://unpkg.com/ag-grid-community/dist/ag-grid-community.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const gridDiv = document.querySelector('#myGrid');
        if (!gridDiv) return;

        const gridConfig = @json($gridConfig ?? []);
        let gridApi;

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
            'exchange_value',
            'action'
        ];

        const columnDefs = (gridConfig.columns || []).map(col => {
            // S.No. aur Action ko identify karo
            const isSnoColumn   = col.field === 'serial_no' || col.headerName?.toLowerCase().includes('s.no') || col.headerName?.toLowerCase().includes('serial');
            const isActionColumn = col.field === 'action' || col.headerName?.toLowerCase().includes('action');

            const columnDef = {
                headerName: col.headerName,
                field: col.field,
                sortable: true,
                filter: true,
                resizable: true,
                cellRenderer: col.field === 'action' ? params => params.value || '' : null,

                // 🔥 Yeh important line – pinned logic
                pinned: col.pinned || (isSnoColumn || isActionColumn ? 'left' : false),

                // S.No. ko thoda bold aur chhota rakho
                cellClass: isSnoColumn ? 'text-center fw-bold' : 'text-center',
                width: isSnoColumn ? 70 : (isActionColumn ? 120 : 140),
            };

            // Agar column group hai (children hain) to unko bhi handle karo
            if (col.children) {
                columnDef.children = col.children.map(child => ({
                    headerName: child.headerName,
                    field: child.field,
                    width: child.width || 110,
                    sortable: true,
                    filter: true,
                    resizable: true,
                    cellClass: 'text-center',
                }));
            }

            return columnDef;
        });

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
                cellStyle: { textAlign: 'center' },
                minWidth: 100,
            },

            onGridReady: params => {
                gridApi = params.api;

                // Default visible columns set karo
                const allCols = gridApi.getColumnDefs().map(c => c.field);
                gridApi.setColumnsVisible(allCols, false);
                gridApi.setColumnsVisible(DEFAULT_VISIBLE_FIELDS, true);

                // 🔥 Auto-size columns
                setTimeout(() => {
                    const allColumnIds = [];
                    gridApi.getAllDisplayedColumns().forEach(column => {
                        allColumnIds.push(column.getColId());
                    });
                    gridApi.autoSizeColumns(allColumnIds);
                }, 300);
            }
        };

        gridApi = agGrid.createGrid(gridDiv, gridOptions);

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

    function openColumnBubble() {
    const tbody = document.getElementById('columnBubbleBody');
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

document.getElementById('btnCustomiseHeaders')
    ?.addEventListener('click', e => {
        e.stopPropagation();
        openColumnBubble();
    });

document.getElementById('closeColumnBubble')
    ?.addEventListener('click', () => {
        document.getElementById('columnBubble').style.display = 'none';
    });

document.getElementById('columnBubble')
    ?.addEventListener('click', e => e.stopPropagation());

document.addEventListener('click', () => {
    document.getElementById('columnBubble').style.display = 'none';
});





    // Quick Filter
    document.getElementById('quickFilter')?.addEventListener('input', e => {
        gridApi.setGridOption('quickFilterText', e.target.value);
    });

    // Reset
    document.getElementById('resetAll')?.addEventListener('click', () => {
        gridApi.setFilterModel(null);
        gridApi.setGridOption('quickFilterText', '');
        document.getElementById('quickFilter').value = '';
    });

    // Status filter (customize values as needed)
    document.getElementById('status_filter')?.addEventListener('change', e => {
        const value = e.target.value;
        window.location.href = `{{ route('booking.exchange') }}?status_filter=${value}`;
    });

    // Export Excel
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
    XLSX.utils.book_append_sheet(wb, ws, 'Int in Exchange');
    XLSX.writeFile(wb, 'int-in-exchange.xlsx');
});


    // Export PDF
    document.getElementById('exportPdf')?.addEventListener('click', () => {
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

    doc.text('Int in Exchange Report', 40, 30);
    doc.autoTable({
        columns: cols,
        body: rows,
        startY: 50,
        styles: { fontSize: 8 }
    });

    doc.save('int-in-exchange.pdf');
});

});
</script>
@endpush