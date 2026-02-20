@extends(backpack_view('blank'))

@section('header')
<section class="container-fluid">
    <h2>
        <i class="la la-shield text-warning"></i> Edit Insurance
        <small>Booking #{{ $booking->id }} — {{ $booking->name ?? 'N/A' }}</small>
    </h2>
</section>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-gradient-warning d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0 fw-bold text-dark">
                    Insurance Details — Booking #{{ $booking->id }}
                </h3>
                <a href="{{ url()->previous() }}" class="btn btn-dark btn-sm">
                    <i class="la la-arrow-left"></i> Back
                </a>
            </div>

            <div class="card-body">
                @include(backpack_view('inc.alerts'))
                <!-- or your custom alerts -->


                <!-- ──────────────────────────────────────────────── -->
                <!--       Booking Information (View Only) Card         -->
                <!--       सबसे ऊपर - context के लिए महत्वपूर्ण        -->
                <!-- ──────────────────────────────────────────────── -->
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-gradient-light text-dark d-flex align-items-center">
                        <i class="la la-info-circle me-2 text-primary fs-4"></i>
                        <h5 class="mb-0 fw-semibold">
                            Booking Information (View Only) #{{ $booking->id }}
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        <dl class="row g-4 mb-0">
                            <!-- Row 1: Basic Info -->
                            <div class="col-12">
                                <div class="row g-4 border-bottom pb-3 mb-3">
                                    <div class="col-md-3 col-sm-6">
                                        <dt class="text-muted small fw-medium mb-1">Booking Date</dt>
                                        <dd class="fs-5 fw-semibold mb-0 text-dark">
                                            {{ $booking->booking_date
                                            ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y')
                                            : '<span class="text-muted">—</span>' }}
                                        </dd>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                        <dt class="text-muted small fw-medium mb-1">Customer Name</dt>
                                        <dd class="fs-5 fw-semibold mb-0 text-dark">
                                            {{ $booking->name ?? '<span class="text-muted">—</span>' }}
                                        </dd>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                        <dt class="text-muted small fw-medium mb-1">Branch</dt>
                                        <dd class="fs-5 fw-semibold mb-0 text-dark">
                                            {{ $booking->branch
                                            ? ($booking->branch->name ?? $booking->branch->abbr ?? '—')
                                            : '<span class="text-muted">—</span>' }}
                                        </dd>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                        <dt class="text-muted small fw-medium mb-1">Location</dt>
                                        <dd class="fs-5 fw-semibold mb-0 text-dark">
                                            @if($booking->location_id)
                                            {{ $booking->location?->name ?? '<span class="text-muted">—</span>' }}
                                            @else
                                            {{ $booking->location_other ?: '<span class="text-muted">—</span>' }}
                                            @endif
                                        </dd>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2: Vehicle Details -->
                            <div class="col-12">
                                <div class="row g-4 border-bottom pb-3 mb-3">
                                    <div class="col-md-3 col-sm-6">
                                        <dt class="text-muted small fw-medium mb-1">
                                            <i class="la la-car-side me-1 text-primary"></i> Model
                                        </dt>
                                        <dd class="fs-5 fw-semibold mb-0 text-dark">
                                            {{ $booking->model ?? '<span class="text-muted">—</span>' }}
                                        </dd>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                        <dt class="text-muted small fw-medium mb-1">
                                            <i class="la la-cogs me-1 text-primary"></i> Variant
                                        </dt>
                                        <dd class="fs-5 fw-semibold mb-0 text-dark">
                                            {{ $booking->variant ?? '<span class="text-muted">—</span>' }}
                                        </dd>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                        <dt class="text-muted small fw-medium mb-1">
                                            <i class="la la-palette me-1 text-primary"></i> Color
                                        </dt>
                                        <dd class="fs-5 fw-semibold mb-0 text-dark">
                                            {{ $booking->color ?? '<span class="text-muted">—</span>' }}
                                        </dd>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                        <dt class="text-muted small fw-medium mb-1">
                                            <i class="la la-key me-1 text-primary"></i> Chassis No.
                                        </dt>
                                        <dd class="fs-5 fw-semibold mb-0 text-dark">
                                            {{ $booking->chassis_no ?? $booking->chasis_no ?? '<span
                                                class="text-muted">—</span>' }}
                                        </dd>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 3: Invoice Details (Extra fields as requested) -->
                            <div class="col-12">
                                <div class="row g-4">
                                    <div class="col-md-4 col-sm-6">
                                        <dt class="text-muted small fw-medium mb-1">
                                            <i class="la la-file-invoice me-1 text-primary"></i> Invoice Number
                                        </dt>
                                        <dd class="fs-5 fw-semibold mb-0 text-dark">
                                            {{ $booking->inv_no ?? $booking->dms_invoice_number ?? '<span
                                                class="text-muted">—</span>' }}
                                        </dd>
                                    </div>

                                    <div class="col-md-4 col-sm-6">
                                        <dt class="text-muted small fw-medium mb-1">
                                            <i class="la la-calendar-check me-1 text-primary"></i> Invoice Date
                                        </dt>
                                        <dd class="fs-5 fw-semibold mb-0 text-dark">
                                            {{ $booking->inv_date
                                            ? \Carbon\Carbon::parse($booking->inv_date)->format('d M Y')
                                            : '<span class="text-muted">—</span>' }}
                                        </dd>
                                    </div>

                                    <!-- अगर Dealer Invoice भी दिखाना हो तो यहाँ तीसरा column जोड़ सकते हैं -->
                                    <!-- <div class="col-md-4 col-sm-6">
                                <dt class="text-muted small fw-medium mb-1">Dealer Invoice No.</dt>
                                <dd class="fs-5 fw-semibold mb-0 text-dark">
                                    {{ $booking->dealer_inv_no ?? '<span class="text-muted">—</span>' }}
                                </dd>
                            </div> -->
                                </div>
                            </div>
                        </dl>
                    </div>
                </div>

                <form method="POST" action="{{ route('booking.insurance.update', $booking->id) }}"
                    enctype="multipart/form-data" class="row g-3">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                    <!-- Row 1 -->
                    <div class="col-md-4">
                        <label class="form-label">Insurance Source <span class="text-danger">*</span></label>
                        <select name="insurance_category" class="form-control">
                            <option value="">Select Source</option>
                            <option value="1" {{ old('insurance_category', $insurance?->source ?? '') == 1 ? 'selected'
                                : '' }}>By Dealer (OEM Portal)</option>
                            <option value="2" {{ old('insurance_category', $insurance?->source ?? '') == 2 ? 'selected'
                                : '' }}>By Dealer (Agency)</option>
                            <option value="3" {{ old('insurance_category', $insurance?->source ?? '') == 3 ? 'selected'
                                : '' }}>By Owner (Self)</option>
                        </select>
                        @error('insurance_category') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Insurance Company <span class="text-danger">*</span></label>
                        <select name="insurance_company" id="insurance_company" class="form-control">
                            <option value="">Select Company</option>
                            @foreach ($data['insurances'] ?? [] as $ins)
                            <option value="{{ $ins['id'] }}" data-short="{{ $ins['short_name'] ?? '' }}" {{
                                old('insurance_company', $insurance?->insurer ?? '') == $ins['id'] ? 'selected' : '' }}>
                                {{ $ins['name'] ?? 'N/A' }}
                            </option>
                            @endforeach
                        </select>
                        @error('insurance_company') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Short Name</label>
                        <input type="text" id="insurance_short_name" class="form-control" readonly>
                    </div>

                    <!-- Row 2 -->
                    <div class="col-md-4">
                        <label class="form-label">Policy No. <span class="text-danger">*</span></label>
                        <input type="text" name="policy_no" class="form-control text-uppercase"
                            value="{{ old('policy_no', $insurance?->pol_no ?? '') }}" maxlength="25">
                        @error('policy_no') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Policy Date <span class="text-danger">*</span></label>
                        <input type="text" name="policy_date" id="policy_date" class="form-control flatpickr"
                            value="{{ old('policy_date', $insurance?->pol_date ? Carbon::parse($insurance->pol_date)->format('d-M-Y') : '') }}">
                        <!-- This hidden field MUST exist -->
                        <input type="hidden" name="hidden_policy_date" id="hidden_policy_date"
                            value="{{ old('hidden_policy_date', $insurance?->pol_date ?? '') }}">
                        @error('hidden_policy_date') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Policy Type <span class="text-danger">*</span></label>
                        <select name="policy_type" class="form-control">
                            <option value="">Select Type</option>
                            <option value="1" {{ old('policy_type', $insurance?->pol_type ?? '') == 1 ? 'selected' : ''
                                }}>Normal</option>
                            <option value="2" {{ old('policy_type', $insurance?->pol_type ?? '') == 2 ? 'selected' : ''
                                }}>Nil Dep</option>
                            <option value="3" {{ old('policy_type', $insurance?->pol_type ?? '') == 3 ? 'selected' : ''
                                }}>Nil Dep + Cons.</option>
                            <option value="4" {{ old('policy_type', $insurance?->pol_type ?? '') == 4 ? 'selected' : ''
                                }}>Nil Dep + Cons. + Extra Add-On</option>
                        </select>
                        @error('policy_type') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- File Upload -->
                    <div class="col-md-6">
                        <label class="form-label">Policy Copy <span class="text-danger">*</span></label>
                        <input type="file" name="policy_copy" class="form-control" accept=".pdf">
                        @error('policy_copy') <span class="text-danger small">{{ $message }}</span> @enderror

                        @if ($insurance && $insurance->hasMedia('policy_copy'))
                        <div class="mt-2">
                            @php $media = $insurance->getFirstMedia('policy_copy'); @endphp
                            <a href="{{ $media->getUrl() }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="la la-file-pdf"></i> View Current Policy
                            </a>
                        </div>
                        @endif
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-success px-5 py-2">
                            <i class="la la-save"></i> Save Insurance
                        </button>
                        <a href="{{ route('booking.pending-insurance') }}" class="btn btn-secondary px-5 py-2 ms-3">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after_styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('after_scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    flatpickr('#policy_date', {
        dateFormat: "d-M-Y",
        maxDate: "today",
        onChange: function(selectedDates, dateStr, instance) {
            // Fill hidden field in Y-m-d format
            const hidden = document.getElementById('hidden_policy_date');
            if (selectedDates[0] && hidden) {
                hidden.value = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
            }
        }
    });

    // Optional: trigger change on load if there's a default value
    const policyDateInput = document.getElementById('policy_date');
    if (policyDateInput && policyDateInput.value) {
        policyDateInput.dispatchEvent(new Event('change'));
    }

    // Short name auto-fill (if you still have it)
    const companySelect = document.getElementById('insurance_company');
    const shortInput = document.getElementById('insurance_short_name');
    if (companySelect && shortInput) {
        companySelect.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            shortInput.value = opt.dataset.short || '';
        });
        companySelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush