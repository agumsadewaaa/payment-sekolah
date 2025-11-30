<!-- Id Field -->
<div class="col-sm-12">
    {!! Form::label('id', 'Id:') !!}
    <p>{{ $tagihan->id }}</p>
</div>

<!-- Kelas Field -->
<div class="col-sm-12">
    {!! Form::label('kelas', 'Kelas:') !!}
    <p>{{ $tagihan->kelas }}</p>
</div>

<!-- Tagihan Field -->
<div class="col-sm-12">
    {!! Form::label('tagihan', 'Tagihan:') !!}
    <p>{{ $tagihan->tagihan }}</p>
</div>

<!-- Nominal Field -->
<div class="col-sm-12">
    {!! Form::label('nominal', 'Nominal:') !!}
    <p>Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</p>
</div>

<!-- Created At Field -->
<div class="col-sm-12">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{{ $tagihan->created_at }}</p>
</div>

<!-- Updated At Field -->
<div class="col-sm-12">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{{ $tagihan->updated_at }}</p>
</div>

