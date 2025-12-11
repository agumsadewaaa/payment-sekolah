<div class="card-body">
    <div class="table-responsive">
        <table id="example3" class="display min-w850">
            <thead>
            <tr>
                <th>Kode</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($kelass as $kelas)
                <tr>
                    <td>{{ $kelas->kode }}</td>
                    <td>{{ $kelas->kelas }}</td>
                    <td>{{ $kelas->jurusan }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['kelas.destroy', $kelas->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('kelas.show', [$kelas->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            @hasanyrole('admin|super-admin')
                                <a href="{{ route('kelas.edit', [$kelas->id]) }}" class='btn btn-default btn-xs'>
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
            @include('adminlte-templates::common.paginate', ['records' => $kelass])
        </div>
    </div>
</div>
