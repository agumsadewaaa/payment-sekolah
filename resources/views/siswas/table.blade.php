<div class="card-body">
    <div class="table-responsive">
        <table id="example3" class="display min-w850">
            <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIS</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th>Progres Pembayaran</th>
                <th>Status Siswa</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @foreach($siswas as $siswa)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $siswa->nama }}</td>
                    <td><span style="font-family: monospace;">{{ $siswa->nis }}</span></td>
                    <td>{{ $siswa->kelas == 0 ? 'Lulus' : $siswa->kelas }}</td>
                    <td>{{ $siswa->jurusans ? $siswa->jurusans->jurusan : '-' }}</td>
                    <td>
                        @php
                            $progress = $siswa->progress;
                            $isTunggakan = is_string($progress) && strpos((string)$progress, '-') === 0;
                            $progressValue = $isTunggakan ? ltrim($progress, '-') : $progress;

                            // Tentukan warna
                            if ($isTunggakan) {
                                $color = '#dc3545'; // merah untuk tunggakan
                                $textClass = 'text-white';
                                $displayText = '-' . str_replace('.', ',', $progressValue) . '%';
                            } elseif ($progress < 25) {
                                $color = '#dc3545'; // merah
                                $textClass = 'text-dark';
                                $displayText = round($progress) . '%';
                            } elseif ($progress < 50) {
                                $color = '#fd7e14'; // oren
                                $textClass = 'text-dark';
                                $displayText = round($progress) . '%';
                            } elseif ($progress < 75) {
                                $color = '#ffc107'; // kuning
                                $textClass = 'text-dark';
                                $displayText = round($progress) . '%';
                            } else {
                                $color = '#28a745'; // hijau
                                $textClass = 'text-dark';
                                $displayText = round($progress) . '%';
                            }
                        @endphp

                        {{-- Badge dengan warna sama seperti progress bar --}}
                        <span class="badge {{ $textClass }}" style="background-color: {{ $color }};">
                            {{ $displayText }}
                        </span>

                        {{-- Progress bar kecil --}}
                        @if(!$isTunggakan)
                            <div style="background:#eee; border-radius:5px; width:100px; height:10px; overflow:hidden; margin-top:5px;">
                                <div style="background: {{ $color }}; width:{{ round($progress) }}%; height:100%;"></div>
                            </div>
                        @endif
                    </td>
                    <td>{{ $siswa->status_siswa }}</td>
                    <td>
                        <div class='btn-group'>
                            <a href="{{ route('siswas.show', $siswa->id) }}" class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            @hasanyrole('admin|super-admin')
                                <a href="{{ route('siswas.edit', $siswa->id) }}" class='btn btn-default btn-xs'>
                                    <i class="far fa-edit"></i>
                                </a>
                                {!! Form::open(['route' => ['siswas.destroy', $siswa->id], 'method' => 'delete', 'style' => 'display:inline']) !!}
                                    {!! Form::button('<i class="far fa-trash-alt"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-danger btn-xs',
                                        'onclick' => "return confirm('Are you sure?')"
                                    ]) !!}
                                {!! Form::close() !!}
                            @endhasanyrole
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
