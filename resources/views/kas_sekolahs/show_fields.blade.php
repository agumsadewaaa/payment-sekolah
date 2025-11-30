<!-- Id Field -->
<div class="col-sm-12">
    {!! Form::label('id', 'Id:') !!}
    <p>{{ $kasSekolah->id }}</p>
</div>

<!-- Tanggal Field -->
<div class="col-sm-12">
    {!! Form::label('tanggal', 'Tanggal:') !!}
    <p>{{ $kasSekolah->tanggal }}</p>
</div>

<!-- Catatan Field -->
<div class="col-sm-12">
    {!! Form::label('catatan', 'Catatan:') !!}
    <p>{{ $kasSekolah->catatan }}</p>
</div>

<!-- Tipe Field -->
<div class="col-sm-12">
    {!! Form::label('tipe', 'Tipe:') !!}
    <p>{{ $kasSekolah->tipe }}</p>
</div>

<!-- Created At Field -->
<div class="col-sm-12">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{{ $kasSekolah->created_at }}</p>
</div>

<!-- Updated At Field -->
<div class="col-sm-12">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{{ $kasSekolah->updated_at }}</p>
</div>

<!-- Deleted At Field -->
<div class="col-sm-12">
    {!! Form::label('deleted_at', 'Deleted At:') !!}
    <p>{{ $kasSekolah->deleted_at }}</p>
</div>

