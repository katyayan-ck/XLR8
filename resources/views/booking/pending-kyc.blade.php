@extends(backpack_view('blank'))

@section('header')
<section class="container-fluid">
    <h2>
        <i class="la la-id-card text-warning"></i> Pending KYC
        <small class="d-none d-md-inline">Bookings with incomplete KYC (PAN / Aadhaar missing)</small>
    </h2>
</section>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            {{-- HEADER --}}
            <div class="card-header bg-gradient-warning
                        d-flex justify-content-between align-items-center
                        flex-nowrap flex-md-nowrap flex-wrap">
                <h3 class="card-title mb-0 fw-bold text-black text-nowrap">
                    Pending KYC Dashboard
                </h3>
            </div>

            {{-- BODY --}}
            <div class="card-body p-0" style="background:#f8fafc">
                {{-- TOOLBAR --}}
                <div class="d-flex justify-content-between align-items-center
                            flex-wrap gap-2
                            p-3 border-bottom bg-white">
                    {{-- LEFT CONTROLS --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <input type="text" id="quickFilter" class="form-control w-100 w-md-auto"
                            style="width:360px; min-width:260px;"
                            placeholder="Quick search (name, mobile, booking no...)">
                        <button id="resetAll" class="btn btn-outline-danger btn-sm text-nowrap">
                            <i class="la la-undo"></i> Reset
                        </button>
                    </div>

                    {{-- RIGHT EXPORT BUTTONS --}}
                    <div class="d-flex gap-2 flex-wrap mt-2 mt-md-0">
                        <button id="exportCsv" class="btn btn-success btn-sm text-nowrap w-100 w-md-auto">
                            <i class="la la-file-excel-o"></i> Excel
                        </button>
                        <button id="exportPdf" class="btn btn-danger btn-sm text-nowrap w-100 w-md-auto">
                            <i class="la la-file-pdf-o"></i> PDF
                        </button>
                    </div>
                </div>

                {{-- AG GRID --}}
                <div id="myGrid" class="ag-theme-quartz" style="height: calc(100vh - 260px); width:100%;"></div>
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

        // Excel Export (action exclude)
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
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Pending KYC');
            XLSX.writeFile(workbook, 'pending-kyc-' + new Date().toISOString().slice(0,10) + '.xlsx');
        });

        // PDF Export (action exclude)
        document.getElementById('exportPdf')?.addEventListener('click', () => {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'pt', 'a4');

            const exportColumns = columnDefs
                .filter(col => col.field !== 'action')
                .map(col => ({ header: col.headerName, dataKey: col.field }));

            const rows = [];
            gridApi.forEachNodeAfterFilterAndSort(node => {
                rows.push(node.data);
            });

            doc.text('Pending KYC Report', 40, 30);
            doc.autoTable({
                columns: exportColumns,
                body: rows,
                startY: 50,
                styles: { fontSize: 8, cellPadding: 4 },
                headStyles: { fillColor: [255, 193, 7] },
            });

            doc.save('pending-kyc.pdf');
        });
    });
</script>
@endpush