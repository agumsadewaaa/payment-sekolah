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
    <div class="input-group">
        <span class="input-group-text">Rp</span>
        {!! Form::text('nominal', null, ['class' => 'form-control nominal', 'id' => 'nominal', 'placeholder' => 'Masukkan nominal', 'required' => true]) !!}
    </div>
</div>

@push('scripts')
    @include('partials.nominal-input')
@endpush