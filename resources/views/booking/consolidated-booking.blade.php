@extends(backpack_view('blank'))

@push('after_styles')
<link rel="stylesheet" href="https://unpkg.com/ag-grid-community/styles/ag-theme-quartz.css">

<style>
    body {
        background: #f8f9fa;
    }

    #myGrid {
        display: block;
    }

    /* Group Header Colors - exact match from your screenshot */
    .group-vehicle-info {
        background-color: #FFD6D5 !important;
    }

    /* light pink/red */
    .group-stock {
        background-color: #ADD8E6 !important;
    }

    /* light blue */
    .group-booking {
        background-color: #A6F1A6 !important;
    }

    /* light green */
    .group-hot-enq {
        background-color: #94DBFA !important;
    }

    /* light cyan/blue */
    .group-finance {
        background-color: #92D9F5 !important;
    }

    /* light blue-cyan */
    .group-exchange {
        background-color: #F0FFF0 !important;
    }

    /* very light mint */
    .group-global {
        background-color: #FFE4B5 !important;
    }

    /* light yellow/khaki */
    .group-pending {
        background-color: #FFE4E1 !important;
    }

    /* light pinkish-orange */

    .ag-header-cell-label {
        font-weight: 700 !important;
        justify-content: center !important;
    }

    .ag-cell {
        font-size: 13px !important;
        padding: 6px 8px !important;
    }

    .text-right {
        text-align: right !important;
    }

    .fw-bold {
        font-weight: 700 !important;
    }
</style>
@endpush

@section('header')
<section class="container-fluid">
    <h2>
        <i class="la la-chart-bar text-success"></i> Consolidated Booking Report
        <small class="d-none d-md-inline">All Branches Summary</small>
    </h2>
