<h2 style="font-weight: bold; margin-bottom: 16px;">
    Laporan Hasil Skrining
</h2>

<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Kader</th>
            <th>No KK</th>
            <th>Kepala Keluarga</th>
            <th>Nama Anggota</th>
            <th>Siklus</th>

            @foreach($questions as $q)
                <th>{{ $q }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $index => $row)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $row['kader'] }}</td>
            <td>{{ $row['no_kk'] }}</td>
            <td>{{ $row['kepala_keluarga'] }}</td>
            <td>{{ $row['nama_anggota'] }}</td>
            <td>{{ $row['siklus'] }}</td>

            @foreach($questions as $q)
                <td>{{ $row[$q] ?? '-' }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>