<!-- Id Field -->
<div class="col-sm-12">
    {!! Form::label('id', 'Id:') !!}
    <p>{{ $siswa->id }}</p>
</div>

<!-- Nama Field -->
<div class="col-sm-12">
    {!! Form::label('nama', 'Nama:') !!}
    <p>{{ $siswa->nama }}</p>
</div>

<!-- Nisn Field -->
<div class="col-sm-12">
    {!! Form::label('nisn', 'Nisn:') !!}
    <p>{{ $siswa->nisn }}</p>
</div>

<!-- Kontak Ortu Field -->
<div class="col-sm-12">
    {!! Form::label('kontak_ortu', 'Kontak Ortu:') !!}
    <p>{{ $siswa->kontak_ortu }}</p>
</div>

<!-- Kelas Field -->
<div class="col-sm-12">
    {!! Form::label('kelas', 'Kelas:') !!}
    <p>{{ $siswa->kelas }}</p>
</div>

<!-- Jurusan Field -->
<div class="col-sm-12">
    {!! Form::label('jurusan', 'Jurusan:') !!}
    <p>{{ $siswa->jurusan }}</p>
</div>

<!-- Tahun Masuk Field -->
<div class="col-sm-12">
    {!! Form::label('tahun_masuk', 'Tahun Masuk:') !!}
    <p>{{ $siswa->tahun_masuk }}</p>
</div>

<!-- Tahun Lulus Field -->
<div class="col-sm-12">
    {!! Form::label('tahun_lulus', 'Tahun Lulus:') !!}
    <p>{{ $siswa->tahun_lulus }}</p>
</div>

<!-- Status Siswa Field -->
<div class="col-sm-12">
    {!! Form::label('status_siswa', 'Status Siswa:') !!}
    <p>{{ $siswa->status_siswa }}</p>
</div>

<!-- Created At Field -->
<div class="col-sm-12">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{{ $siswa->created_at }}</p>
</div>

<!-- Updated At Field -->
<div class="col-sm-12">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{{ $siswa->updated_at }}</p>
</div>

<!-- Deleted At Field -->
<div class="col-sm-12">
    {!! Form::label('deleted_at', 'Deleted At:') !!}
    <p>{{ $siswa->deleted_at }}</p>
</div>

