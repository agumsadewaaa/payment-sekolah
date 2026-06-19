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
    <div class="input-group">
        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
        {!! Form::text('tanggal', null, [
            'class' => 'form-control datepicker-default',
            'id' => 'tanggal',
            'placeholder' => 'Pilih tanggal',
            'required',
            'autocomplete' => 'off'
        ]) !!}
    </div>
</div>

<!-- Tipe Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tipe', 'Tipe:') !!}<span class="text-danger">*</span>
    {!! Form::select('tipe', [1 => 'Pendapatan', 2 => 'Pengeluaran'], null, [
        'class' => 'form-control selectpicker',
        'id' => 'tipe',
        'placeholder' => 'Pilih tipe',
        'required',
        'data-live-search' => 'true'
    ]) !!}
</div>

<!-- ====== Form Pendapatan ====== -->
<div id="form-pendapatan" style="display:none;">
    <div class="row">
        <!-- Kelas -->
        <div class="form-group col-sm-6">
            {!! Form::label('kelas', 'Kelas:') !!}<span class="text-danger">*</span>
            {!! Form::select('kelas', $kelas, old('kelas', isset($kasSiswa) && $kasSiswa && $kasSiswa->siswa ? $kasSiswa->siswa->jurusan : null), [
                'class' => 'form-control pendapatan-field selectpicker',
                'id' => 'kelas',
                'placeholder' => 'Pilih kelas',
                'data-live-search' => 'true'
            ]) !!}
        </div>

        <!-- Nama Siswa -->
        <div class="form-group col-sm-6">
            {!! Form::label('siswa_id', 'Nama:') !!}<span class="text-danger">*</span>
            {!! Form::select('siswa_id', isset($siswaOptions) ? $siswaOptions : [], old('siswa_id', isset($kasSiswa) ? $kasSiswa->siswa_id : null), [
                'class' => 'form-control pendapatan-field selectpicker',
                'id' => 'siswa_id',
                'placeholder' => 'Pilih nama siswa',
                'data-live-search' => 'true'
            ]) !!}
        </div>
    </div>
    
    <!-- Alert for Outstanding Bills -->
    <div id="alert-outstanding" style="display:none;" class="row">
        <div class="col-sm-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-exclamation-triangle"></i> Perhatian!</strong>
                <p id="alert-text" class="mb-0"></p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Tagihan -->
        <div class="form-group col-sm-6">
            {!! Form::label('tagihan_id', 'Tagihan:') !!}<span class="text-danger">*</span>
            {!! Form::select('tagihan_id', 
                isset($tagihanOptions) ? $tagihanOptions : [],
                old('tagihan_id', isset($kasSiswa) ? $kasSiswa->tagihan_id : null), 
                [
                    'class' => 'form-control pendapatan-field selectpicker',
                    'id' => 'tagihan_id',
                    'placeholder' => 'Pilih tagihan',
                    'data-live-search' => 'true'
                ]) !!}
        </div>

        <!-- Metode Pembayaran -->
        <div class="form-group col-sm-6">
            {!! Form::label('metode_pembayaran', 'Metode Pembayaran:') !!}<span class="text-danger">*</span>
            {!! Form::select('metode_pembayaran', ['Tunai' => 'Tunai', 'Transfer' => 'Transfer', 'Non-Tunai' => 'Non-Tunai / Beasiswa'], null, [
                'class' => 'form-control pendapatan-field selectpicker',
                'id' => 'metode_pembayaran',
                'placeholder' => 'Pilih metode pembayaran',
                'data-live-search' => 'true'
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
            'class' => 'form-control nominal',
            'id' => 'nominal',
            'placeholder' => 'Masukkan nominal',
            'required'
        ]) !!}
    </div>
</div>

