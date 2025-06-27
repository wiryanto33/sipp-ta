<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Form Penilaian Sidang</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            color: #000;
            margin: 20 px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        table {
            margin-top: 50px;
            width: 100%;
            border-collapse: collapse;
        }

        th {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }

        .nilai-total {
            width: 50%;
            margin: 20px auto;
            font-weight: bold;
        }

        .nilai-total td {
            border: 1px solid black;
            padding: 6px;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header">
        <h3>FORMULIR PENILAIAN SIDANG TUGAS AKHIR</h3>
        <h4>
            PROGRAM STUDI
            {{ strtoupper($penilaianSidang->jadwalSidang->tugasAkhir->mahasiswa->prodi->jenjang . ' ' . $penilaianSidang->jadwalSidang->tugasAkhir->mahasiswa->prodi->name) }}
        </h4>
        <h4>SEKOLAH TINGGI TEKNOLOGI ANGKATAN LAUT</h4>
    </div>

    <!-- Informasi Mahasiswa -->
    <table class="info-table">
        <tr>
            <td><strong>Nama</strong></td>
            <td>: {{ $penilaianSidang->jadwalSidang->tugasAkhir->mahasiswa->user->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>NRP</strong></td>
            <td>: {{ $penilaianSidang->jadwalSidang->tugasAkhir->mahasiswa->user->nrp ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Judul</strong></td>
            <td>: {{ $penilaianSidang->jadwalSidang->tugasAkhir->judul ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Jenis Sidang</strong></td>
            <td>: {{ $penilaianSidang->jadwalSidang->jenis_sidang ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Sidang</strong></td>
            <td>: {{ \Carbon\Carbon::parse($penilaianSidang->jadwalSidang->tanggal)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Tempat/Ruang</strong></td>
            <td>: {{ $penilaianSidang->jadwalSidang->tempat_sidang ?? '-' }}/{{ $penilaianSidang->jadwalSidang->ruang_sidang ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Penguji</strong></td>
            <td>: {{ $penilaianSidang->pengujiSidang->dosen->user->name ?? '-' }}</td>
        </tr>
    </table>

    <!-- Tabel Penilaian -->
    <table border="1" cellspacing="0" cellpadding="5">
        <tr>
            <th rowspan="2">No</th>
            <th colspan="2">Yang Dinilai</th>
            <th rowspan="2">Nilai</th>
            <th rowspan="2">Nilai Rata Rata</th>
            <th rowspan="2">Bobot</th>
            <th rowspan="2">Nilai Penguji</th>
        </tr>
        <tr>
            <th>Obyek</th>
            <th>Komponen</th>
        </tr>

        <!-- No 1: Materi Skripsi -->
        <tr>
            <td rowspan="5">1</td>
            <td rowspan="5" class="object-cell">Materi Skripsi</td>
            <td class="component-cell">a. Originalitas Materi</td>
            <td class="value-cell">{{ $penilaianSidang->originalitas_materi }}</td>
            <td class="value-cell" rowspan="5">
                @php
                    $nilaiRataRata =
                        ($penilaianSidang->originalitas_materi +
                            $penilaianSidang->analisa_metodologi +
                            $penilaianSidang->tingkat_aplikasi_materi +
                            $penilaianSidang->pengembangan_kreativitas +
                            $penilaianSidang->tata_tulis) /
                        5;
                    echo number_format($nilaiRataRata, 2);
                @endphp
            </td>
            <td class="value-cell" rowspan="5">0.5</td>
            <td class="value-cell" rowspan="5">
                @php
                    $nilaiPenguji = $nilaiRataRata * 0.5;
                    echo number_format($nilaiPenguji, 2);
                @endphp
            </td>
        </tr>
        <tr>
            <td class="component-cell">b. Analisa dan Metodologi</td>
            <td class="value-cell">{{ $penilaianSidang->analisa_metodologi }}</td>


        </tr>
        <tr>
            <td class="component-cell">c. Tingkat Aplikasi Materi</td>
            <td class="value-cell">{{ $penilaianSidang->tingkat_aplikasi_materi }}</td>


        </tr>
        <tr>
            <td class="component-cell">d. Pengembangan daya Kreativitas</td>
            <td class="value-cell">{{ $penilaianSidang->pengembangan_kreativitas }}</td>
        </tr>
        <tr>
            <td class="component-cell">e. Tata Tulis</td>
            <td class="value-cell">{{ $penilaianSidang->tata_tulis }}</td>
        </tr>

        <!-- No 2: Kemampuan Presentasi -->
        <tr>
            <td rowspan="4">2</td>
            <td rowspan="4" class="object-cell">Penyajian</td>
            <td class="component-cell">a. Penguasaan Materi</td>
            <td class="value-cell">{{ $penilaianSidang->penguasaan_materi }}</td>
            <td class="value-cell" rowspan="4">
                @php
                    $nilaiRataRataPenyajian =
                        ($penilaianSidang->penguasaan_materi +
                            $penilaianSidang->sikap_dan_penampilan +
                            $penilaianSidang->penyajian_sarana_sistematika +
                            $penilaianSidang->hasil_yang_dicapai) /
                        4;

                    echo number_format($nilaiRataRataPenyajian, 2);
                @endphp
            </td>
            <td class="value-cell" rowspan="4">0.3</td>
            <td class="value-cell" rowspan="4">
                @php
                    $nilaiPengujiPenyajian = $nilaiRataRataPenyajian * 0.3;
                    echo number_format($nilaiPengujiPenyajian, 2);
                @endphp
            </td>
        </tr>
        <tr>
            <td class="component-cell">b. Sikap dan Penampilan</td>
            <td class="value-cell">{{ $penilaianSidang->sikap_dan_penampilan }}</td>
        </tr>
        <tr>
            <td class="component-cell">c. Penyiapan Sarana dan Sistematika</td>
            <td class="value-cell">{{ $penilaianSidang->penyajian_sarana_sistematika }}</td>
        </tr>
        <tr>
            <td class="component-cell">d. Hasil yang dicapai</td>
            <td class="value-cell">{{ $penilaianSidang->hasil_yang_dicapai }}</td>
        </tr>

        <!-- No 3: Sikap dan Etika -->
        <tr>
            <td rowspan="3">3</td>
            <td rowspan="3" class="object-cell">Diskusi dan tanya jawab</td>
            <td class="component-cell">a. Penguasaan Materi</td>
            <td class="value-cell">{{ $penilaianSidang->penguasaan_materi_diskusi }}</td>
            <td class="value-cell" rowspan="3">
                @php
                    $nilaiRataRataDiskusi =
                        ($penilaianSidang->penguasaan_materi_diskusi +
                            $penilaianSidang->objektivitas_tanggapan +
                            $penilaianSidang->kemampuan_mempertahankan_ide) /
                        3;
                    echo number_format($nilaiRataRataDiskusi, 2);
                @endphp
            </td>
            <td class="value-cell" rowspan="3">0.2</td>
            <td class="value-cell" rowspan="3">
                @php
                    $nilaiPengujiDiskusi = $nilaiRataRataDiskusi * 0.2;
                    echo number_format($nilaiPengujiDiskusi, 2);
                @endphp
            </td>
        </tr>
        <tr>
            <td class="component-cell">b. Obyektifitas dalam menanggapi pertanyaan</td>
            <td class="value-cell">{{ $penilaianSidang->objektivitas_tanggapan }}</td>
        </tr>
        <tr>
            <td class="component-cell">c. Kemampuan menjelaskan dan mempertahankan ide</td>
            <td class="value-cell">{{ $penilaianSidang->kemampuan_mempertahankan_ide }}</td>
        </tr>

        <!-- Total -->
        <tr class="total-row">
            <td colspan="3" style="text-align:center; font-weight:bold;">TOTAL NILAI</td>
            <td class="value-cell">
                @php
                    $totalNilai =
                        $penilaianSidang->originalitas_materi +
                        $penilaianSidang->analisa_metodologi +
                        $penilaianSidang->tingkat_aplikasi_materi +
                        $penilaianSidang->pengembangan_kreativitas +
                        $penilaianSidang->tata_tulis +
                        $penilaianSidang->penguasaan_materi +
                        $penilaianSidang->sikap_dan_penampilan +
                        $penilaianSidang->penyajian_sarana_sistematika +
                        $penilaianSidang->hasil_yang_dicapai +
                        $penilaianSidang->penguasaan_materi_diskusi +
                        $penilaianSidang->objektivitas_tanggapan +
                        $penilaianSidang->kemampuan_mempertahankan_ide;
                    echo $totalNilai;
                @endphp
            </td>
            <td class="value-cell">
                @php
                    $totalNilaiRataRata = $nilaiRataRata + $nilaiRataRataPenyajian + $nilaiRataRataDiskusi;
                    echo number_format($totalNilaiRataRata, 2);
                @endphp
            </td>
            <td class="value-cell"></td>
            <td class="value-cell">
                @php
                    $totalNilaiPenguji = $nilaiPenguji + $nilaiPengujiPenyajian + $nilaiPengujiDiskusi;
                    echo number_format($totalNilaiPenguji, 2);
                @endphp
            </td>
        </tr>
    </table>

    <!-- Tanda Tangan Penguji -->
    <div class="signature">
        <div class="signature-box">
            <p>Surabaya,
                {{ \Carbon\Carbon::parse($penilaianSidang->jadwalSidang->tanggal)->translatedFormat('d F Y') }}</p>
            <p>Penguji,</p>
            <div class="line">
                {{ $penilaianSidang->pengujiSidang->dosen->user->name ?? '-' }}
            </div>
        </div>
    </div>

</body>

</html>
