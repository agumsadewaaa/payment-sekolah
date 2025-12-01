<!-- Kode Field -->
<div class="form-group col-sm-12">
    {!! Form::label('kode', 'Kode:') !!}
    {!! Form::text('kode', null, ['class' => 'form-control', 'required', 'readonly', 'id' => 'kode', 'placeholder' => 'Auto-generated dari Kelas + Jurusan']) !!}
</div>

<!-- Kelas Field -->
<div class="form-group col-sm-6">
    {!! Form::label('kelas', 'Kelas:') !!}
    {!! Form::select('kelas', [10 => '10', 11 => '11', 12 => '12'], null, [
        'class' => 'form-control',
        'placeholder' => 'Pilih kelas',
        'required',
        'id' => 'kelas'
    ]) !!}
</div>

<!-- Jurusan Field -->
<div class="form-group col-sm-6">
    {!! Form::label('jurusan', 'Jurusan:') !!}
    {!! Form::text('jurusan', null, ['class' => 'form-control', 'required', 'id' => 'jurusan']) !!}
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        // Auto-generate kode dari kelas + jurusan
        function updateKode() {
            var kelasVal = $('#kelas').val();
            var jurusanVal = $('#jurusan').val();

            if (kelasVal && jurusanVal) {
                $('#kode').val(kelasVal + '-' + jurusanVal);
            } else {
                $('#kode').val('');
            }
        }

        $('#kelas, #jurusan').on('change keyup', function() {
            updateKode();
        });

        // Trigger saat edit (untuk populate kode)
        @if(isset($kelas) && $kelas->kelas && $kelas->jurusan)
            updateKode();
        @endif
    });
</script>
@endpush