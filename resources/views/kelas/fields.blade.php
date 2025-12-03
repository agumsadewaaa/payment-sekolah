<!-- Kode Field (Read-only preview) -->
<div class="form-group col-sm-12">
    {!! Form::label('kode_preview', 'Kode Kelas (Auto):') !!}
    <input type="text" id="kode_preview" class="form-control bg-light" readonly placeholder="Akan otomatis terisi (contoh: 10-TKJ)">
    {!! Form::hidden('kode', null, ['id' => 'kode']) !!}
</div>

<!-- Kelas Field -->
<div class="form-group col-sm-6">
    {!! Form::label('kelas', 'Tingkat Kelas:') !!}
    {!! Form::select('kelas', [10 => '10', 11 => '11', 12 => '12'], null, [
        'class' => 'form-control',
        'placeholder' => 'Pilih tingkat kelas',
        'required',
        'id' => 'kelas'
    ]) !!}
</div>

<!-- Jurusan Field -->
<div class="form-group col-sm-6">
    {!! Form::label('jurusan', 'Jurusan:') !!}
    {!! Form::select('jurusan', [
        'Akuntansi dan Keuangan Lembaga' => 'Akuntansi dan Keuangan Lembaga',
        'Manajemen Perkantoran dan Layanan Bisnis' => 'Manajemen Perkantoran dan Layanan Bisnis',
        'Teknologi Farmasi' => 'Teknologi Farmasi',
        'Teknik Jaringan Komputer & Telekomunikasi' => 'Teknik Jaringan Komputer & Telekomunikasi',
        'Teknik Otomotif' => 'Teknik Otomotif'
    ], null, [
        'class' => 'form-control',
        'placeholder' => 'Pilih jurusan',
        'required',
        'id' => 'jurusan'
    ]) !!}
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        // Mapping jurusan ke kode singkat
        const jurusanMap = {
            'Akuntansi dan Keuangan Lembaga': 'AKL',
            'Manajemen Perkantoran dan Layanan Bisnis': 'MPLB',
            'Teknologi Farmasi': 'TF',
            'Teknik Jaringan Komputer & Telekomunikasi': 'TJKT',
            'Teknik Otomotif': 'TO'
        };

        // Auto-generate kode dari kelas + kode singkat jurusan
        function updateKode() {
            var kelasVal = $('#kelas').val();
            var jurusanVal = $('#jurusan').val();

            if (kelasVal && jurusanVal) {
                var kodeJurusan = jurusanMap[jurusanVal] || '';
                var kodeKelas = kelasVal + '-' + kodeJurusan;
                $('#kode').val(kodeKelas);
                $('#kode_preview').val(kodeKelas);
            } else {
                $('#kode').val('');
                $('#kode_preview').val('');
            }
        }

        $('#kelas, #jurusan').on('change', function() {
            updateKode();
        });

        // Trigger saat edit (untuk populate kode)
        @if(isset($kelas) && $kelas->kelas && $kelas->jurusan)
            updateKode();
        @endif
    });
</script>
@endpush