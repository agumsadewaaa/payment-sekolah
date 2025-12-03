<!-- Kode Field -->
<div class="form-group col-sm-12">
    {!! Form::label('kode', 'Kode Kelas:') !!}
    {!! Form::text('kode', null, ['class' => 'form-control', 'required', 'id' => 'kode', 'placeholder' => 'Contoh: 10-TKJ']) !!}
    <small class="text-muted">Format: Tingkat-Kode Jurusan (misal: 10-TKJ, 11-AKL, 12-MPLB)</small>
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
    {!! Form::label('jurusan', 'Nama Jurusan:') !!}
    {!! Form::text('jurusan', null, ['class' => 'form-control', 'required', 'id' => 'jurusan', 'placeholder' => 'Contoh: Teknologi Komputer Jaringan']) !!}
</div>