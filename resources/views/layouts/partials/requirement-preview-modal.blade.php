<div class="modal fade" id="requirementPreviewModal" tabindex="-1" role="dialog" aria-labelledby="requirementPreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 0.85rem; overflow: hidden;">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title" id="requirementPreviewLabel">Preview Berkas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" style="height: 72vh;">
                <iframe id="requirementPreviewFrame" src="about:blank" style="width: 100%; height: 100%; border: 0;"></iframe>
                <div id="requirementPreviewFallback" class="h-100 d-none align-items-center justify-content-center flex-column text-center px-4">
                    <i class="fas fa-file-alt text-muted" style="font-size: 2.25rem;"></i>
                    <p class="mt-3 mb-2 font-weight-600">File ini tidak bisa dipreview langsung di browser.</p>
                    <small id="requirementPreviewFileMeta" class="text-muted mb-3"></small>
                    <a id="requirementPreviewFallbackDownload" href="#" class="btn btn-primary btn-sm">
                        <i class="fas fa-download mr-1"></i> Unduh untuk melihat file
                    </a>
                </div>
            </div>
            <div class="modal-footer border-0">
                <a id="requirementPreviewDownload" href="#" class="btn btn-primary btn-sm">
                    <i class="fas fa-download mr-1"></i> Unduh Berkas
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var previewModal = document.getElementById('requirementPreviewModal');
        if (!previewModal) {
            return;
        }

        var previewFrame = document.getElementById('requirementPreviewFrame');
        var previewLabel = document.getElementById('requirementPreviewLabel');
        var previewDownload = document.getElementById('requirementPreviewDownload');
        var previewFallback = document.getElementById('requirementPreviewFallback');
        var previewFallbackMeta = document.getElementById('requirementPreviewFileMeta');
        var previewFallbackDownload = document.getElementById('requirementPreviewFallbackDownload');

        document.querySelectorAll('.requirement-preview-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                var fileUrl = this.getAttribute('data-url');
                var downloadUrl = this.getAttribute('data-download-url');
                var fileName = this.getAttribute('data-name') || 'Berkas';
                var mimeType = this.getAttribute('data-mime') || '-';
                var canPreview = this.getAttribute('data-previewable') === '1';

                previewLabel.textContent = 'Preview: ' + fileName;
                previewDownload.setAttribute('href', downloadUrl);
                previewFallbackDownload.setAttribute('href', downloadUrl);
                previewFallbackMeta.textContent = fileName + ' (' + mimeType + ')';

                if (canPreview) {
                    previewFallback.classList.add('d-none');
                    previewFallback.classList.remove('d-flex');
                    previewFrame.classList.remove('d-none');
                    previewFrame.setAttribute('src', fileUrl);
                } else {
                    previewFrame.classList.add('d-none');
                    previewFrame.setAttribute('src', 'about:blank');
                    previewFallback.classList.remove('d-none');
                    previewFallback.classList.add('d-flex');
                }
            });
        });

        $('#requirementPreviewModal').on('hidden.bs.modal', function () {
            previewFrame.setAttribute('src', 'about:blank');
            previewFrame.classList.remove('d-none');
            previewFallback.classList.add('d-none');
            previewFallback.classList.remove('d-flex');
        });
    });
</script>
