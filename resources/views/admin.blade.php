@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('status'))
        <div class="alert alert-success mb-3">
            {{ session('status') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">Menu Admin</div>
        <div class="card-body">
            <p>Gunakan tombol ini untuk otomatisasi kenaikan kelas & kelulusan siswa.</p>

            <form method="POST" action="{{ route('admin.promote') }}"
                  onsubmit="return confirm('Yakin memproses kenaikan & kelulusan sekarang?');">
                @csrf
                <button type="submit" class="btn btn-primary">
                    Proses Kenaikan & Kelulusan
                </button>
            </form>

            <hr>
            <!-- <small class="text-muted">
                Aturan: Kelas 10→11, 11→12, 12→0, dan <code>tahun_lulus</code> diisi tahun berjalan.
            </small> -->
        </div>
    </div>
</div>
@endsection
