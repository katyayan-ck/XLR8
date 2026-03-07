// resources/js/ag-grid-controls.js

// Ye file sirf buttons ko handle karti hai
// gridApi ko blade se window.gridApi ke through access karegi

document.addEventListener('DOMContentLoaded', function () {
    // Thoda wait karte hain taaki AG Grid initialize ho jaye
    setTimeout(() => {
        const gridApi = window.gridApi;

        if (!gridApi) {
            console.warn("AG Grid API not found. Make sure grid is initialized before this script runs.");
            return;
        }

        // ─── 1. All Headers ───────────────────────────────────────
        document.getElementById('btnAllHeaders')?.addEventListener('click', () => {
            const allColumnIds = gridApi.getColumns().map(col => col.getColId());
            gridApi.setColumnsVisible(allColumnIds, true);
            gridApi.sizeColumnsToFit();
        });

        // ─── 2. Default Headers ───────────────────────────────────
        document.getElementById('btnDefaultHeaders')?.addEventListener('click', () => {
            const allColumnIds = gridApi.getColumns().map(col => col.getColId());

            // ─── Yahan default visible columns define karo ─────────
            // In field names ko apne actual columnDefs ke 'field' se match karna
            // Niche example diya hai – tum apne hisab se badal dena
            const defaultVisibleFields = [
                'sno',                      // S.No.
                'segment', 'model', 'variant', 'color',          // Vehicle Info
                'total_bookings', 'bkn', 'chr',                  // Bookings
                'order_verif', 'order_creation', 'booking_creation', // Pending Actions (important ones)
                // 'kyc_data', 'book_canc', 'refund'             // agar ye bhi default mein chahiye to add kar dena
            ];

            gridApi.setColumnsVisible(allColumnIds, false);           // sab hide
            gridApi.setColumnsVisible(defaultVisibleFields, true);    // sirf ye visible
            gridApi.sizeColumnsToFit();
        });

        // ─── 3. Customise Headers (Tool Panel toggle) ─────────────
        document.getElementById('btnCustomiseHeaders')?.addEventListener('click', () => {
            if (gridApi.isToolPanelShowing()) {
                gridApi.closeToolPanel();
            } else {
                gridApi.openToolPanel('columns');
            }
        });

        // ─── Optional: Save / Reset column state (localStorage) ───
        const STORAGE_KEY = `ag-grid-column-state-${window.location.pathname}`;

        // Save button (agar blade mein button daala hai to)
        document.getElementById('btnSaveState')?.addEventListener('click', () => {
            const state = gridApi.getColumnState();
            localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
            alert("Current column layout saved!");
        });

        // Reset button
        document.getElementById('btnResetState')?.addEventListener('click', () => {
            localStorage.removeItem(STORAGE_KEY);
            location.reload();
        });

        // Page load pe saved state apply karo
        const savedState = localStorage.getItem(STORAGE_KEY);
        if (savedState) {
            try {
                const state = JSON.parse(savedState);
                gridApi.applyColumnState({
                    state: state,
                    applyOrder: true,
                });
            } catch (err) {
                console.warn("Failed to restore saved column state:", err);
            }
        }

    }, 100); // 100ms delay → mostly kaafi hota hai
});
