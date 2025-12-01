@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const formatNumber = (n) => new Intl.NumberFormat('id-ID').format(n);
    const numericOnly = (s) => (s || '').toString().replace(/\D/g, '');

    // Format while typing for any input with class .nominal
    document.querySelectorAll('.nominal').forEach(function (el) {
        // format on input
        el.addEventListener('input', function (e) {
            const raw = numericOnly(this.value);
            // keep empty if nothing
            this.value = raw ? formatNumber(raw) : '';
        });

        // optionally format initial value (if already present)
        if (el.value && numericOnly(el.value) !== el.value) {
            el.value = formatNumber(numericOnly(el.value));
        }
    });

    // Before submit, ensure numeric values are sent (remove separators)
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            form.querySelectorAll('.nominal').forEach(function (el) {
                el.value = numericOnly(el.value);
            });
        });
    });
});
</script>
@endpush
