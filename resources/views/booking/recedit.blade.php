@extends(backpack_view('blank'))

@section('title', 'Edit Receipt')

@section('header')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection

@section('content')
<div class="container-fluid">
    @include(backpack_view('inc.alerts'))

    <h2 class="mb-4">Edit Receipt</h2>

    <div class="card">
        <div class="card-header">
            <h3>Receipt Details</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('receipt.update', ['bookingId' => $booking_id, 'receiptId' => $receipt_id]) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    <!-- Receipt / Voucher No. -->
                    <div class="col-sm-4">
                        <label for="reciept_no">Receipt No. <span class="text-danger">*</span></label>
                        <input type="text" name="reciept" id="reciept_no" class="form-control"
                               value="{{ old('reciept', $entry->reciept) }}" required>
                        <div id="reciept_no_warning" class="text-danger mt-1" style="display:none;">
                            Receipt No already exists
                        </div>
                        <input type="hidden" name="booking_id" value="{{ $entry->bid }}">
                        <input type="hidden" name="receipt_id" value="{{ $entry->id }}">
                    </div>

                    <!-- Date -->
                    <div class="col-sm-4">
                        <label for="date_picker">Date <span class="text-danger">*</span></label>
                        <input type="text" name="date" id="date_picker" class="form-control flatpickr"
                               value="{{ old('date', \Carbon\Carbon::parse($entry->date)->format('d-M-Y')) }}" required>
                    </div>

                    <!-- Amount -->
                    <div class="col-sm-4">
                        <label for="amount">Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="amount" class="form-control"
                               value="{{ old('amount', $entry->amount) }}" step="0.01" required>
                    </div>

                    <!-- Proof Upload + Preview -->
                    <div class="col-sm-6">
                        <label>Current Proof</label>
                        <div style="margin-top:8px;">
                            @if($entry->getFirstMediaUrl('amount-proof'))
                                @php
                                    $media = $entry->getFirstMedia('amount-proof');
                                    $isPdf = str_contains($media->mime_type ?? '', 'pdf');
                                @endphp
                                <div class="d-inline-block position-relative">
                                    @if($isPdf)
                                        <img src="{{ asset('images/pdf-icon.png') }}" width="100" alt="PDF">
                                    @else
                                        <img src="{{ $entry->getFirstMediaUrl('amount-proof') }}" class="img-thumbnail" width="140" alt="Proof">
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">No proof uploaded</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <label for="amount_proof">Replace Proof (optional)</label>
                        <input type="file" name="amount_proof" id="amount_proof" class="form-control mt-2"
                               accept="image/jpeg,image/png,application/pdf" onchange="previewFile(event)">
                        <small class="text-muted">JPG, PNG, PDF (max 2MB)</small>

                        <div id="previewContainer" class="mt-3" style="display:none;">
                            <img id="imagePreview" class="img-thumbnail" width="140" style="display:none;">
                            <img id="pdfIcon" src="{{ asset('images/pdf-icon.png') }}" width="100" style="display:none;">
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary px-4">Update Receipt</button>
                        <button type="submit" name="action" value="delete" class="btn btn-danger px-4 ms-2"
                                onclick="return confirm('Are you sure you want to delete this receipt? This action cannot be undone.')">
                            Delete Receipt
                        </button>
                        <a href="{{ backpack_url('booking/' . $booking_id) }}" class="btn btn-secondary px-4 ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('after_scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
$(document).ready(function() {

    // Flatpickr
    flatpickr("#date_picker", {
        dateFormat: "d-M-Y",
        maxDate: "today",
        allowInput: true
    });

    // Preview new file
    function previewFile(event) {
        const file = event.target.files[0];
        if (!file) return;

        const preview = document.getElementById('imagePreview');
        const pdfIcon = document.getElementById('pdfIcon');
        const container = document.getElementById('previewContainer');

        container.style.display = 'block';

        if (file.type.startsWith('image/')) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
            pdfIcon.style.display = 'none';
        } else if (file.type === 'application/pdf') {
            preview.style.display = 'none';
            pdfIcon.style.display = 'block';
        }
    }

    // Receipt number duplicate check – same as add.blade.php
    const originalReceipt = $('#reciept_no').val();

    $('#reciept_no').on('change', function() {
        const val = $(this).val().trim();

        if (!val || val === originalReceipt) {
            $('#reciept_no_warning').hide();
            $(this).removeClass('is-invalid');
            return;
        }

        $.ajax({
            url: '{{ url("/admin/check-receipt") }}/' + encodeURIComponent(val),
            method: 'GET',
            success: function(data) {
                if (data !== 0) {
                    $('#reciept_no_warning').show();
                    $('#reciept_no').addClass('is-invalid');
                } else {
                    $('#reciept_no_warning').hide();
                    $('#reciept_no').removeClass('is-invalid');
                }
            },
            error: function() {
                alert('Error checking receipt number. Please try again.');
            }
        });
    });

    // Optional: real-time input cleanup
    $('#reciept_no').on('input', function() {
        $('#reciept_no_warning').hide();
        $(this).removeClass('is-invalid');
    });
});
</script>
@endpush