@push('scripts')
<script>
$(function() {
    // Initialize datepicker for tanggal field
    $('#tanggal').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: 'Pilih',
            cancelLabel: 'Batal'
        }
    });

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
        $('.selectpicker').selectpicker('refresh');
    }

    tipeSelect.addEventListener('change', toggleForm);
    toggleForm();
    
    // Auto-trigger jika ada old input (setelah error/validation fail)
    @if(old('kelas'))
        $('#kelas').val('{{ old('kelas') }}').trigger('change');
        $('#kelas').selectpicker('refresh');
    @elseif(isset($kasSiswa) && $kasSiswa)
        $('#kelas').val('{{ $kasSiswa && $kasSiswa->siswa ? $kasSiswa->siswa->jurusan : old('kelas') }}').trigger('change');
        $('#kelas').selectpicker('refresh');
    @endif

    // Jika edit dan ada siswa, trigger siswa change
    @if(isset($kasSiswa) && $kasSiswa)
        setTimeout(function() {
            $('#siswa_id').val('{{ $kasSiswa->siswa_id }}').trigger('change');
            $('#siswa_id').selectpicker('refresh');
        }, 500);
    @endif

    // Jika edit dan ada tagihan, trigger tagihan change
    @if(isset($kasSiswa) && $kasSiswa)
        setTimeout(function() {
            $('#tagihan_id').val('{{ $kasSiswa->tagihan_id }}').trigger('change');
            $('#tagihan_id').selectpicker('refresh');
        }, 1000);
    @endif

    // formatting for nominal inputs is handled via shared partial
});

// include central nominal formatting script
@include('partials.nominal-input')

var maxSisaTagihan = 0;

