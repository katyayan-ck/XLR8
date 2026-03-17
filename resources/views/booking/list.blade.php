{{-- resources/views/admin/booking/list.blade.php --}}
@extends(backpack_view('blank'))

@section('header')
<section class="container-fluid">
    <h2>
        {{-- <i class="la la-book text-primary"></i> All Live Bookings --}}
        {{-- <small class="d-none d-md-inline">({{ $bookings->count() }} records)</small> --}}
    </h2>
</section>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            {{-- HEADER --}}
            <div class="card-header bg-gradient-primary
                        d-flex justify-content-between align-items-center
                        flex-nowrap flex-md-nowrap flex-wrap gap-3">

                <h2 class="card-title mb-0 fw-bold text-black text-nowrap">
                    {{-- <i class="la la-book me-2"></i> --}}
                    {{ $title ?? 'All Live Bookings' }}
                    {{-- <small class="d-none d-md-inline ms-2">({{ $bookings->count() }} records)</small> --}}
                </h2>

                <!-- Right side group: button LEFT → dropdown RIGHT -->
                <div class="d-flex align-items-center gap-3 flex-nowrap">

                    <!-- 1. Add New Booking button (left position) -->
                    <a href="{{ backpack_url('booking/create') }}" class="btn btn-blue btn-sm fw-bold shadow-sm">
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

                <div
                    class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3 border-bottom bg-white">

                    {{-- LEFT: Search + Reset --}}
                    <div class="d-flex align-items-center gap-2 flex-nowrap">
                        <input type="text" id="quickFilter" class="form-control w-100 w-md-auto"
                            style="width:360px; min-width:260px;" placeholder="Smart Search...">

                        <button id="resetAll" class="btn btn-outline-danger btn-sm text-nowrap">
                            Reset
                        </button>
                    </div>

                    {{-- CENTER: Column visibility buttons --}}
                    <div class="d-flex gap-2 flex-nowrap justify-content-center">

                        <button id="btnDefaultHeaders" class="btn btn-secondary btn-sm text-nowrap">
                            Default Headers
                        </button>

                        {{-- Customise Headers --}}
                        <div class="position-relative d-inline-block">

                            <button id="btnCustomiseHeaders" class="btn btn-red btn-sm text-nowrap">
                                Customise Headers
                            </button>

                            {{-- Bubble Dropdown --}}
                            <div id="columnBubble" style="display:none;
                                    position:absolute;
                                    top:110%;
                                    left:0;
                                    width:260px;
                                    background:#fff;
                                    border:1px solid #ddd;
                                    border-radius:6px;
                                    box-shadow:0 8px 20px rgba(0,0,0,.15);
                                    z-index:9999;">

                                <div class="d-flex justify-content-between align-items-center px-2 py-1 border-bottom">
                                    <strong style="font-size:13px;">Customise Headers</strong>
                                    <button id="closeColumnBubble" class="btn btn-sm btn-link text-danger p-0">
                                        ✕
                                    </button>
                                </div>

                                <div style="max-height:260px; overflow:auto;">
                                    <table class="table table-sm mb-0">
                                        <tbody id="columnBubbleBody"></tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                        <button id="btnAllHeaders" class="btn btn-blue btn-sm text-nowrap">
                            All Headers
                        </button>




                    </div>

                    {{-- RIGHT: Export buttons --}}
                    <div class="d-flex gap-2 flex-nowrap">
                        {{-- <button id="exportCsv" class="btn btn-success btn-sm text-nowrap">
                            <i class="la la-file-excel-o"></i> Excel
                        </button> --}}
                        <button id="exportCsv" class="btn btn-sm text-nowrap d-flex align-items-center gap-2">

                            <img src="{{ asset('images/export-excel.png') }}" alt="Excel"
                                style="height:30px; width:auto;">

                            {{-- <span>Excel</span> --}}
                        </button>

                        <button id="exportExcel" class="btn btn-sm text-nowrap d-flex align-items-center gap-2">

                            <img src="{{ asset('images/export-pdf.png') }}" alt="PDF" style="height:30px; width:auto;">

                            {{-- <span>PDF</span> --}}
                        </button>

                    </div>

                </div>


                {{-- GRID --}}
                <div id="myGrid" class="ag-theme-quartz" style="height: calc(93vh - 260px); width:100%;"></div>
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

<style>
    /* Header text center */
    .ag-theme-quartz .center-header .ag-header-cell-label {
        justify-content: center !important;
    }

    /* GROUP HEADER CENTER */
    .ag-theme-quartz .ag-header-group-cell-label {
        justify-content: center !important;
    }

    #columnBubble {
        width: 320px;
    }