</section>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">

            {{-- TOOLBAR --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3 border-bottom bg-white">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <input type="text" id="quickFilter" class="form-control" style="width: 360px; min-width: 260px;"
                        placeholder="Quick search in all columns...">
                    <button id="resetAll" class="btn btn-outline-danger btn-sm">
                        Reset
                    </button>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button id="exportExcel" class="btn btn-success btn-sm">
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
        <div class="alert alert-info mt-3">
            {{ session('info') }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('after_scripts')
<script src="https://unpkg.com/ag-grid-community/dist/ag-grid-community.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

<script>
    const gridConfig = @json($gridConfig ?? []);

    let gridApi;

    const columnDefs = (gridConfig.columns || []).map(col => {
        let headerClass = '';
        if (col.headerName === 'Vehicle Info') headerClass = 'group-vehicle-info';
        else if (col.headerName === 'STOCK') headerClass = 'group-stock';
        else if (col.headerName === 'BOOKING') headerClass = 'group-booking';
        else if (col.headerName === 'HOT ENQ') headerClass = 'group-hot-enq';
        else if (col.headerName === 'INT IN FINANCE') headerClass = 'group-finance';
        else if (col.headerName === 'INT IN EXCHANGE') headerClass = 'group-exchange';
        else if (col.headerName === 'GLOBAL INFO') headerClass = 'group-global';
        else if (col.headerName === 'PENDING ACTIONS') headerClass = 'group-pending';

        return {
            headerName: col.headerName,
            field: col.field,
            children: col.children ? col.children.map(child => ({
                ...child,
                cellClass: child.cellClass || 'text-center'
            })) : null,
            headerClass: headerClass || '',
            sortable: true,
            filter: true,
            resizable: true,
            width: col.width || 150,
        };
    });

    const gridOptions = {
        columnDefs,
        rowData: gridConfig.data || [],
        pagination: true,
        paginationPageSize: 20,
        rowHeight : 30,
        paginationPageSizeSelector: [10, 20, 50, 100],
        animateRows: true,
        defaultColDef: {
            sortable: true,
            filter: true,
            resizable: true,
            cellClass: 'text-center',
        },
        overlayNoRowsTemplate: '<span class="ag-overlay-no-rows-center">No data available</span>',
    };

    document.addEventListener('DOMContentLoaded', () => {
        const gridDiv = document.querySelector('#myGrid');
        gridApi = agGrid.createGrid(gridDiv, gridOptions);

        // Quick Filter
        document.getElementById('quickFilter')?.addEventListener('input', e => {
            gridApi.setGridOption('quickFilterText', e.target.value);
        });

        // Reset
        document.getElementById('resetAll')?.addEventListener('click', () => {
            document.getElementById('quickFilter').value = '';
            gridApi.setGridOption('quickFilterText', '');
        });

        // Excel Export (manual using XLSX – works perfectly)
        // document.getElementById('exportExcel')?.addEventListener('click', () => {
        //     const rows = [];
        //     const exportColumns = columnDefs.flatMap(col => {
        //         if (col.children) {
        //             return col.children.map(child => child.headerName);
        //         }
        //         return [col.headerName];
        //     });

        //     gridApi.forEachNodeAfterFilterAndSort(node => {
        //         const row = {};
        //         let i = 0;
        //         const flatten = (cols) => {
        //             cols.forEach(c => {
        //                 if (c.children) {
        //                     flatten(c.children);
        //                 } else {
        //                     row[exportColumns[i]] = node.data[c.field] ?? '';
        //                     i++;
        //                 }
        //             });
        //         };
        //         flatten(columnDefs);
        //         rows.push(row);
        //     });

        //     const worksheet = XLSX.utils.json_to_sheet(rows);
        //     const workbook = XLSX.utils.book_new();
        //     XLSX.utils.book_append_sheet(workbook, worksheet, 'Consolidated Booking');
        //     XLSX.writeFile(workbook, 'Consolidated_Booking_Report_' + new Date().toISOString().slice(0,10) + '.xlsx');
        // });
        document.getElementById('exportExcel')?.addEventListener('click', () => {
    // 1. Get all visible/sorted/filtered rows in flat order
    const rows = [];
    gridApi.forEachNodeAfterFilterAndSort(node => {
        // if (node.group) return; // skip group header rows if you have row grouping
        rows.push({ ...node.data }); // shallow copy of the data object
    });

    // 2. Prepare headers in correct left-to-right order
    const headers = [];
    const fieldOrder = [];

    const collectLeafColumns = (cols) => {
        cols.forEach(col => {
            if (col.children) {
                collectLeafColumns(col.children);
            } else if (col.field) { // only real data columns
                headers.push(col.headerName);
                fieldOrder.push(col.field);
            }
        });
    };
    collectLeafColumns(columnDefs);

    // 3. Re-order each row's data according to the column order
    const orderedRows = rows.map(row => {
        const ordered = {};
        fieldOrder.forEach((field, index) => {
            ordered[headers[index]] = row[field] ?? '';
        });
        return ordered;
    });

    // 4. Create worksheet
    const worksheet = XLSX.utils.json_to_sheet(orderedRows, { header: headers });

    // Optional: auto-size columns (nice to have)
    const colWidths = headers.map((h, i) => {
        let maxLen = h.length;
        orderedRows.forEach(r => {
            const val = String(r[h] || '');
            if (val.length > maxLen) maxLen = val.length;
        });
        return { wch: Math.min(Math.max(maxLen + 2, 10), 60) };
    });
    worksheet['!cols'] = colWidths;

    // 5. Export
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Booking Report');
    const fileName = `Consolidated_Booking_Report_${new Date().toISOString().slice(0,10)}.xlsx`;
    XLSX.writeFile(workbook, fileName);
});

        // PDF Export
        document.getElementById('exportPdf')?.addEventListener('click', () => {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });

            doc.setFontSize(16);
            doc.text('Consolidated Booking Report', 40, 40);

            const exportColumns = [];
            const flattenColumns = (cols) => {
                cols.forEach(col => {
                    if (col.children) {
                        flattenColumns(col.children);
                    } else {
                        exportColumns.push({ header: col.headerName, dataKey: col.field });
                    }
                });
            };
            flattenColumns(columnDefs);

            const body = [];
            gridApi.forEachNodeAfterFilterAndSort(node => {
                body.push(node.data);
            });

            doc.autoTable({
                head: [exportColumns.map(c => c.header)],
                body: body,
                startY: 60,
                styles: { fontSize: 8, cellPadding: 4, overflow: 'linebreak' },
                headStyles: { fillColor: [40, 167, 69], textColor: 255 },
                alternateRowStyles: { fillColor: [245, 245, 245] },
                margin: { top: 60, left: 30, right: 30 },
            });

            doc.save('Consolidated_Booking_Report.pdf');
        });
    });
</script>
@endpush
