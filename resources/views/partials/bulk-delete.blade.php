@push('styles')
<style>
#select-all, .row-checkbox { width: 18px; height: 18px; cursor: pointer;}
#bulk-delete-bar { transition: all 0.3s ease; }
</style>
@endpush

@hasanyrole('admin|super-admin')
<div id="bulk-delete-bar" class="d-none" style="padding-top: 20px; padding-left:20px;">
    <button type="button" id="btn-bulk-delete" class="btn btn-danger">
        <i class="fas fa-trash me-1"></i>Hapus <span id="selected-count">0</span> Data
    </button>
</div>
@endhasanyrole

@push('scripts')
<script>
$(function() {
    var bulkDeleteRoute = '{{ route($route) }}';
    var $selectAll = $('#select-all');
    var $bulkBar = $('#bulk-delete-bar');
    var $selectedCount = $('#selected-count');

    function updateBulkDeleteButton() {
        var checked = $('.row-checkbox:checked').length;
        if (checked > 0) {
            $bulkBar.removeClass('d-none');
            $selectedCount.text(checked);
        } else {
            $bulkBar.addClass('d-none');
        }
    }

    $selectAll.on('change', function() {
        $('.row-checkbox').prop('checked', this.checked);
        updateBulkDeleteButton();
    });

    $(document).on('change', '.row-checkbox', function() {
        var allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
        $selectAll.prop('checked', allChecked);
        updateBulkDeleteButton();
    });

    $('#btn-bulk-delete').on('click', function() {
        var ids = $('.row-checkbox:checked').map(function() { return $(this).val(); }).get();
        if (ids.length === 0) return;
        if (!confirm('Yakin ingin menghapus ' + ids.length + ' data terpilih?')) return;

        $.ajax({
            url: bulkDeleteRoute,
            method: 'POST',
            data: { ids: ids, _token: '{{ csrf_token() }}' },
            success: function() { location.reload(); },
            error: function(xhr) {
                alert('Gagal: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'));
            }
        });
    });
});
</script>
@endpush
