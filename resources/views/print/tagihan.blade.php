<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Daftar Tagihan — {{ $siswa->nama }}</title>
    <style>
        @page { size: A4; margin: 18mm 14mm 16mm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color:#111; margin:0; padding:0; }

        .kop table { width:100%; border:none; }
        .kop td { border:none; vertical-align:middle; }
        .kop img { width:90px; height:auto; }
        .kop .school-name { font-size:18px; font-weight:bold; }
        .kop .sub { font-size:12px; line-height:1.3; }
        .kop .divider { border-bottom:3px double #000; margin-top:4px; }

        .section-title { font-weight:bold; text-transform:uppercase; border-bottom:1px solid #333; padding-bottom:4px; margin:12px 0 6px; }
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:10px 24px; margin-bottom:14px; }

        table { width:100%; border-collapse:collapse; margin:8px 0 14px; font-size:12px; }
        th, td { border:1px solid #333; padding:6px 8px; }
        thead th { background:#eee; }
        td.num, th.num { text-align:right; white-space:nowrap; }
        td.center { text-align:center; }
        tbody tr:nth-child(even) td { background:#fafafa; }

        .footer { position:fixed; bottom:8mm; left:14mm; right:14mm; display:flex; justify-content:space-between; font-size:11px; color:#555; }
        .pagenum:before { content: counter(page); }
        .pagecount:before { content: counter(pages); }

        @media print { .no-print { display:none } }
    </style>
</head>
<body>
<div class="wrap">

    {{-- KOP SEKOLAH --}}
    <div class="kop">
        <table>
            <tr>
                <td style="width:100px; text-align:center;">
                    <img src="https://keuangankitayesa.com/logo.jpg" alt="Logo Sekolah" style="width:90px; height:auto;">
                </td>
                <td style="text-align:center;">
                    <div style="font-size:14px; font-weight:bold;">YAYASAN PENDIDIKAN EKONOMI (YPE)</div>
                    <div style="font-size:14px; font-weight:bold;">SEKOLAH MENENGAH KEJURUAN (SMK)</div>
                    <div class="school-name">SMK "YPE" SAMPANG</div>
                    <div style="font-size:14px; font-weight:bold;">TERAKREDITASI A</div>
                    <div style="margin-top:2px; font-size:13px; font-weight:bold;">PROGRAM KEAHLIAN:</div>
                    <div class="sub">
                        1. Akuntansi dan Keuangan Lembaga (AKL), 2. Manajemen Perkantoran dan Layanan Bisnis (MPLB),<br>
                        3. Teknologi Farmasi (TF), 4. Teknik Jaringan Komputer & Telekomunikasi (TJKT), 5. Teknik Otomotif (TO)
                    </div>
                    <div style="margin-top:3px; font-size:12px;">
                        Alamat : Jl. Gerilya No. 478 Telp. (0282) 697146, 697591 Fax : (0282) 697008 Sampang – Cilacap 53273
                    </div>
                    <div style="font-size:12px; font-style:italic;">
                        Website: <u>http://www.smkypesampang.sch.id</u> | Email: smkype_sampang@yahoo.co.id
                    </div>
                </td>
            </tr>
        </table>
        <div class="divider"></div>
    </div>

    {{-- DETAIL SISWA --}}
    <div class="section-title">Daftar Tagihan Siswa</div>
    <div class="grid-2">
        <div>
            <div><b>Nama</b>: {{ $siswa->nama }}</div>
            <div><b>NISN</b>: {{ $siswa->nisn }}</div>
        </div>
        <div>
            <div><b>Kelas</b>: {{ $siswa->kelas }}</div>
            <div><b>Jurusan</b>: {{ $siswa->jurusans ? $siswa->jurusans->jurusan : '-' }}</div>
        </div>
    </div>

    {{-- RINGKASAN PEMBAYARAN --}}
    <div style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; margin: 10px 0; border-radius: 4px;">
        <div style="font-weight: bold; margin-bottom: 8px; font-size: 13px;">Ringkasan Pembayaran:</div>
        <div class="grid-2" style="font-size: 12px;">
            <div>
                <div><b>Total Tagihan</b>: Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
                <div><b>Total Terbayar</b>: Rp {{ number_format($totalBayar, 0, ',', '.') }}</div>
            </div>
            <div>
                <div><b>Sisa Tagihan</b>: Rp {{ number_format($totalSisa, 0, ',', '.') }}</div>
                <div><b>Progress</b>: {{ round($progress) }}%</div>
            </div>
        </div>
    </div>

    @php
        // ===== Helper nama tagihan & uang
        $rupiah = fn($v) => 'Rp '.number_format((int)$v, 0, ',', '.');
        $namaTagihan = function($t) {
            return $t->nama_tagihan ?? $t->tagihan ?? $t->name ?? '-';
        };

        // ===== Kompatibilitas data:
        // Jika controller ngirim $belum & $lunas gunakan itu.
        // Jika hanya ada $tagihans, pecah manual.
        if (!isset($belum) || !isset($lunas)) {
            $collection = collect($tagihans ?? []);
            $belum = $collection->filter(fn($t) => ($t->status ?? '') === 'Belum Lunas')->values();
            $lunas = $collection->filter(fn($t) => ($t->status ?? '') === 'Lunas')->values();
        } else {
            $belum = collect($belum);
            $lunas = collect($lunas);
        }

        // Totals per kelompok
        $totBelum = [
            'nominal' => (int) $belum->sum('nominal'),
            'bayar'   => (int) $belum->sum('total_bayar'),
            'sisa'    => (int) $belum->sum('sisa'),
        ];
        $totLunas = [
            'nominal' => (int) $lunas->sum('nominal'),
            'bayar'   => (int) $lunas->sum('total_bayar'),
        ];
    @endphp

    {{-- TAGIHAN BELUM LUNAS --}}
    <div class="section-title">Daftar Tagihan Belum Lunas</div>
    <table>
        <thead>
            <tr>
                <th>Nama Tagihan</th>
                <th class="num">Nominal</th>
                <th class="num">Total Bayar</th>
                <th class="num">Sisa</th>
                <th class="center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($belum as $t)
                <tr>
                    <td>{{ $namaTagihan($t) }}</td>
                    <td class="num">{{ $rupiah($t->nominal ?? 0) }}</td>
                    <td class="num">{{ $rupiah($t->total_bayar ?? 0) }}</td>
                    <td class="num">{{ $rupiah($t->sisa ?? 0) }}</td>
                    <td class="center">Belum</td>
                </tr>
                @if(isset($t->pembayaran) && count($t->pembayaran) > 0)
                <tr>
                    <td colspan="5" style="padding: 0; background: #f9f9f9;">
                        <table style="width: 100%; margin: 0; border: none;">
                            <thead>
                                <tr style="background: #e9ecef;">
                                    <th style="border: none; padding: 4px 8px; font-size: 11px;">Detail</th>
                                    <th style="border: none; padding: 4px 8px; font-size: 11px;">Tanggal Bayar</th>
                                    <th style="border: none; padding: 4px 8px; font-size: 11px;">Metode</th>
                                    <th style="border: none; padding: 4px 8px; font-size: 11px; text-align: right;">Jumlah Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($t->pembayaran as $i => $p)
                                <tr>
                                    <td style="border: none; padding: 3px 8px; font-size: 10px;">Angsuran {{ $i+1 }}</td>
                                    <td style="border: none; padding: 3px 8px; font-size: 10px;">{{ \Carbon\Carbon::parse($p->tanggal)->format('d-M-Y') }}</td>
                                    <td style="border: none; padding: 3px 8px; font-size: 10px;">{{ $p->metode_pembayaran ?? '-' }}</td>
                                    <td style="border: none; padding: 3px 8px; font-size: 10px; text-align: right;">{{ $rupiah($p->nominal) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
                @endif
            @empty
                <tr><td colspan="5" class="center">Tidak ada tagihan belum lunas</td></tr>
            @endforelse
            @if($belum->isNotEmpty())
                <tr>
                    <th>Total</th>
                    <th class="num">{{ $rupiah($totBelum['nominal']) }}</th>
                    <th class="num">{{ $rupiah($totBelum['bayar']) }}</th>
                    <th class="num">{{ $rupiah($totBelum['sisa']) }}</th>
                    <th></th>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- TAGIHAN LUNAS --}}
    <div class="section-title">Daftar Tagihan Lunas</div>
    <table>
        <thead>
            <tr>
                <th>Nama Tagihan</th>
                <th class="num">Nominal</th>
                <th class="num">Total Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lunas as $t)
                <tr>
                    <td>{{ $namaTagihan($t) }}</td>
                    <td class="num">{{ $rupiah($t->nominal ?? 0) }}</td>
                    <td class="num">{{ $rupiah($t->total_bayar ?? 0) }}</td>
                </tr>
                @if(isset($t->pembayaran) && count($t->pembayaran) > 0)
                <tr>
                    <td colspan="3" style="padding: 0; background: #f9f9f9;">
                        <table style="width: 100%; margin: 0; border: none;">
                            <thead>
                                <tr style="background: #e9ecef;">
                                    <th style="border: none; padding: 4px 8px; font-size: 11px;">Detail</th>
                                    <th style="border: none; padding: 4px 8px; font-size: 11px;">Tanggal Bayar</th>
                                    <th style="border: none; padding: 4px 8px; font-size: 11px;">Metode</th>
                                    <th style="border: none; padding: 4px 8px; font-size: 11px; text-align: right;">Jumlah Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($t->pembayaran as $i => $p)
                                <tr>
                                    <td style="border: none; padding: 3px 8px; font-size: 10px;">Angsuran {{ $i+1 }}</td>
                                    <td style="border: none; padding: 3px 8px; font-size: 10px;">{{ \Carbon\Carbon::parse($p->tanggal)->format('d-M-Y') }}</td>
                                    <td style="border: none; padding: 3px 8px; font-size: 10px;">{{ $p->metode_pembayaran ?? '-' }}</td>
                                    <td style="border: none; padding: 3px 8px; font-size: 10px; text-align: right;">{{ $rupiah($p->nominal) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
                @endif
            @empty
                <tr><td colspan="3" class="center">Tidak ada tagihan lunas</td></tr>
            @endforelse
            @if($lunas->isNotEmpty())
                <tr>
                    <th>Total</th>
                    <th class="num">{{ $rupiah($totLunas['nominal']) }}</th>
                    <th class="num">{{ $rupiah($totLunas['bayar']) }}</th>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <div>Dicetak: {{ now()->format('d-m-Y H:i') }}</div>
        <div>Hal. <span class="pagenum"></span> / <span class="pagecount"></span></div>
    </div>
</div>
</body>
</html>
