{{-- This file controls TOP HEADER (horizontal) + SIDEBAR (mobile) --}}

{{-- DASHBOARD --}}
<x-backpack::menu-item title="{{ trans('backpack::base.dashboard') }}" icon="la la-home"
    :link="backpack_url('dashboard')" />

{{-- USERS --}}
<x-backpack::menu-item title="Users" icon="la la-users" :link="backpack_url('user')" />

{{-- SALES MAIN DROPDOWN --}}
<x-backpack::menu-dropdown title="Sales" icon="la la-chart-line">

    {{-- Separator --}}
    <x-backpack::menu-separator title="Sales Configuration" />

    {{-- Price List --}}
    @if(auth()->check() && (auth()->user()->hasPermissionTo('can_view_documents') || auth()->user()->hasRole('super
    admin')))
    <x-backpack::menu-dropdown-item title="Price List" icon="la la-tag" :link="backpack_url('pricing')" />
    @endif

    {{-- Enquiries --}}
    <x-backpack::menu-dropdown title="Enquiries" icon="la la-question-circle" nested="true">
        <x-backpack::menu-dropdown-item title="Add Hot Enquiry" icon="la la-plus-circle"
            :link="backpack_url('enquiries/add-hot-enquiry')" />
        <x-backpack::menu-dropdown-item title="Hot Enquiry List" icon="la la-list"
            :link="backpack_url('enquiries/hot-enquiry-list')" />
        <x-backpack::menu-dropdown-item title="Unassigned Enquiries" icon="la la-user-times"
            :link="backpack_url('enquiries/unassigned-enquiries')" />
    </x-backpack::menu-dropdown>

    {{-- Booking (fully kept with all your items) --}}
    <x-backpack::menu-dropdown title="Booking" icon="la la-book-open" nested="true">

        <x-backpack::menu-dropdown-item title="Add New Booking" icon="la la-plus-circle"
            :link="backpack_url('booking/create')" />

        <x-backpack::menu-dropdown-item title="Booking List" icon="la la-list" :link="backpack_url('booking')" />

        <x-backpack::menu-separator title="Pending Stages" />
        <x-backpack::menu-dropdown-item title="Pending DMS Booking" icon="la la-database"
            :link="backpack_url('booking/pending-dms')" />

        <x-backpack::menu-dropdown-item title="Pending Sales Order" icon="la la-file-alt"
            :link="backpack_url('booking/pending/sales-order')" />

        <x-backpack::menu-dropdown-item title="Pending KYC" icon="la la-id-card"
            :link="backpack_url('booking/pending-kyc')" />
        
        <x-backpack::menu-dropdown-item title="Pending Payment" icon="la la-rupee-sign"
            :link="backpack_url('booking/pending-payment')" />


        <x-backpack::menu-dropdown-item title="Pending Invoices" icon="la la-file-invoice"
            :link="backpack_url('booking/pending-invoices')" />
        <x-backpack::menu-dropdown-item title="Pending Insurance" icon="la la-shield-alt"
            :link="backpack_url('booking/pending-insurance')" />
        <x-backpack::menu-dropdown-item title="Pending RTO" icon="la la-car"
            :link="backpack_url('booking/pending-rto')" />
        <x-backpack::menu-dropdown-item title="Pending Deliveries" icon="la la-truck"
            :link="backpack_url('booking/pending-deliveries')" />
        <x-backpack::menu-dropdown-item title="Pending Reg. No." icon="la la-hashtag"
            :link="backpack_url('booking/pending-registration')" />
        <x-backpack::menu-dropdown-item title="Pending DO" icon="la la-file-signature"
            :link="backpack_url('booking/pending-do')" />


        <x-backpack::menu-dropdown-item title="Dummy Bookings" icon="la la-flask"
            :link="backpack_url('booking/dummy')" />

        <x-backpack::menu-dropdown-item title="Erroneous Entries" icon="la la-exclamation-circle"
            :link="backpack_url('booking/errors')" />
    </x-backpack::menu-dropdown>

    {{-- Exchange --}}
    <x-backpack::menu-dropdown title="Exchange" icon="la la-exchange-alt" nested="true">
        <x-backpack::menu-dropdown title="Enquiry Stage" icon="la la-question-circle" nested="true">
            <x-backpack::menu-dropdown-item title="Int in Exchange" icon="la la-check"
                :link="backpack_url('exchange/enquiry/int-in-exchange')" />
            <x-backpack::menu-dropdown-item title="Int in Scrappage" icon="la la-recycle"
                :link="backpack_url('exchange/enquiry/int-in-scrappage')" />
            <x-backpack::menu-dropdown-item title="Not Interested" icon="la la-thumbs-down"
                :link="backpack_url('exchange/enquiry/not-interested')" />
        </x-backpack::menu-dropdown>

        <x-backpack::menu-dropdown title="Booking Stage" icon="la la-book-open" nested="true">
            <x-backpack::menu-dropdown-item title="Int in Exchange"
                :link="backpack_url('booking/exchange')" />
            <x-backpack::menu-dropdown-item title="Int in Scrappage"
                :link="backpack_url('booking/scrappage')" />
            <x-backpack::menu-dropdown-item title="Not Interested"
                :link="backpack_url('booking/exchange/not-interested')" />
        </x-backpack::menu-dropdown>
    </x-backpack::menu-dropdown>

    {{-- Finance (now with valid dummy links) --}}

    <x-backpack::menu-dropdown title="Finance" icon="la la-money-bill" nested="true">
        <x-backpack::menu-dropdown title="Enquiry Stage" icon="la la-question-circle" nested="true">
            <x-backpack::menu-dropdown-item title="Int in Finance" icon="la la-check"
                :link="backpack_url('finance/enquiry/int-in-finance')" />
            <x-backpack::menu-dropdown-item title="Not Interested" icon="la la-thumbs-down"
                :link="backpack_url('finance/enquiry/not-interested')" />
        </x-backpack::menu-dropdown>

        <x-backpack::menu-dropdown title="Booking Stage" icon="la la-book-open" nested="true">
            <x-backpack::menu-dropdown-item title="Int in Finance"
                :link="backpack_url('booking/finance')" />
            <x-backpack::menu-dropdown-item title="Not Interested"
                :link="backpack_url('booking/finance/not-interested')" />
            <x-backpack::menu-dropdown-item title="Retail" :link="backpack_url('booking/finance/retail')" />
            <x-backpack::menu-dropdown-item title="Payout" :link="backpack_url('finance/payout')" />
        </x-backpack::menu-dropdown>
    </x-backpack::menu-dropdown>


    {{-- Refund --}}
    <x-backpack::menu-dropdown title="Refund" icon="la la-undo" nested="true">
        <x-backpack::menu-dropdown title="Bookings" icon="la la-book-open" nested="true">
            <x-backpack::menu-dropdown-item title="Requested" :link="backpack_url('booking/refund/requested')" />
            <x-backpack::menu-dropdown-item title="Refunded" :link="backpack_url('booking/refunded')" />
            <x-backpack::menu-dropdown-item title="Rejected" :link="backpack_url('booking/rejected')" />
        </x-backpack::menu-dropdown>

        <x-backpack::menu-dropdown title="Customer Recon" icon="la la-users-cog" nested="true">
            <x-backpack::menu-dropdown title="Sales" icon="la la-chart-line" nested="true">
                <x-backpack::menu-dropdown-item title="Requested" :link="backpack_url('refund/sales/requested')" />
                <x-backpack::menu-dropdown-item title="Refunded" :link="backpack_url('refund/sales/refunded')" />
                <x-backpack::menu-dropdown-item title="Rejected" :link="backpack_url('refund/sales/rejected')" />
            </x-backpack::menu-dropdown>
            <!-- You can add Service section similarly if needed -->
        </x-backpack::menu-dropdown>
    </x-backpack::menu-dropdown>

    {{-- Co Dealer Transactions --}}

    <x-backpack::menu-dropdown title="Co Dealer Transactions" icon="la la-handshake" nested="true">
        <x-backpack::menu-dropdown-item title="Add Co Dealer" icon="la la-user-plus"
            :link="backpack_url('co-dealer/add')" />
        <x-backpack::menu-dropdown-item title="Add New Request" icon="la la-plus-circle"
            :link="backpack_url('co-dealer/new-request')" />
        <x-backpack::menu-dropdown-item title="Pending Requests" icon="la la-clock"
            :link="backpack_url('co-dealer/pending-requests')" />
        <x-backpack::menu-dropdown-item title="Approved Requests" icon="la la-check-circle"
            :link="backpack_url('co-dealer/approved')" />
        <x-backpack::menu-dropdown-item title="Pending Transactions" icon="la la-exchange-alt"
            :link="backpack_url('co-dealer/pending-transactions')" />
        <x-backpack::menu-dropdown-item title="Pending Payment" icon="la la-money-bill-wave"
            :link="backpack_url('co-dealer/pending-payment')" />
        <x-backpack::menu-dropdown-item title="Dealer Ledger" icon="la la-book"
            :link="backpack_url('co-dealer/ledger')" />
    </x-backpack::menu-dropdown>


    {{-- Other (Fees) --}}
    @can(['create_fee_collection', 'verify_fee_collection'])
    <x-backpack::menu-dropdown title="Other" icon="la la-file-invoice-dollar" nested="true">
        <x-backpack::menu-dropdown title="Fee Collection" icon="la la-dollar-sign" nested="true">
            <x-backpack::menu-dropdown title="Registration" icon="la la-registered" nested="true">
                <x-backpack::menu-dropdown-item title="Add Fee" icon="la la-plus-circle"
                    :link="backpack_url('fee-collection/add')" />
                <x-backpack::menu-dropdown-item title="View List" icon="la la-list-ul"
                    :link="backpack_url('fee-collection')" />
            </x-backpack::menu-dropdown>
        </x-backpack::menu-dropdown>
    </x-backpack::menu-dropdown>
    @endcan

    {{-- Reports --}}

    <x-backpack::menu-dropdown title="Reports" icon="la la-file-alt" nested="true">
        <x-backpack::menu-dropdown title="Stock" icon="la la-boxes" nested="true">
            <x-backpack::menu-dropdown-item title="Current Stock" :link="backpack_url('reports/stock')" />
            <x-backpack::menu-dropdown-item title="Live Order" :link="backpack_url('reports/live-order')" />
        </x-backpack::menu-dropdown>
        <x-backpack::menu-dropdown title="Booking" icon="la la-book" nested="true">
            <x-backpack::menu-dropdown-item title="Consolidated Booking"
                :link="backpack_url('reports/consolidated-booking')" />
            <x-backpack::menu-dropdown-item title="Branch Booking" :link="backpack_url('reports/branch-booking')" />
            <x-backpack::menu-dropdown-item title="Pending Actions" :link="backpack_url('reports/pending-actions')" />
        </x-backpack::menu-dropdown>
        <!-- Add more report sections as needed -->
    </x-backpack::menu-dropdown>


</x-backpack::menu-dropdown>