@extends(backpack_view('blank'))

@section('header')
<section class="container-fluid">
    <h2>
        <i class="la la-car text-success"></i>
        {{ request('rto_type', 'pending') === 'closed' ? 'Closed RTO' : 'Pending RTO' }}
        <small class="d-none d-md-inline">
            {{ request('rto_type', 'pending') === 'closed' ? '(Processed)' : '(Awaiting Processing)' }}
        </small>
    </h2>
</section>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">

            {{-- HEADER --}}
            <div
                class="card-header bg-gradient-success d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h3 class="card-title mb-0 fw-bold text-black">
                    {{ request('rto_type', 'pending') === 'closed' ? 'Closed' : 'Pending' }} RTO Dashboard
                </h3>

                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-black mb-0 text-nowrap">RTO Status:</label>
                        <select id="rto_type" class="form-control form-select" style="min-width: 180px;">
                            <option value="pending" {{ request('rto_type', 'pending' )==='pending' ? 'selected' : '' }}>
                                Pending RTO
                            </option>
                            <option value="closed" {{ request('rto_type')==='closed' ? 'selected' : '' }}>
                                Closed / Processed RTO
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
                            placeholder="Quick search in all columns...">
                        <button id="resetAll" class="btn btn-outline-danger btn-sm">Reset</button>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button id="exportCsv" class="btn btn-success btn-sm">
                            <i class="la la-file-excel-o"></i> Excel
                        </button>
                        <button id="exportExcel" class="btn btn-danger btn-sm">
                            <i class="la la-file-pdf-o"></i> PDF
                        </button>
                    </div>
                </div>

                {{-- GRID --}}
                <div id="myGrid" class="ag-theme-quartz" style="height: calc(100vh - 260px); width: 100%;"></div>
            </div>

            @if(session('info'))
            <div class="card-footer text-center py-4 text-muted">
                {{ session('info') }}
            </div>
            @endif
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

    const gridOptions = {
        columnDefs,
        rowData: gridConfig.data || [],
        pagination: true,
        paginationPageSize: 50,
        paginationPageSizeSelector: [20, 50, 100, 200],
        animateRows: true,
        defaultColDef: {
            sortable: true,
            filter: true,
            resizable: true,
        },
        components: {
            htmlRenderer: (params) => params.value || '',
        },
    };

    document.addEventListener('DOMContentLoaded', () => {
        const gridDiv = document.querySelector('#myGrid');
        gridApi = agGrid.createGrid(gridDiv, gridOptions);

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

        // Listen to RTO type change → redirect with new param
        document.getElementById('rto_type')?.addEventListener('change', function() {
            const url = new URL(window.location);
            url.searchParams.set('rto_type', this.value);
            // Optional: remove old time filter if you had it
            // url.searchParams.delete('status_filter');
            window.location = url;
        });

        // Excel Export (exclude action column)
        document.getElementById('exportCsv')?.addEventListener('click', () => {
            const rows = [];
            const exportColumns = columnDefs.filter(col => col.field !== 'action');

            gridApi.forEachNodeAfterFilterAndSort(node => {
                const row = {};
                exportColumns.forEach(col => {
                    row[col.headerName] = node.data[col.field] ?? '';
                });
                rows.push(row);
            });

            const worksheet = XLSX.utils.json_to_sheet(rows);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'RTO Report');
            XLSX.writeFile(workbook, 'rto-report-' + new Date().toISOString().slice(0,10) + '.xlsx');
        });

        // PDF Export
        document.getElementById('exportExcel')?.addEventListener('click', () => {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'pt', 'a4');

            const exportColumns = columnDefs
                .filter(col => col.field !== 'action')
                .map(col => ({ header: col.headerName, dataKey: col.field }));

            const rows = [];
            gridApi.forEachNodeAfterFilterAndSort(node => {
                rows.push(node.data);
            });

            doc.text('RTO Report', 40, 30);
            doc.autoTable({
                columns: exportColumns,
                body: rows,
                startY: 50,
                styles: { fontSize: 8 },
                headStyles: { fillColor: [40, 167, 69] },
            });

            doc.save('rto-report.pdf');
        });
    });
</script>
@endpush