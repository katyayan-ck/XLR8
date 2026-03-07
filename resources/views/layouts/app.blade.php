<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    @stack('before_styles')
</head>

<body>

    @yield('content')

    {{-- ================= GLOBAL FILE PREVIEW MODAL ================= --}}
    <div class="modal fade" id="filePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">File Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">

                    <img id="modalImagePreview" class="img-fluid"
                        style="display:none; max-height:70vh; object-fit:contain;">

                    <iframe id="modalPdfPreview" style="width:100%; height:70vh; display:none;"
                        frameborder="0"></iframe>

                </div>

                <div class="modal-footer">
                    <a id="downloadFileBtn" class="btn btn-success" download>
                        Download
                    </a>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- ================= GLOBAL FILE CHIP SCRIPT ================= --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
        console.log("Global file chip script loaded");

        // FILE INPUT CHANGE - नई file upload पर chip बनाए
        document.body.addEventListener("change", function (e) {
            if (!e.target.classList.contains("file-input")) return;

            const file = e.target.files[0];
            if (!file) return;

            // 2MB validation
            if (file.size > 2 * 1024 * 1024) {
                alert("File must be less than 2MB");
                e.target.value = "";
                return;
            }

            const previewId = e.target.dataset.preview;
            const previewDiv = document.getElementById(previewId);
            if (!previewDiv) return;

            const fileURL = URL.createObjectURL(file);

            previewDiv.innerHTML = `
                <span class="btn btn-primary file-chip"
                      data-file-url="${fileURL}"
                      data-file-name="${file.name}"
                      style="cursor:pointer">
                    📎 ${file.name}
                </span>
            `;
        });

        // CHIP CLICK - एक ही modal में सब खुले
        document.body.addEventListener("click", function (e) {
            const chip = e.target.closest(".file-chip");
            if (!chip) return;

            console.log("Chip clicked →", chip.dataset.fileName);

            const fileUrl  = chip.dataset.fileUrl;
            const fileName = chip.dataset.fileName || "File";

            if (!fileUrl) {
                console.warn("No file URL found on chip");
                return;
            }

            // एक ही modal ke elements
            const modalElement = document.getElementById("filePreviewModal");
            const img = document.getElementById("modalImagePreview");
            const pdf = document.getElementById("modalPdfPreview");
            const title = document.getElementById("modalTitle");
            const dlBtn = document.getElementById("downloadFileBtn");

            // Reset
            img.style.display = "none";
            pdf.style.display = "none";
            img.src = "";
            pdf.src = "";

            title.innerText = fileName;
            dlBtn.href = fileUrl;
            dlBtn.download = fileName;

            // PDF ya image check
            if (fileName.toLowerCase().endsWith(".pdf") || fileUrl.toLowerCase().includes(".pdf")) {
                pdf.src = fileUrl;
                pdf.style.display = "block";
            } else {
                img.src = fileUrl;
                img.style.display = "block";
            }

            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        });
    });
    </script>

    {{-- VERY IMPORTANT --}}
    @stack('after_scripts')

</body>

</html>