<div class="card-body">
    <div class="table-responsive">
        <table id="example3" class="display min-w850">
            <thead>
            <tr>
                <th>Tanggal</th>
                <th>Catatan</th>
                <th>Tipe</th>
                <th>Metode Pembayaran</th>
                <th>Nominal</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($kasSekolahs as $kasSekolah)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($kasSekolah->tanggal)->translatedFormat('d M Y') }}</td>
                    <td>{{ $kasSekolah->catatan }}</td>
                    <td>
                        {{ $kasSekolah->tipe == 1 ? 'Pendapatan' : ($kasSekolah->tipe == 2 ? 'Pengeluaran' : '-') }}
                    </td>
                    <td>{{ $kasSekolah->metode_pembayaran ?? '-' }}</td>
                    <td>Rp {{ number_format($kasSekolah->nominal, 0, ',', '.') }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['kas-sekolahs.destroy', $kasSekolah->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('kas-sekolahs.show', [$kasSekolah->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('kas-sekolahs.edit', [$kasSekolah->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-edit"></i>
                            </a>
                            {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $kasSekolahs])
        </div>
    </div>
</div>
