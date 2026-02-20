{{-- resources/views/admin/booking/list.blade.php --}}
@extends(backpack_view('blank'))

@section('header')
<section class="container-fluid">
    <h2>
        <i class="la la-book text-primary"></i> All Live Bookings
        {{-- <small class="d-none d-md-inline">({{ $bookings->count() }} records)</small> --}}
    </h2>
</section>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            {{-- HEADER --}}
            {{-- HEADER --}}
            <div class="card-header bg-gradient-primary
                        d-flex justify-content-between align-items-center
                        flex-nowrap flex-md-nowrap flex-wrap gap-3">

                <h3 class="card-title mb-0 fw-bold text-black text-nowrap">
                    <i class="la la-book me-2"></i>
                    {{ $title ?? 'All Live Bookings' }}
                    {{-- <small class="d-none d-md-inline ms-2">({{ $bookings->count() }} records)</small> --}}
                </h3>

                <!-- Right side group: button LEFT → dropdown RIGHT -->
                <div class="d-flex align-items-center gap-3 flex-nowrap">

                    <!-- 1. Add New Booking button (left position) -->
                    <a href="{{ backpack_url('booking/create') }}" class="btn btn-light btn-sm fw-bold shadow-sm">
                        <i class="la la-plus me-1"></i> Add New Booking
                    </a>

                    <!-- 2. Status dropdown (right of button) -->
                    <select id="statusFilter" class="form-select form-select-sm bg-white text-dark border-0 shadow-sm"
                        style="min-width: 200px; max-width: 260px;">
                        <option value="{{ backpack_url('booking') }}" {{ Route::currentRouteName()==='booking.index'
                            ? 'selected' : '' }}>
                            All Live Bookings
                        </option>
                        <option value="{{ backpack_url('booking/hold') }}" {{ Route::currentRouteName()==='booking.hold'
                            ? 'selected' : '' }}>
                            On-Hold Bookings
                        </option>
                        <option value="{{ backpack_url('booking/invoiced') }}" {{
                            Route::currentRouteName()==='booking.invoiced' ? 'selected' : '' }}>
                            Invoiced Bookings
                        </option>
                        <option value="{{ backpack_url('booking/cancelled') }}" {{
                            Route::currentRouteName()==='booking.cancelled' ? 'selected' : '' }}>
                            Cancelled Bookings
                        </option>
                    </select>

                </div>
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
                            style="width:360px; min-width:260px;" placeholder="Quick search...">
                        
                        <button id="resetAll" class="btn btn-outline-danger btn-sm text-nowrap">
                            Reset
                        </button>
                    </div>

                    {{-- RIGHT EXPORT BUTTONS --}}
                    <div class="d-flex gap-2 flex-wrap mt-2 mt-md-0">
                        <button id="exportCsv" class="btn btn-success btn-sm text-nowrap w-100 w-md-auto">
                            <i class="la la-file-excel-o"></i> Excel
                        </button>
                        <button id="exportExcel" class="btn btn-danger btn-sm text-nowrap w-100 w-md-auto">
                            <i class="la la-file-pdf-o"></i> PDF
                        </button>
                    </div>
                </div>

                {{-- GRID --}}
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
    let gridApi;

    const gridOptions = {
        columnDefs: @json($gridConfig['columns']),
        rowData: @json($gridConfig['data']),
        pagination: true,
        paginationPageSize: 50,
        // sideBar: true,
        rowHeight: 28,
        animateRows: true,
        defaultColDef: {
            sortable: true,
            filter: true,
            resizable: true,
            floatingFilter: false,  // optional – agar floating filter chahiye to true kar do

        },


        // Action column ke liye HTML render
        components: {
        htmlRenderer: (params) => params.value || '',
        },
    };

    document.addEventListener('DOMContentLoaded', () => {
        const gridDiv = document.querySelector('#myGrid');
        gridApi = agGrid.createGrid(gridDiv, gridOptions);

        // Quick search
        document.getElementById('quickFilter')?.addEventListener('input', e => {
            gridApi.setGridOption('quickFilterText', e.target.value);
        });

        // Reset filters
        document.getElementById('resetAll')?.addEventListener('click', () => {
            gridApi.setFilterModel(null);
            gridApi.setGridOption('quickFilterText', '');
            document.getElementById('quickFilter').value = '';
        });

        // Excel Export (action column exclude karke)
        document.getElementById('exportCsv')?.addEventListener('click', () => {
            gridApi.exportDataAsExcel({
                fileName: 'live-bookings.xlsx',
                columnKeys: gridOptions.columnDefs.map(col => col.field).filter(f => f !== 'action'),
            });
        });

        // PDF Export (simple jsPDF + autoTable)
        document.getElementById('exportExcel')?.addEventListener('click', () => {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'pt', 'a4');

            const exportCols = gridOptions.columnDefs
                .filter(col => col.field !== 'action')
                .map(col => ({ header: col.headerName, dataKey: col.field }));

            const rows = [];
            gridApi.forEachNodeAfterFilterAndSort(node => rows.push(node.data));

            doc.text('Live Bookings Report', 40, 30);
            doc.autoTable({
                columns: exportCols,
                body: rows,
                startY: 50,
                styles: { fontSize: 8 },
                headStyles: { fillColor: [33, 150, 243] },
            });
            doc.save('live-bookings.pdf');
        });

        // Status dropdown navigation (tumhara original code)
        document.getElementById('statusFilter')?.addEventListener('change', function() {
            if (this.value) {
                window.location.href = this.value;
            }
        });
    });
</script>
@endpush
