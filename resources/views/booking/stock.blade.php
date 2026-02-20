@extends(backpack_view('blank'))

@section('header')
<section class="container-fluid">
    <h2>
        <i class="la la-box text-success"></i> Stock Report
        <small class="d-none d-md-inline">Vehicle Stock Inventory</small>
    </h2>
</section>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">

            {{-- HEADER --}}
            {{-- <div
                class="card-header bg-gradient-success d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h3 class="card-title mb-0 fw-bold text-black text-nowrap">
                    Stock Report Dashboard
                </h3>
            </div> --}}

            {{-- BODY --}}
            <div class="card-body p-0 bg-light">

                {{-- TOOLBAR --}}
                <div
                    class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3 border-bottom bg-white">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <input type="text" id="quickFilter" class="form-control" style="width: 360px; min-width: 260px;"
                            placeholder="Quick search in all columns...">
                        <button id="resetAll" class="btn btn-outline-danger btn-sm">
                            Reset
                        </button>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button id="exportCsv" class="btn btn-success btn-sm">
                            <i class="la la-file-excel-o"></i> Excel
                        </button>
                        <button id="exportPdf" class="btn btn-danger btn-sm">
                            <i class="la la-file-pdf-o"></i> PDF
                        </button>
                    </div>
                </div>

                {{-- GRID --}}
                <div id="myGrid" class="ag-theme-quartz" style="height: calc(100vh - 240px); width: 100%;"></div>
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

    const columnDefs = [
        
        { field: 'sno', headerName: 'S.No.', pinned: 'left', width: 80, filter: false },

        {
            headerName: 'VEHICLE INFO',
            children: [
                { field: 'seg',  headerName: 'SEGMENT', width: 140 },
                { field: 'mdl',  headerName: 'MODEL',   width: 160 },
                { field: 'vrnt', headerName: 'VARIANT', width: 220 },
                { field: 'clr',  headerName: 'COLOR',   width: 130 },
            ]
        },

        {
            headerName: 'TOTAL STOCK',
            children: [
                { field: 'total', headerName: 'TOTAL', width: 100, cellClass: 'text-right' },
                { field: 'bkn',   headerName: 'BKN',   width: 80,  cellClass: 'text-right' },
                { field: 'chr',   headerName: 'CHR',   width: 80,  cellClass: 'text-right' },
            ]
        },

        {
            headerName: gridConfig.ovin || 'STOCK VIN-2024',
            children: gridConfig.locbr.map(loc => ({
                field: `ovin_${loc.toLowerCase()}`,
                headerName: loc,
                width: 80,
                cellClass: 'text-right'
            })).concat([
                { field: 'ovin_damage',      headerName: 'DAMAGE',     width: 100, cellClass: 'text-right' },
                { field: 'ovin_dlr_transit', headerName: 'DLR TST',    width: 110, cellClass: 'text-right' },
                { field: 'ovin_oem_transit', headerName: 'OEM TST',    width: 110, cellClass: 'text-right' },
            ])
        },

        {
            headerName: gridConfig.cvin || 'STOCK VIN-2025',
            children: gridConfig.locbr.map(loc => ({
                field: `cvin_${loc.toLowerCase()}`,
                headerName: loc,
                width: 80,
                cellClass: 'text-right'
            })).concat([
                { field: 'cvin_damage',      headerName: 'DAMAGE',     width: 100, cellClass: 'text-right' },
                { field: 'cvin_dlr_transit', headerName: 'DLR TST',    width: 110, cellClass: 'text-right' },
                { field: 'cvin_oem_transit', headerName: 'OEM TST',    width: 110, cellClass: 'text-right' },
            ])
        },

        {
            headerName: 'GLOBAL DATA',
            children: [
                { field: 'tst_max_age',  headerName: 'TST MAX AGE', width: 140 },
                { field: 'stock_max_age', headerName: 'PHY MAX AGE', width: 140 },
                { field: 'stock_gt_60',  headerName: 'AGE > 60D',   width: 120, cellClass: 'text-right' },
                { field: 'bkng',         headerName: 'BOOKED',      width: 120, cellClass: 'text-right' },
                { field: 'enq',          headerName: 'HOT ENQ',     width: 120, cellClass: 'text-right' },
                { field: 'lordr',        headerName: 'LIVE ORDERS', width: 130, cellClass: 'text-right' },
            ]
        }
    ];

    const gridOptions = {
        columnDefs,
        rowData: gridConfig.data || [],
        pagination: true,
        paginationPageSize: 20,
        rowHeight: 22,
        paginationPageSizeSelector: [10, 20, 50, 100],
        animateRows: true,
        defaultColDef: {
            sortable: true,
            filter: true,
            resizable: true,
            cellClass: 'text-center'
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

        // Excel Export with multi-row headers
        document.getElementById('exportCsv')?.addEventListener('click', () => {
            const headerRow1 = [
                '', 'VEHICLE INFO', '', '', '', 'TOTAL STOCK', '', '',
                gridConfig.ovin || 'STOCK VIN-2024', '', '', '', '', '',
                gridConfig.cvin || 'STOCK VIN-2025', '', '', '', '', '',
                'GLOBAL DATA', '', '', '', ''
            ];

            const headerRow2 = [
                'S.No.', 'SEGMENT', 'MODEL', 'VARIANT', 'COLOR',
                'TOTAL', 'BKN', 'CHR',
                ...gridConfig.locbr.map(loc => loc), 'DAMAGE', 'DLR TST', 'OEM TST',
                ...gridConfig.locbr.map(loc => loc), 'DAMAGE', 'DLR TST', 'OEM TST',
                'TST MAX AGE', 'PHY MAX AGE', 'AGE > 60D', 'BOOKED', 'HOT ENQ', 'LIVE ORDERS'
            ];

            const dataRows = [];
            gridApi.forEachNodeAfterFilterAndSort(node => {
                const d = node.data;
                dataRows.push([
                    d.sno, d.seg, d.mdl, d.vrnt, d.clr,
                    d.total, d.bkn, d.chr,
                    ...gridConfig.locbr.map(loc => d[`ovin_${loc.toLowerCase()}`] || 0),
                    d.ovin_damage, d.ovin_dlr_transit, d.ovin_oem_transit,
                    ...gridConfig.locbr.map(loc => d[`cvin_${loc.toLowerCase()}`] || 0),
                    d.cvin_damage, d.cvin_dlr_transit, d.cvin_oem_transit,
                    d.tst_max_age, d.stock_max_age, d.stock_gt_60, d.bkng, d.enq, d.lordr
                ]);
            });

            const aoa = [headerRow1, headerRow2, ...dataRows];
            const ws = XLSX.utils.aoa_to_sheet(aoa);

            ws['!merges'] = [
                { s: { r: 0, c: 1 }, e: { r: 0, c: 4 } },
                { s: { r: 0, c: 5 }, e: { r: 0, c: 7 } },
                { s: { r: 0, c: 8 }, e: { r: 0, c: 8 + gridConfig.locbr.length + 2 } },
                { s: { r: 0, c: 8 + gridConfig.locbr.length + 3 }, e: { r: 0, c: 8 + gridConfig.locbr.length + 3 + gridConfig.locbr.length + 2 } },
                { s: { r: 0, c: 8 + gridConfig.locbr.length + 3 + gridConfig.locbr.length + 3 }, e: { r: 0, c: aoa[0].length - 1 } }
            ];

            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Stock Report');
            XLSX.writeFile(wb, 'stock-report-' + new Date().toISOString().slice(0,10) + '.xlsx');
        });

        // PDF Export with multi-row headers
        document.getElementById('exportPdf')?.addEventListener('click', () => {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });

            doc.setFontSize(14);
            doc.text("Stock Summary Report", 40, 40);

            const headerRow1 = [
                '', 'VEHICLE INFO', '', '', '', 'TOTAL STOCK', '', '',
                gridConfig.ovin || 'STOCK VIN-2024', '', '', '', '', '',
                gridConfig.cvin || 'STOCK VIN-2025', '', '', '', '', '',
                'GLOBAL DATA', '', '', '', ''
            ];

            const headerRow2 = [
                'S.No.', 'SEGMENT', 'MODEL', 'VARIANT', 'COLOR',
                'TOTAL', 'BKN', 'CHR',
                ...gridConfig.locbr.map(loc => loc), 'DAMAGE', 'DLR TST', 'OEM TST',
                ...gridConfig.locbr.map(loc => loc), 'DAMAGE', 'DLR TST', 'OEM TST',
                'TST MAX AGE', 'PHY MAX AGE', 'AGE > 60D', 'BOOKED', 'HOT ENQ', 'LIVE ORDERS'
            ];

            const body = [];
            gridApi.forEachNodeAfterFilterAndSort(node => {
                const d = node.data;
                body.push([
                    d.sno, d.seg, d.mdl, d.vrnt, d.clr,
                    d.total, d.bkn, d.chr,
                    ...gridConfig.locbr.map(loc => d[`ovin_${loc.toLowerCase()}`] || 0),
                    d.ovin_damage, d.ovin_dlr_transit, d.ovin_oem_transit,
                    ...gridConfig.locbr.map(loc => d[`cvin_${loc.toLowerCase()}`] || 0),
                    d.cvin_damage, d.cvin_dlr_transit, d.cvin_oem_transit,
                    d.tst_max_age, d.stock_max_age, d.stock_gt_60, d.bkng, d.enq, d.lordr
                ]);
            });

            doc.autoTable({
                head: [headerRow1, headerRow2],
                body,
                startY: 60,
                styles: { fontSize: 8, cellPadding: 4, overflow: 'linebreak', halign: 'center' },
                headStyles: [{
                    fillColor: [40, 167, 69],
                    textColor: 255,
                    fontStyle: 'bold'
                }, {
                    fillColor: [60, 187, 89],
                    textColor: 255,
                    fontStyle: 'bold'
                }],
                alternateRowStyles: { fillColor: [245, 245, 245] },
                margin: { top: 60, left: 30, right: 30 },
                didParseCell: function (data) {
                    if (data.row.index === 0) {
                        if (data.column.index === 1) data.cell.colSpan = 4;
                        if (data.column.index === 5) data.cell.colSpan = 3;
                        if (data.column.index === 8) data.cell.colSpan = gridConfig.locbr.length + 3;
                        if (data.column.index === 8 + gridConfig.locbr.length + 3) data.cell.colSpan = gridConfig.locbr.length + 3;
                        if (data.column.index === 8 + gridConfig.locbr.length + 3 + gridConfig.locbr.length + 3) data.cell.colSpan = 6;
                    }
                }
            });

            doc.save('stock-summary-report.pdf');
        });
    });
</script>
@endpush