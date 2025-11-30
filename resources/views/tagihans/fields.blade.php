<!-- Kelas Field -->
<div class="form-group col-sm-6">
    {!! Form::label('kelas', 'Kelas:') !!}<span class="text-danger">*</span>
    {!! Form::select('kelas', $kelas, null, [
        'class' => 'form-control pendapatan-field',
        'id' => 'kelas',
        'placeholder' => 'Pilih kelas'
    ]) !!}
</div>

<!-- Tagihan Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tagihan', 'Tagihan:') !!}<span class="text-danger">*</span>
    {!! Form::text('tagihan', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Nominal Field -->
<div class="form-group col-sm-12">
    {!! Form::label('nominal', 'Nominal:') !!}<span class="text-danger">*</span>
    {!! Form::number('nominal', null, ['class' => 'form-control', 'required' => true, 'min' => 0]) !!}
</div>