</style>
@endpush


@push('after_scripts')
<script src="https://unpkg.com/ag-grid-community/dist/ag-grid-community.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

<script>
    const ALL_COLUMNS = @json($gridConfig['columns']);
    function getCols(fields) {
        return ALL_COLUMNS.filter(col => fields.includes(col.field));
    }

    let gridApi;




    document.getElementById('closeColumnBubble')
        ?.addEventListener('click', () => {
            document.getElementById('columnBubble').style.display = 'none';
        });

    document.addEventListener('click', () => {
        const bubble = document.getElementById('columnBubble');
        if (bubble) bubble.style.display = 'none';
    });


    let columnDefs;
    const STATUS = getCurrentStatus();

        if (STATUS === 'live') {
            columnDefs = [

            {
            headerName:'Primary',
            children: getCols([
            'serial_no',
            'booking_no',
            'created_at',
            'booking_date',
            'days_count'
            ])
            },

            {
            headerName:'Customer',
            children: getCols([
            'name',
            'care_of',
            'mobile',
            'pan_no',
            'adhar_no',
            'gstn',
            'branch_name',
            'location_name'
            ])
            },

            {
            headerName:'Vehicle',
            children: getCols([
            'segment',
            'model',
            'variant',
            'color',
            'chasis_no'
            ])
            },

            {
            headerName:'Booking Details',
            children: getCols([
            'b_type',
            'b_source',
            'consultant',
            'col_type',
            'col_by'
            ])
            },

            {
            headerName:'Finance',
            children: getCols([
            'booking_amount',
            'fin_mode',
            'financier',
            'loan_status'
            ])
            },

            {
            headerName:'Dates',
            children: getCols([
            'cancel_date',
            'refund_request_date',
            'refund_date',
            'refund_rejection_date',
            'invoice_date',
            'receipt_date',
            'cpd',
            'del_date',
            'otf_date'
            ])
            },

            {
            headerName:'DMS',
            children: getCols([
            'sap_no',
            'dms_no',
            'dms_otf',
            'dms_so',
            'online_bk_ref_no'
            ])
            },

            {
            headerName:'Stock',
            children: getCols([
            'livecount',
            'stockcount'
            ])
            },

            {
            headerName:'Actions',
            children: getCols(['action'])
            }

            ];

        }else if (STATUS === 'hold') {

            columnDefs = [

            {
            headerName:'Primary',
            children: getCols([
            'serial_no',
            'booking_no',
            'created_at',
            'booking_date',
            'days_count'
            ])
            },

            {
            headerName:'Customer',
            children: getCols([
            'customer_type',
            'customer_category',
            'col_type',
            'col_by',
            'booking_amount',
            'receipt_no',
            'receipt_date',
            'name',
            'care_of',
            'care_of_name',
            'mobile',
            'alt_mobile',
            'gender',
            'occupation',
            'pan_no',
            'adhar_no',
            'gstn',
            'dob',
            'customer_age',
            'branch_name',
            'location_name'
            ])
            },

            {
            headerName:'Vehicle',
            children: getCols([
            'segment',
            'model',
            'variant',
            'color',
            'seating',
            'accessories_amount',
            'chasis_no'
            ])
            },

            {
            headerName:'Booking Detail',
            children: getCols([
            'booking_status',
            'b_type',
            'online_bk_ref_no',
            'b_source',
            'dsa_name',
            'consultant',
            'delivery_date_type',
            'del_date',
            'fin_mode',
            'financier',
            'financier_short',
            'loan_status'
            ])
            },

            {
            headerName:'Purchase type details',
            children: getCols([
            'purchase_type',
            'brand_make1',
            'model_variant1',
            'brand_make2',
            'model_variant2',
            'veh_reg_no',
            'veh_mfg_year',
            'veh_odo',
            'used_expected_price',
            'used_offered_price',
            'exchange_bonus',
            'price_gap'
            ])
            },

            {
            headerName:'Referred BY',
            children: getCols([
            'ref_customer_name',
            'ref_mobile',
            'existing_model',
            'existing_variant',
            'chasis_reg_no'
            ])
            },

            {
            headerName:'Dms Booking Details',
            children: getCols([
            'dms_no',
            'dms_otf',
            'otf_date',
            'dms_so'
            ])
            },

            {
            headerName:'Stock',
            children: getCols([
            'livecount',
            'stockcount'
            ])
            },

            {
            headerName:'Actions',
            children: getCols(['action'])
            }

            ];

        }else if (STATUS === 'invoiced') {

            columnDefs = [

            {
            headerName:'Primary',
            children: getCols([
            'serial_no',
            'booking_no',
            'created_at',
            'booking_date',
            'days_count',
            'invoice_no',
            'invoice_date'
            ])
            },

            {
            headerName:'Customer',
            children: getCols([
            'customer_type',
            'customer_category',
            'col_type',
            'col_by',
            'booking_amount',
            'receipt_no',
            'receipt_date',
            'name',
            'care_of',
            'care_of_name',
            'mobile',
            'alt_mobile',
            'gender',
            'occupation',
            'pan_no',
            'adhar_no',
            'gstn',
            'dob',
            'customer_age',
            'branch_name',
            'location_name'
            ])
            },

            {
            headerName:'Vehicle',
            children: getCols([
            'segment',
            'model',
            'variant',
            'color',
            'seating',
            'accessories_amount',
            'chasis_no'
            ])
            },

            {
            headerName:'Booking Detail',
            children: getCols([
            'booking_status',
            'b_type',
            'online_bk_ref_no',
            'b_source',
            'dsa_name',
            'consultant',
            'delivery_date_type',
            'del_date',
            'fin_mode',
            'financier',
            'financier_short',
            'loan_status'
            ])
            },

            {
            headerName:'Purchase type details',
            children: getCols([
            'purchase_type',
            'brand_make1',
            'model_variant1',
            'brand_make2',
            'model_variant2',
            'veh_reg_no',
            'veh_mfg_year',
            'veh_odo',
            'used_expected_price',
            'used_offered_price',
            'exchange_bonus',
            'price_gap'
            ])
            },

            {
            headerName:'Referred BY',
            children: getCols([
            'ref_customer_name',
            'ref_mobile',
            'existing_model',
            'existing_variant',
            'chasis_reg_no'
            ])
            },

            {
            headerName:'Dms Booking Details',
            children: getCols([
            'dms_no',
            'dms_otf',
            'otf_date',
            'dms_so'
            ])
            },

            {
            headerName:'Stock',
            children: getCols([
            'livecount',
            'stockcount'
            ])
            },

            {
            headerName:'Actions',
            children: getCols(['action'])
            }

            ];

        }else if (STATUS === 'cancelled') {

        columnDefs = [

        {
        headerName:'Primary',
        children: getCols([
        'serial_no',
        'booking_no',
        'created_at',
        'booking_date',
        'days_count',
        'cancel_date'
        ])
        },

        {
        headerName:'Customer',
        children: getCols([
        'customer_type',
        'customer_category',
        'col_type',
        'col_by',
        'booking_amount',
        'receipt_no',
        'receipt_date',
        'name',
        'care_of',
        'care_of_name',
        'mobile',
        'alt_mobile',
        'gender',
        'occupation',
        'pan_no',
        'adhar_no',
        'gstn',
        'dob',
        'customer_age',
        'branch_name',
        'location_name'
        ])
        },

        {
        headerName:'Vehicle',
        children: getCols([
        'segment',
        'model',
        'variant',
        'color',
        'seating',
        'accessories_amount',
        'chasis_no'
        ])
        },

        {
        headerName:'Booking Detail',
        children: getCols([
        'booking_status',
        'b_type',
        'online_bk_ref_no',
        'b_source',
        'dsa_name',
        'consultant',
        'delivery_date_type',
        'del_date',
        'fin_mode',
        'financier',
        'financier_short',
        'loan_status'
        ])
        },

        {
        headerName:'Purchase type details',
        children: getCols([
        'purchase_type',
        'brand_make1',
        'model_variant1',
        'brand_make2',
        'model_variant2',
        'veh_reg_no',
        'veh_mfg_year',
        'veh_odo',
        'used_expected_price',
        'used_offered_price',
        'exchange_bonus',
        'price_gap'
        ])
        },

        {
        headerName:'Referred BY',
        children: getCols([
        'ref_customer_name',
        'ref_mobile',
        'existing_model',
        'existing_variant',
        'chasis_reg_no'
        ])
        },

        {
        headerName:'Dms Booking Details',
        children: getCols([
        'dms_no',
        'dms_otf',
        'otf_date',
        'dms_so'
        ])
        },

        {
        headerName:'Stock',
        children: getCols([
        'livecount',
        'stockcount'
        ])
        },

        {
        headerName:'Actions',
        children: getCols(['action'])
        }

        ];

    }

    const DEFAULT_COLUMNS_BY_STATUS = {

        live: [

            'serial_no',
            'booking_no',
            'created_at',
            'booking_date',
            'days_count',

            'customer_category',
            'col_type',
            'booking_amount',

            'name',
            'mobile',
            'pan_no',
            'adhar_no',
            'gstn',

            'branch_name',
            'location_name',

            'model',
            'variant',
            'color',
            'seating',

            'accessories_amount',
            'chasis_no',

            'b_source',
            'consultant',

            'del_date',

            'fin_mode',
            'financier_short',
            'loan_status',
            'purchase_type',

            'dms_otf',
            'dms_so',

            'livecount',
            'stockcount',

            'action'

        ],

        hold: [

            'serial_no',
            'booking_no',
            'created_at',
            'booking_date',
            'days_count',

            'customer_category',
            'col_type',
            'booking_amount',

            'name',
            'mobile',

            'branch_name',
            'location_name',

            'model',
            'variant',
            'color',
            'seating',

            'chasis_no',

            'b_source',
            'consultant',

            'livecount',
            'stockcount',

            'action'

        ],

        invoiced: [

            'serial_no',
            'booking_no',
            'created_at',
            'booking_date',
            'days_count',

            'invoice_no',
            'invoice_date',

            'customer_category',

            'name',
            'mobile',

            'branch_name',
            'location_name',

            'model',
            'variant',
            'color',
            'seating',

            'chasis_no',

            'b_source',
            'consultant',

            'livecount',
            'stockcount',

            'action'

        ],

        cancelled: [

            'serial_no',
            'booking_no',
            'created_at',
            'booking_date',
            'days_count',

            'cancel_date',

            'customer_category',
            'col_type',
            'booking_amount',

            'name',
            'mobile',

            'branch_name',
            'location_name',

            'model',
            'variant',
            'color',
            'seating',

            'chasis_no',

            'b_source',
            'consultant',

            'action'

        ]
    };

    function getCurrentStatus() {
        const route = "{{ Route::currentRouteName() }}";

        if (route === 'booking.hold') return 'hold';
        if (route === 'booking.invoiced') return 'invoiced';
        if (route === 'booking.cancelled') return 'cancelled';

        return 'live'; // default
    }


    function openColumnBubble(){

    const bubble = document.getElementById('columnBubble');
    const tbody  = document.getElementById('columnBubbleBody');

    if(!gridApi) return;

    tbody.innerHTML='';

    // columnDefs se groups lo
    columnDefs.forEach(group => {

        const groupName = group.headerName;
        const children  = group.children || [];

        if(groupName === 'Actions') return;

        // GROUP ROW
        const groupTr = document.createElement('tr');

        const groupCheckTd = document.createElement('td');
        groupCheckTd.style.width='30px';

        const groupCheckbox = document.createElement('input');
        groupCheckbox.type='checkbox';

        const fields = children
    .map(c => c.field)
    .filter(Boolean);

        const visible = fields.some(f=>{
            const col = gridApi.getColumn(f);
            return col && col.isVisible();
        });

        groupCheckbox.checked = visible;

        // Primary always visible
        if(groupName === 'Primary'){
            groupCheckbox.checked = true;
            groupCheckbox.disabled = true;
        }

        groupCheckbox.addEventListener('change',()=>{

            gridApi.setColumnsVisible(fields, groupCheckbox.checked);

            tbody.querySelectorAll(`[data-group='${groupName}'] input`)
                .forEach(cb=>cb.checked = groupCheckbox.checked);

        });

        groupCheckTd.appendChild(groupCheckbox);

        const groupLabelTd = document.createElement('td');
        groupLabelTd.innerHTML = `<strong>${groupName}</strong>`;

        groupTr.appendChild(groupCheckTd);
        groupTr.appendChild(groupLabelTd);

        tbody.appendChild(groupTr);

        // CHILD ROWS
        children.forEach(col=>{

            if(!col.field) return;

            const tr = document.createElement('tr');
            tr.dataset.group = groupName;

            const tdCheck = document.createElement('td');
            tdCheck.style.paddingLeft='25px';

            const checkbox = document.createElement('input');
            checkbox.type='checkbox';

            const column = gridApi.getColumn(col.field);
            checkbox.checked = column ? column.isVisible() : false;

            if(groupName === 'Primary'){
                checkbox.checked = true;
                checkbox.disabled = true;
            }

            checkbox.addEventListener('change',()=>{
                gridApi.setColumnsVisible([col.field], checkbox.checked);
            });

            tdCheck.appendChild(checkbox);

            const tdLabel = document.createElement('td');
            tdLabel.innerText = col.headerName;

            tr.appendChild(tdCheck);
            tr.appendChild(tdLabel);

            tbody.appendChild(tr);

        });

    });

    bubble.style.display='block';
}
    // const COLUMN_GROUPS = {
    //     Primary: [
    //         {field:'serial_no', label:'S.No'},
    //         {field:'booking_no', label:'XB No'},
    //         {field:'created_at', label:'Entry Date'},
    //         {field:'booking_date', label:'Booking Date'},
    //         {field:'days_count', label:'Booking Age'}
    //     ],

    //     Customer: [
    //         {field:'name', label:'Customer Name'},
    //             {field:'care_of', label:'Care Of'},
    //             {field:'mobile', label:'Mobile'},
    //             {field:'pan_no', label:'PAN'},
    //             {field:'adhar_no', label:'Aadhaar'},
    //             {field:'gstn', label:'GSTIN'},
    //             {field:'branch_name', label:'Branch'},
    //             {field:'location_name', label:'Location'}
    //     ],

    //     Vehicle: [
    //         {field:'segment', label:'Segment'},
    //         {field:'model', label:'Model'},
    //         {field:'variant', label:'Variant'},
    //         {field:'color', label:'Color'},
    //         {field:'chasis_no', label:'Chassis No'}
    //     ],

    //     "Booking Details": [
    //         {field:'b_type', label:'Booking Type'},
    //         {field:'b_source', label:'Booking Source'},
    //         {field:'consultant', label:'Consultant'},
    //         {field:'col_type', label:'Collection Type'},
    //         {field:'col_by', label:'Collected By'}
    //     ],

    //     Finance: [
    //         {field:'booking_amount', label:'Booking Amount'},
    //         {field:'fin_mode', label:'Finance Mode'},
    //         {field:'financier', label:'Financier'},
    //         {field:'loan_status', label:'Loan Status'}
    //     ],

    //     Dates: [
    //         {field:'cancel_date', label:'Cancellation Date'},
    //         {field:'refund_request_date', label:'Refund Request Date'},
    //         {field:'refund_date', label:'Refund Date'},
    //         {field:'refund_rejection_date', label:'Refund Reject Date'},
    //         {field:'invoice_date', label:'Invoice Date'},
    //         {field:'receipt_date', label:'Receipt Date'},
    //         {field:'cpd', label:'CPD'},
    //         {field:'del_date', label:'Delivery Date'},
    //         {field:'otf_date', label:'OTF Date'}
    //     ],

    //     DMS: [
    //         {field:'sap_no', label:'SAP Booking No'},
    //         {field:'dms_no', label:'DMS Booking No'},
    //         {field:'dms_otf', label:'DMS OTF'},
    //         {field:'dms_so', label:'DMS SO'},
    //         {field:'online_bk_ref_no', label:'Online Ref No'}
    //     ],

    //     Stock: [
    //         {field:'livecount', label:'Live Order'},
    //         {field:'stockcount', label:'Stock In Hand'}
    //     ]
    // };


