@props(['name','label','id','existing' => null])

<div class="col-md-3">
    <label class="form-label fw-bold">{{ $label }}</label>

    <input type="file" name="{{ $name }}" id="{{ $id }}" class="form-control file-input-new"
        accept=".jpg,.jpeg,.png,.pdf" data-preview="{{ $id }}NewPreview">

    <small class="form-text text-muted">
        Only jpg, png or pdf (up to 2MB)
    </small>

    <div id="{{ $id }}NewPreview" class="mt-2">
        @if($existing)
        <span class="btn btn-primary file-chip-new" data-file-url="{{ asset('storage/'.$existing) }}"
            data-file-name="{{ basename($existing) }}" style="cursor:pointer">
            📎 {{ basename($existing) }}
        </span>
        @endif
    </div>
</div>