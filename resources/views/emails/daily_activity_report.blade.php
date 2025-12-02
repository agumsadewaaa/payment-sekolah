<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Laporan Aktivitas Harian</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color:#222;">
    <h2 style="margin-bottom:4px;">Laporan Aktivitas Harian</h2>
    <p style="margin-top:0;">Tanggal: <strong>{{ $dateLabel }}</strong></p>

    @if($activities->isEmpty())
        <p>Tidak ada aktivitas tercatat pada tanggal tersebut.</p>
    @else
        <table width="100%" cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; font-size:14px;">
            <thead>
                <tr style="background:#f5f5f5;">
                    <th align="left">Waktu</th>
                    <th align="left">User</th>
                    <th align="left">Aksi</th>
                    <th align="left">Model</th>
                    <th align="left">ID</th>
                    <th align="left">Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $act)
                    <tr>
                        <td>{{ \Illuminate\Support\Carbon::parse($act->created_at)->format('d/m/Y H:i') }}</td>
                        <td>{{ optional($act->user)->name ?? ('User ID: ' . $act->user_id) }}</td>
                        <td>{{ $act->action }}</td>
                        <td>{{ class_basename($act->model_type) }}</td>
                        <td>{{ $act->model_id }}</td>
                        <td>{{ $act->description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p style="color:#777; margin-top:16px;">Email ini dikirim otomatis oleh sistem pada 00:01 setiap hari.</p>
</body>
</html>
