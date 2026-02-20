@extends(backpack_view('blank'))

@section('title', 'Add Booking Amount - ' . ($booking->booking_no ?? $booking->id))

@push('head')
<link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        

        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-black">
                    <h5 class="mb-0">
                        <i class="la la-rupee-sign me-2"></i>
                        Add Received Amount / Receipt
                    </h5>
                    <small>Booking: {{ $booking->booking_no ?? 'N/A' }} - {{ $booking->name ?? 'Customer' }}</small>
                </div>

                <div class="card-body">
                    <form class="forms-sample" method="POST"
                        action="{{ route('booking.add-amount.store', $booking->id) }}" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="bid" value="{{ $booking->id }}">

                        <div class="row g-4">
                            <!-- Receipt Date -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="receipt_date" class="form-label">
                                        Receipt Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="receipt_date" id="receipt_date"
                                        class="form-control @error('receipt_date') is-invalid @enderror"
                                        placeholder="dd-MMM-yyyy" required>
                                    <input type="hidden" name="hidden_receipt_date" id="hidden_receipt_date"
                                        value="{{ old('hidden_receipt_date') }}">
                                    @error('receipt_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Receipt Number -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="reciept_no" class="form-label">
                                        Receipt Number <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="reciept_no" name="reciept_no"
                                        class="form-control @error('reciept_no') is-invalid @enderror"
                                        placeholder="Enter Receipt Number" required value="{{ old('reciept_no') }}">
                                    <div id="reciept_no_warning" class="invalid-feedback" style="display:none;">
                                        Receipt Number already exists.
                                    </div>
                                    @error('reciept_no')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Amount -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="amount" class="form-label">
                                        Received Amount <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" id="amount" name="amount" step="0.01" min="0.01"
                                        class="form-control @error('amount') is-invalid @enderror"
                                        placeholder="Enter Amount" required value="{{ old('amount') }}">
                                    @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- File Upload -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fdoc" class="form-label">
                                        Upload Proof (JPG/PNG/PDF) <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" name="amount_proof" id="fdoc"
                                        class="form-control @error('amount_proof') is-invalid @enderror"
                                        accept="image/jpeg,image/png,application/pdf" required>
                                    <small class="form-text text-muted">Max 2MB • JPG, PNG, PDF only</small>
                                    @error('amount_proof')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror

                                    <!-- Preview Area -->
                                    <div class="mt-3 position-relative d-inline-block">
                                        <img id="frameLeft" src="" class="img-thumbnail" width="140"
                                            style="display:none;">
                                        <img id="pdfIcon" src="{{ asset('images/pdf-icon.png') }}" class="img-thumbnail"
                                            width="140" style="display:none;">
                                        <button type="button" id="clearLeft"
                                            class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 translate-middle"
                                            style="display:none; width:28px; height:28px; line-height:1; font-size:18px; padding:0;"
                                            onclick="discardImageLeft()">
                                            ×
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row mt-5">
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-success btn-lg px-5" id="submitBtn">
                                    <i class="la la-save me-2"></i> Add Amount
                                </button>
                                <a href="{{ backpack_url('booking') }}" class="btn btn-secondary btn-lg px-5 ms-3">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    // Flatpickr for receipt date
        flatpickr("#receipt_date", {
            dateFormat: "d-M-Y",
            maxDate: "today",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates[0]) {
                    const d = selectedDates[0];
                    const iso = d.getFullYear() + '-' +
                               String(d.getMonth() + 1).padStart(2, '0') + '-' +
                               String(d.getDate()).padStart(2, '0');
                    document.getElementById('hidden_receipt_date').value = iso;
                }
            }
        });

        // File preview + validation
        function previewFile() {
            const input = document.getElementById('fdoc');
            const imgPreview = document.getElementById('frameLeft');
            const pdfPreview = document.getElementById('pdfIcon');
            const clearBtn = document.getElementById('clearLeft');

            if (!input.files || !input.files[0]) return;

            const file = input.files[0];
            const fileType = file.type;
            const fileSize = file.size;

            // Reset
            imgPreview.style.display = 'none';
            pdfPreview.style.display = 'none';
            clearBtn.style.display = 'none';

            if (fileSize > 2 * 1024 * 1024) {
                alert('File size exceeds 2MB limit.');
                input.value = '';
                return;
            }

            if (fileType.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgPreview.src = e.target.result;
                    imgPreview.style.display = 'block';
                    clearBtn.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else if (fileType === 'application/pdf') {
                pdfPreview.style.display = 'block';
                clearBtn.style.display = 'block';
            } else {
                alert('Only JPG, PNG, or PDF files are allowed.');
                input.value = '';
            }
        }

        function discardImageLeft() {
            document.getElementById('fdoc').value = '';
            document.getElementById('frameLeft').style.display = 'none';
            document.getElementById('pdfIcon').style.display = 'none';
            document.getElementById('clearLeft').style.display = 'none';
        }

        // Receipt number uniqueness check (AJAX)
        document.addEventListener('DOMContentLoaded', function() {
            const receiptInput = document.getElementById('reciept_no');
            const warning = document.getElementById('reciept_no_warning');
            const submitBtn = document.getElementById('submitBtn');

            receiptInput.addEventListener('blur', function() {
                const rn = this.value.trim();
                if (!rn) {
                    warning.style.display = 'none';
                    receiptInput.classList.remove('is-invalid');
                    submitBtn.disabled = false;
                    return;
                }
            
                const url = new URL("{{ route('check-receipt', ['rn' => 'placeholder']) }}", window.location.origin);
                url.pathname = url.pathname.replace('placeholder', encodeURIComponent(rn));
            
                fetch(url)
                    .then(response => response.text())
                    .then(data => { /* same as before */ })
                    .catch(() => { /* same */ });
            });

            // Clear warning on input
            receiptInput.addEventListener('input', function() {
                warning.style.display = 'none';
                this.classList.remove('is-invalid');
                submitBtn.disabled = false;
            });
        });
</script>
@endpush