document.getElementById('btnCustomiseHeaders')
    ?.addEventListener('click', (e) => {
        e.stopPropagation();
        openColumnBubble();
    });


    const gridOptions = {
        columnDefs: columnDefs,
        rowData: @json($gridConfig['data']),

        pagination: true,
        paginationPageSize: 50,
        rowHeight: 28,
        animateRows: true,

        sideBar: {
            toolPanels: [
                {
                    id: 'columns',
                    labelDefault: 'Columns',
                    iconKey: 'columns',
                    toolPanel: 'agColumnsToolPanel',
                    toolPanelParams: {
                        suppressRowGroups: true,
                        suppressValues: true,
                        suppressPivots: true,
                        suppressPivotMode: true,
                    }
                }
            ],
            hiddenByDefault: true
        },

        defaultColDef: {
    sortable: true,
    filter: true,
    resizable: true,
    headerClass: 'center-header',
    cellStyle: { textAlign: 'center' }
},

        components: {
            htmlRenderer: params => params.value || '',
        },

        onGridReady: params => {
    gridApi = params.api;

    const status = getCurrentStatus();
    const defaultFields = DEFAULT_COLUMNS_BY_STATUS[status] || [];

    const allCols = [];
gridApi.getAllGridColumns().forEach(col=>{
    allCols.push(col.getColId());
});

    // hide all
    gridApi.setColumnsVisible(allCols, false);

    // show only defaults for that list
    gridApi.setColumnsVisible(defaultFields, true);

    // 🔥 AUTO WIDTH LOGIC
    setTimeout(() => {
        const allColumnIds = [];
        gridApi.getAllDisplayedColumns().forEach(column => {
            allColumnIds.push(column.getColId());
        });
        gridApi.autoSizeColumns(allColumnIds);
    }, 300);
}


    };

    document.addEventListener('DOMContentLoaded', () => {

        const gridDiv = document.querySelector('#myGrid');
        agGrid.createGrid(gridDiv, gridOptions);

        // 🔍 Quick search
        document.getElementById('quickFilter')?.addEventListener('input', e => {
            gridApi.setGridOption('quickFilterText', e.target.value);
        });

        // 🔄 Reset filters
        document.getElementById('resetAll')?.addEventListener('click', () => {
            gridApi.setFilterModel(null);
            gridApi.setGridOption('quickFilterText', '');
            document.getElementById('quickFilter').value = '';
        });

        // 📌 ALL HEADERS
        document.getElementById('btnAllHeaders')?.addEventListener('click', () => {
    const allCols = [];
gridApi.getAllGridColumns().forEach(col=>{
    allCols.push(col.getColId());
});
    gridApi.setColumnsVisible(allCols, true);

    setTimeout(() => {
        const allColumnIds = [];
        gridApi.getAllDisplayedColumns().forEach(column => {
            allColumnIds.push(column.getColId());
        });
        gridApi.autoSizeColumns(allColumnIds);
    }, 200);
});

        // 📌 DEFAULT HEADERS
        document.getElementById('btnDefaultHeaders')?.addEventListener('click', () => {

    if (!gridApi) return;

    const status = getCurrentStatus();
    const defaultFields = DEFAULT_COLUMNS_BY_STATUS[status] || [];

    const allCols = gridApi.getColumnDefs()
        .map(c => c.field)
        .filter(Boolean);

    // Hide all columns first
    gridApi.setColumnsVisible(allCols, false);

    // Show only default columns for current status
    gridApi.setColumnsVisible(defaultFields, true);

    setTimeout(() => {
    const allColumnIds = [];
    gridApi.getAllDisplayedColumns().forEach(column => {
        allColumnIds.push(column.getColId());
    });
    gridApi.autoSizeColumns(allColumnIds);
}, 200);

});




         document.getElementById('columnBubble')
        ?.addEventListener('click', e => e.stopPropagation());
});



        document.getElementById('exportCsv')?.addEventListener('click', () => {

    // 1️⃣ Visible columns nikaalo (action exclude)
    const visibleColumns = gridApi.getAllDisplayedColumns()
        .map(col => col.getColDef())
        .filter(col => col.field && col.field !== 'action');

    // 2️⃣ Header row
    const headers = visibleColumns.map(col => col.headerName);

    // 3️⃣ Row data (sirf visible + filtered rows)
    const rows = [];
    gridApi.forEachNodeAfterFilterAndSort(node => {
        const row = {};
        visibleColumns.forEach(col => {
            row[col.headerName] = node.data[col.field];
        });
        rows.push(row);
    });

    // 4️⃣ Excel worksheet + workbook
    const worksheet = XLSX.utils.json_to_sheet(rows, { header: headers });
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Live Bookings');

    // 5️⃣ Download
    XLSX.writeFile(workbook, 'live-bookings.xlsx');
});


        // 📄 PDF Export
        document.getElementById('exportExcel')?.addEventListener('click', () => {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'pt', 'a4');

    // 1️⃣ Sirf visible columns (action exclude)
    const visibleColumns = gridApi.getAllDisplayedColumns()
        .map(col => col.getColDef())
        .filter(col => col.field && col.field !== 'action');

    // 2️⃣ PDF columns config
    const exportCols = visibleColumns.map(col => ({
        header: col.headerName,
        dataKey: col.field
    }));

    // 3️⃣ Visible + filtered rows
    const rows = [];
    gridApi.forEachNodeAfterFilterAndSort(node => {
        const row = {};
        visibleColumns.forEach(col => {
            row[col.field] = node.data[col.field];
        });
        rows.push(row);
    });

    // 4️⃣ Generate PDF
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


        // 🔀 Status filter
        document.getElementById('statusFilter')?.addEventListener('change', function () {
            if (this.value) window.location.href = this.value;
        });

</script>
@endpush
