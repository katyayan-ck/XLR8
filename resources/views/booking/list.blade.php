{{-- resources/views/admin/booking/list.blade.php --}}
@extends(backpack_view('blank'))

@section('header')
<section class="container-fluid">
    <h2>All Live Bookings <small>({{ $bookings->count() }} records)</small></h2>
</section>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0"
                    style="font-weight: 800; color: #000; font-size: 1.55rem; text-shadow: 0 1px 3px rgba(0,0,0,0.3);">
                    Live Bookings Dashboard
                </h3>
                <a href="{{ backpack_url('booking/create') }}" class="btn btn-light btn-sm fw-bold">
                    Add New Booking
                </a>
            </div>

            <div class="card-body p-0" style="background:#f8fafc">
                <!-- Toolbar -->
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-white"
                    style="min-height: 65px;">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <input type="text" id="quickFilter" class="form-control" style="width: 340px;"
                            placeholder="Quick search in all columns...">
                        <button id="toggleFilters" class="btn btn-outline-secondary btn-sm">Filters</button>
                        <button id="resetAll" class="btn btn-outline-danger btn-sm">Reset</button>
                        <button id="togglePivot" class="btn btn-outline-primary btn-sm">Pivot Mode</button>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button id="exportCsv" class="btn btn-success btn-sm">CSV</button>
                        <button id="exportExcel" class="btn btn-success btn-sm">Excel</button>
                        <button onclick="window.print()" class="btn btn-secondary btn-sm">Print</button>
                    </div>
                </div>

                <!-- AG Grid -->
                <div id="myGrid" class="ag-theme-quartz" style="height: calc(100vh - 260px); width:100%;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after_styles')
<link rel="stylesheet" href="https://unpkg.com/ag-grid-community/styles/ag-theme-quartz.css">
@endpush

@push('after_scripts')
<script src="https://unpkg.com/ag-grid-enterprise/dist/ag-grid-enterprise.min.js"></script>
<script>
    // Watermark hide (development only)
    if (typeof agGrid !== 'undefined') {
        agGrid.LicenseManager.setLicenseKey("Using_AG_Grid_Enterprise_For_Evaluation_Purposes_Only");
    }

    const rowData = @json($bookings);

    const columnDefs = [
        { headerName: "S.No.", valueGetter: "node.rowIndex + 1", width: 70, pinned: 'left', lockPosition: true, cellStyle: {textAlign: 'center'} },
        { headerName: "Booking No", field: "booking_no", width: 140, pinned: 'left' },
        { headerName: "Date", field: "booking_date", width: 110 },
        { headerName: "Customer", field: "name", width: 230 },
        { headerName: "Mobile", field: "mobile", width: 135 },

        { headerName: "Source", field: "booking_source", width: 130 },
        { headerName: "Coll.Type", field: "collection_type", width: 100 },
        { headerName: "Collected By", field: "collected_by", width: 160 },
        { headerName: "DSA", field: "dsa_name", width: 100 },

        { headerName: "Model", field: "model", width: 200 },
        { headerName: "Variant", field: "variant", width: 320 },
        { headerName: "Color", field: "color", width: 110 },

        {
            headerName: "Amount",
            field: "booking_amount",
            width: 140,
            cellStyle: { textAlign: 'right', fontWeight: '600' },
            valueFormatter: params => params.value && params.value !== '0' ? '₹ ' + params.value : '₹ 0'
        },

        { headerName: "Finance", field: "fin_mode", width: 110 },
        { headerName: "Financier", field: "financier", width: 150 },

        { headerName: "Consultant", field: "consultant", width: 180 },
        { headerName: "Branch", field: "branch", width: 160 },
        { headerName: "Location", field: "location", width: 160 },
        { headerName: "Days", field: "days_count", width: 90, cellStyle: {textAlign: 'center'} },
        { headerName: "Mode", field: "b_mode", width: 110 },
        { headerName: "Type", field: "b_type", width: 110 },

        // {
        //     headerName: "Status",
        //     field: "status",
        //     width: 130,
        //     cellStyle: { textAlign: 'center' },
        //     cellRenderer: params => params.value || '-'
        // },

        {
    headerName: "Actions",
    field: "action",
    width: 110,
    pinned: 'right',
    lockPosition: true,
    sortable: false,
    filter: false,
    resizable: false,
    cellRenderer: params => params.value || ''
}
    ];

    const gridOptions = {
        columnDefs: columnDefs,
        rowData: rowData,
        defaultColDef: {
            sortable: true,
            filter: true,
            floatingFilter: true,
            resizable: true,
            enableRowGroup: true,
            enablePivot: true,
            enableValue: true,
        },
        pagination: true,
        paginationPageSize: 50,
        paginationPageSizeSelector: [25, 50, 100, 200, 500],
        sideBar: true,
        rowGroupPanelShow: 'always',
        pivotPanelShow: 'always',
        animateRows: true,
        headerHeight: 48,
        theme: 'quartz',
    };

    document.addEventListener('DOMContentLoaded', () => {
        const gridDiv = document.querySelector('#myGrid');
        const gridApi = agGrid.createGrid(gridDiv, gridOptions);

        let filterTimeout;
        document.getElementById('quickFilter')?.addEventListener('input', (e) => {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
        if (gridApi && !gridApi.isDestroyed()) {
            gridApi.setGridOption('quickFilterText', e.target.value);
        }
        }, 300);
        });
        document.getElementById('toggleFilters')?.addEventListener('click', () => {
            gridApi.setSideBarVisible(!gridApi.isSideBarVisible());
        });
        document.getElementById('resetAll')?.addEventListener('click', () => {
            gridApi.setFilterModel(null);
            gridApi.setQuickFilter('');
            gridApi.setPivotMode(false);
            document.getElementById('quickFilter').value = '';
        });
        document.getElementById('togglePivot')?.addEventListener('click', function () {
            const isPivot = gridApi.isPivotMode();
            gridApi.setPivotMode(!isPivot);
            this.textContent = isPivot ? 'Pivot Mode' : 'Exit Pivot';
            this.classList.toggle('btn-danger', !isPivot);
        });
        document.getElementById('exportCsv')?.addEventListener('click', () => gridApi.exportDataAsCsv({fileName: 'live-bookings.csv'}));
        document.getElementById('exportExcel')?.addEventListener('click', () => gridApi.exportDataAsExcel({fileName: 'live-bookings.xlsx'}));
    });
</script>
@endpush