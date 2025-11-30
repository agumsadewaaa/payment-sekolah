<!-- Tanggal Field -->
<!-- <div class="card">
    <div class="card-header">
        <h4 class="card-title">Pick-Date picker</h4>
    </div>
    <div class="card-body">
        <label class="form-label">Default picker</label>
        <input name="datepicker" class="datepicker-default form-control" id="datepicker">
    </div>
</div> -->
<!-- Tanggal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tanggal', 'Tanggal:') !!}<span class="text-danger">*</span>
    {!! Form::date('tanggal', null, [
        'class' => 'form-control',
        'id' => 'tanggal',
        'required'
    ]) !!}
</div>

<!-- Tipe Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tipe', 'Tipe:') !!}<span class="text-danger">*</span>
    {!! Form::select('tipe', [1 => 'Pendapatan', 2 => 'Pengeluaran'], null, [
        'class' => 'form-control',
        'id' => 'tipe',
        'placeholder' => 'Pilih tipe',
        'required'
    ]) !!}
</div>

<!-- ====== Form Pendapatan ====== -->
<div id="form-pendapatan" style="display:none;">
    <div class="row">
        <!-- Kelas -->
        <div class="form-group col-sm-6">
            {!! Form::label('kelas', 'Kelas:') !!}<span class="text-danger">*</span>
            {!! Form::select('kelas', $kelas, null, [
                'class' => 'form-control pendapatan-field',
                'id' => 'kelas',
                'placeholder' => 'Pilih kelas'
            ]) !!}
        </div>

        <!-- Nama Siswa -->
        <div class="form-group col-sm-6">
            {!! Form::label('siswa_id', 'Nama:') !!}<span class="text-danger">*</span>
            {!! Form::select('siswa_id', [], null, [
                'class' => 'form-control pendapatan-field',
                'id' => 'siswa_id',
                'placeholder' => 'Pilih nama siswa'
            ]) !!}
        </div>
    </div>
    
    <div class="row">
        <!-- Tagihan -->
        <div class="form-group col-sm-6">
            {!! Form::label('tagihan_id', 'Tagihan:') !!}<span class="text-danger">*</span>
            {!! Form::select('tagihan_id', [], null, [
                'class' => 'form-control pendapatan-field',
                'id' => 'tagihan_id',
                'placeholder' => 'Pilih tagihan'
            ]) !!}
        </div>

        <!-- Metode Pembayaran -->
        <div class="form-group col-sm-6">
            {!! Form::label('metode_pembayaran', 'Metode Pembayaran:') !!}<span class="text-danger">*</span>
            {!! Form::select('metode_pembayaran', ['Tunai' => 'Tunai', 'Transfer' => 'Transfer', 'Non-Tunai' => 'Non-Tunai / Beasiswa'], null, [
                'class' => 'form-control pendapatan-field',
                'id' => 'metode_pembayaran',
                'placeholder' => 'Pilih metode pembayaran'
            ]) !!}
        </div>
    </div>
</div>

<!-- ====== Form Pengeluaran ====== -->
<div id="form-pengeluaran" style="display:none;">
    <!-- Catatan/Keterangan -->
    <div class="form-group col-sm-12">
        {!! Form::label('catatan', 'Catatan/Keterangan:') !!}<span class="text-danger">*</span>
        {!! Form::text('catatan', null, [
            'class' => 'form-control pengeluaran-field',
            'placeholder' => 'Detail Catatan/Pengeluaran'
        ]) !!}
    </div>
</div>

<!-- Nominal Field (dipakai dua-duanya) -->
<div class="form-group col-sm-12">
    {!! Form::label('nominal', 'Nominal:') !!}<span class="text-danger">*</span>
    <div class="input-group">
        <span class="input-group-text">Rp</span>
        {!! Form::text('nominal', null, [
            'class' => 'form-control',
            'id' => 'nominal',
            'placeholder' => 'Masukkan nominal',
            'required'
        ]) !!}
    </div>
</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const tipeSelect = document.querySelector('#tipe');
    const formPendapatan = document.getElementById('form-pendapatan');
    const formPengeluaran = document.getElementById('form-pengeluaran');
    const nominalInput = document.getElementById('nominal');

    const pendapatanFields = document.querySelectorAll('.pendapatan-field');
    const pengeluaranFields = document.querySelectorAll('.pengeluaran-field');

    function toggleForm() {
        if (tipeSelect.value === "1") { // Pendapatan
            formPendapatan.style.display = 'block';
            formPengeluaran.style.display = 'none';
            pendapatanFields.forEach(f => f.required = true);
            pengeluaranFields.forEach(f => f.required = false);
        } else if (tipeSelect.value === "2") { // Pengeluaran
            formPendapatan.style.display = 'none';
            formPengeluaran.style.display = 'block';
            pengeluaranFields.forEach(f => f.required = true);
            pendapatanFields.forEach(f => f.required = false);
        } else {
            formPendapatan.style.display = 'none';
            formPengeluaran.style.display = 'none';
            pendapatanFields.forEach(f => f.required = false);
            pengeluaranFields.forEach(f => f.required = false);
        }
    }

    tipeSelect.addEventListener('change', toggleForm);
    toggleForm();

    // 🔥 Format nominal input aman
    nominalInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        e.target.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
    });

    // 🔥 Bersihkan nominal sebelum submit
    $('form').on('submit', function() {
        $('#nominal').val($('#nominal').val().replace(/\D/g, ''));
    });
});

$(document).ready(function() {
    // Kelas -> siswa
    $('#kelas').on('change', function() {
        var kelas = $(this).val();
        $('#siswa_id').empty().append('<option value="">Loading...</option>');
        $('#tagihan_id').empty().append('<option value="">Pilih tagihan</option>');
        if (kelas) {
            $.get('/get-siswa-by-kelas/' + kelas, function(data) {
                $('#siswa_id').empty().append('<option value="">Pilih nama siswa</option>');
                $.each(data, function(id, nama) {
                    $('#siswa_id').append('<option value="' + id + '">' + nama + '</option>');
                });
                $('#siswa_id').selectpicker('refresh');
            });
        }
    });

    // Siswa -> tagihan
    $('#siswa_id').on('change', function() {
        var siswa_id = $(this).val();
        $('#tagihan_id').empty().append('<option value="">Loading...</option>');
        if(siswa_id) {
            $.get('/get-tagihan-by-siswa/' + siswa_id, function(data) {
                $('#tagihan_id').empty().append('<option value="">Pilih tagihan</option>');
                $.each(data, function(id, tagihan) {
                    $('#tagihan_id').append('<option value="'+id+'">'+tagihan+'</option>');
                });
                $('#tagihan_id').selectpicker('refresh');
            });
        }
    });
});
</script>
@endpush



