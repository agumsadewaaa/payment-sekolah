<div class="card-body">
    <div class="table-responsive">
        <table id="example3" class="display min-w850">
            <thead>
            <tr>
                <th>Kelas</th>
                <th>Tagihan</th>
                <th>Nominal</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tagihans as $tagihan)
                <tr>
                    <td>{{ $tagihan->kelass ? $tagihan->kelass->kode : '-' }}</td>
                    <td>{{ $tagihan->tagihan }}</td>
                    <td>Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['tagihans.destroy', $tagihan->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('tagihans.show', [$tagihan->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            @hasanyrole('admin|super-admin')
                                <a href="{{ route('tagihans.edit', [$tagihan->id]) }}" class='btn btn-default btn-xs'>
                                    <i class="far fa-edit"></i>
                                </a>
                                {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                            @endhasanyrole
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
            @include('adminlte-templates::common.paginate', ['records' => $tagihans])
        </div>
    </div>
</div>