$(document).ready(function() {
    // Kelas -> siswa
    $('#kelas').on('change', function() {
        var kelas = $(this).val();
        $('#siswa_id').empty().append('<option value="">Loading...</option>');
        $('#tagihan_id').empty().append('<option value="">Pilih tagihan</option>');
        resetNominalValidation();
        if (kelas) {
            $.get('/get-siswa-by-kelas/' + kelas, function(data) {
                $('#siswa_id').empty().append('<option value="">Pilih nama siswa</option>');
                $.each(data, function(id, nama) {
                    $('#siswa_id').append('<option value="' + id + '">' + nama + '</option>');
                });
                if(typeof $.fn.selectpicker !== 'undefined') {
                    $('#siswa_id').selectpicker('refresh');
                }
            });
        }
    });

    // Siswa -> tagihan
    $('#siswa_id').on('change', function() {
        var siswa_id = $(this).val();
        resetNominalValidation();
        
        // Check for outstanding bills
        if(siswa_id) {
            $.get('/get-outstanding-bills/' + siswa_id, function(response) {
                if(response && response.has_outstanding) {
                    var billsList = response.outstanding_bills.map(function(bill) {
                        return bill.kode_kelas + ' - ' + bill.tagihan + ': Rp ' + bill.nominal.toLocaleString('id-ID');
                    }).join('<br>');
                    
                    $('#alert-text').html(
                        'Siswa ini memiliki tunggakan dari kelas sebelumnya sebagai berikut:<br>' +
                        billsList + '<br><br>' +
                        '<strong>Total Tunggakan: Rp ' + response.total_outstanding.toLocaleString('id-ID') + '</strong><br>' +
                        'Pastikan pembayaran kali ini cukup untuk melunasi tunggakan PLUS tagihan kelas saat ini!'
                    );
                    $('#alert-outstanding').show();
                } else {
                    $('#alert-outstanding').hide();
                }
            }).fail(function(error) {
                console.log('Error getting outstanding bills:', error);
                $('#alert-outstanding').hide();
            });
        }
        
        @if(isset($kasSekolah))
            // EDIT MODE: Jangan refresh tagihan via AJAX, sudah di-populate dari PHP
            // Hanya trigger tagihan change untuk auto-populate catatan
            $('#tagihan_id').trigger('change');
        @else
            // CREATE MODE: Populate tagihan via AJAX
            $('#tagihan_id').empty().append('<option value="">Loading...</option>');
            if(siswa_id) {
                $.get('/get-tagihan-by-siswa/' + siswa_id, function(data) {
                    console.log('Tagihan data received:', data);
                    $('#tagihan_id').empty().append('<option value="">Pilih tagihan</option>');
                    if(data && Object.keys(data).length > 0) {
                        $.each(data, function(id, tagihan) {
                            $('#tagihan_id').append('<option value="'+id+'">'+tagihan+'</option>');
                        });
                    } else {
                        console.log('No tagihan available for siswa:', siswa_id);
                        $('#tagihan_id').append('<option value="">Tidak ada tagihan yang tersedia</option>');
                    }
                    if(typeof $.fn.selectpicker !== 'undefined') {
                        $('#tagihan_id').selectpicker('refresh');
                    }
                }).fail(function(error) {
                    console.log('Error getting tagihan:', error);
                    $('#tagihan_id').empty().append('<option value="">Error loading tagihan</option>');
                });
            }
        @endif
    });

    // Tagihan -> validasi nominal max dan auto-populate catatan
    $('#tagihan_id').on('change', function() {
        var tagihan_id = $(this).val();
        var siswa_id = $('#siswa_id').val();
        var kelas_id = $('#kelas').val();
        resetNominalValidation();
        
        if(tagihan_id && siswa_id) {
            @if(!isset($kasSekolah))
                // UNTUK CREATE: Get sisa tagihan via AJAX
                $.get('/get-sisa-tagihan/' + siswa_id + '/' + tagihan_id, function(response) {
                    if(response.sisa !== undefined) {
                        var sisaTagihan = response.sisa;
                        maxSisaTagihan = sisaTagihan;
                        var nominalInput = $('#nominal');
                        
                        // Set max attribute hanya untuk create
                        nominalInput.attr('max', sisaTagihan);
                        nominalInput.attr('placeholder', 'Maksimal: Rp ' + sisaTagihan.toLocaleString('id-ID'));
                        
                        // Tambahkan info sisa
                        if($('#info-sisa').length === 0) {
                            nominalInput.closest('.form-group').append(
                                '<small id="info-sisa" class="text-info">Sisa tagihan: Rp ' + sisaTagihan.toLocaleString('id-ID') + '</small>'
                            );
                        } else {
                            $('#info-sisa').text('Sisa tagihan: Rp ' + sisaTagihan.toLocaleString('id-ID'));
                        }
                        
                        // Validasi nominal saat ini juga
                        validateNominal();
                    }
                });
            @else
                // UNTUK EDIT: Hanya auto-populate catatan tanpa validasi nominal
            @endif
            
            // Get tagihan info untuk auto-populate catatan (untuk create dan edit)
            $.get('/get-tagihan-info/' + siswa_id + '/' + kelas_id + '/' + tagihan_id, function(response) {
                if(response.catatan) {
                    $('#catatan').val(response.catatan);
                }
            });
        }
    });

    // Validasi nominal setiap kali input berubah
    $('#nominal').on('input', function() {
        validateNominal();
    });

    // Prevent form submit jika nominal melebihi sisa tagihan
    $('form').on('submit', function(e) {
        if ($('#tipe').val() === '1' && maxSisaTagihan > 0) {
            var nominal = parseInt($('#nominal').val().replace(/\D/g, '')) || 0;
            if (nominal > maxSisaTagihan) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Nominal tidak boleh melebihi sisa tagihan!'
                });
                return false;
            }
        }
    });
});

function resetNominalValidation() {
    maxSisaTagihan = 0;
    $('#warning-nominal').remove();
    $('#info-sisa').remove();
    $('#nominal').removeAttr('max').attr('placeholder', 'Masukkan nominal');
}

function validateNominal() {
    var nominal = parseInt($('#nominal').val().replace(/\D/g, '')) || 0;
    if (maxSisaTagihan > 0 && nominal > maxSisaTagihan) {
        if ($('#warning-nominal').length === 0) {
            $('#nominal').closest('.form-group').append(
                '<small id="warning-nominal" class="text-danger"><i class="fas fa-exclamation-circle"></i> Nominal tidak boleh melebihi sisa tagihan!</small>'
            );
        }
        $('#nominal').addClass('is-invalid');
    } else {
        $('#warning-nominal').remove();
        $('#nominal').removeClass('is-invalid');
    }
}
</script>
@endpush



