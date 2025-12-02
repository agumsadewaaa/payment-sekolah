<!-- Nama Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nama', 'Nama:') !!}<span class="text-danger">*</span>
    {!! Form::text('nama', null, ['class' => 'form-control', 'required' ]) !!}
</div>

<!-- NISN Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nisn', 'NISN:') !!}<span class="text-danger">*</span>
    {!! Form::number('nisn', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Kontak Ortu Field -->
<div class="form-group col-sm-6">
    {!! Form::label('kontak_ortu', 'Kontak Ortu:') !!}<span class="text-danger">*</span>
    {!! Form::text('kontak_ortu', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Kelas Field -->
<div class="form-group col-sm-6">
    {!! Form::label('kelas', 'Kelas:') !!}<span class="text-danger">*</span>
    {!! Form::select('kelas', [10 => '10', 11 => '11', 12 => '12'], null, [
        'class' => 'form-control',
        'placeholder' => 'Pilih kelas',
        'required',
        'id' => 'kelas'
    ]) !!}
</div>

<!-- Jurusan Field -->
<div class="form-group col-sm-6">
    {!! Form::label('jurusan', 'Jurusan:') !!}<span class="text-danger">*</span>
    {!! Form::select('jurusan', [], null, [
        'class' => 'form-control',
        'placeholder' => 'Pilih jurusan',
        'required',
        'id' => 'jurusan'
    ]) !!}
</div>

<!-- Kode Kelas Field (Hidden, auto-generated) -->
{!! Form::hidden('kode_kelas', null, ['id' => 'kode_kelas']) !!}

<!-- Tahun Masuk Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tahun_masuk', 'Tahun Masuk:') !!}<span class="text-danger">*</span>
    {!! Form::number('tahun_masuk', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Tahun Lulus Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tahun_lulus', 'Tahun Lulus:') !!}
    {!! Form::number('tahun_lulus', null, ['class' => 'form-control']) !!}
</div>

<!-- Status Siswa Field -->
<div class="form-group col-sm-6"><span class="text-danger">*</span>
    {!! Form::label('status_siswa', 'Status Siswa:') !!}
    {!! Form::text('status_siswa', null, ['class' => 'form-control', 'required']) !!}
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        // Untuk populate jurusan ketika kelas berubah
        $('#kelas').on('change', function() {
            var kelas = $(this).val();

            // reset dulu jurusan biar ada feedback
            $('#jurusan').empty().append('<option value="">Loading...</option>');
            $('#kode_kelas').val('');

            if (kelas) {
                $.get('/get-jurusan/' + kelas, function(data) {
                    $('#jurusan').empty().append('<option value="">Pilih jurusan</option>');
                    $.each(data, function(id, nama) {
                        $('#jurusan').append('<option value="' + id + '">' + nama + '</option>');
                    });
                    
                    // kalau jurusan pakai bootstrap-select
                    if ($.fn.selectpicker) {
                        $('#jurusan').selectpicker('refresh');
                    }
                    
                    // Auto-select jurusan jika sudah ada nilai sebelumnya (saat edit)
                    @if(isset($siswa) && $siswa->jurusan)
                        $('#jurusan').val('{{ $siswa->jurusan }}');
                        if ($.fn.selectpicker) {
                            $('#jurusan').selectpicker('refresh');
                        }
                        // Trigger change untuk update kode kelas
                        $('#jurusan').trigger('change');
                    @endif
                });
            } else {
                $('#jurusan').empty().append('<option value="">Pilih jurusan</option>');
                if ($.fn.selectpicker) {
                    $('#jurusan').selectpicker('refresh');
                }
            }
        });

        // Update kode kelas ketika jurusan dipilih
        $('#jurusan').on('change', function() {
            var kelasVal = $('#kelas').val();
            var jurusanId = $(this).val();
            var jurusanText = $('#jurusan option:selected').text();

            if (kelasVal && jurusanId && jurusanText !== 'Pilih jurusan' && jurusanText !== 'Loading...') {
                $('#kode_kelas').val(kelasVal + '-' + jurusanText);
            } else {
                $('#kode_kelas').val('');
            }
        });

        // Trigger auto-load jurusan saat edit (ketika kelas sudah terisi)
        @if(isset($siswa) && $siswa->kelas)
            $('#kelas').trigger('change');
        @endif
    });
</script>
@endpush
