{{-- Excel-specific view for BPPajak export --}}
<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-weight: bold; font-size: 14px;">
                BUKU PEMBANTU PAJAK
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; font-weight: bold; font-size: 14px;">
                BENDAHARA PENGELUARAN PEMBANTU
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; font-weight: bold;">
                PERIODE {{ \Carbon\Carbon::parse($tanggalawal)->translatedFormat('j F Y') }} -
                {{ \Carbon\Carbon::parse($tanggalakhir)->translatedFormat('j F Y') }}
            </th>
        </tr>
        <tr>
            <th colspan="7"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #4a90e2;">No</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #4a90e2;">Tanggal</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #4a90e2;">No Bukti</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #4a90e2;">Uraian</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #4a90e2;">Penerimaan</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #4a90e2;">Pengeluaran</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #4a90e2;">Saldo</th>
        </tr>
    </thead>
    <tbody>
        @php
            $saldo = $saldoLalu ?? 0;
            $totalTerima = 0;
            $totalKeluar = 0;
            $rowNumber = 1;
        @endphp
        <tr>
            <td colspan="4" style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Saldo Sebelumnya</td>
            <td style="border: 1px solid #000000; background-color: #f2f2f2;"></td>
            <td style="border: 1px solid #000000; background-color: #f2f2f2;"></td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">{{ number_format($saldo, 2, ',', '.') }}</td>
        </tr>

        @foreach ($trhtransout as $item)
            @php
                $saldo += $item->terima - $item->keluar;
                $totalTerima += $item->terima;
                $totalKeluar += $item->keluar;
            @endphp
            <tr>
                <td style="text-align: center; border: 1px solid #000000;">{{ $rowNumber++ }}</td>
                <td style="border: 1px solid #000000;">{{ \Carbon\Carbon::parse($item->tgl_bukti)->translatedFormat('j F Y') }}</td>
                <td style="border: 1px solid #000000;">{{ $item->no_pajak }}</td>
                <td style="border: 1px solid #000000;">{{ $item->uraian }}</td>
                <td style="text-align: right; border: 1px solid #000000;">{{ number_format($item->terima, 2, ',', '.') }}</td>
                <td style="text-align: right; border: 1px solid #000000;">{{ number_format($item->keluar, 2, ',', '.') }}</td>
                <td style="text-align: right; border: 1px solid #000000;">{{ number_format($saldo, 2, ',', '.') }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="4" style="font-weight: bold; text-align: right; border: 1px solid #000000;">Total</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000000;">{{ number_format($totalTerima, 2, ',', '.') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000000;">{{ number_format($totalKeluar, 2, ',', '.') }}</td>
            <td style="border: 1px solid #000000;"></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td colspan="5" style="text-align: center;">Pontianak, {{ \Carbon\Carbon::parse($tanggalTtd)->translatedFormat('j F Y') }}</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">{{ $pa_kpa->jabatan ?? 'PA/KPA' }}</td>
            <td colspan="2"></td>
            <td colspan="3" style="text-align: center;">{{ $bendahara->jabatan ?? 'Bendahara' }}</td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; font-weight: bold;">{{ $pa_kpa->nama ?? '' }}</td>
            <td colspan="2"></td>
            <td colspan="3" style="text-align: center; font-weight: bold;">{{ $bendahara->nama ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">{{ $pa_kpa->pangkat ?? '' }}</td>
            <td colspan="2"></td>
            <td colspan="3" style="text-align: center;">{{ $bendahara->pangkat ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">NIP. {{ $pa_kpa->nip ?? '' }}</td>
            <td colspan="2"></td>
            <td colspan="3" style="text-align: center;">NIP. {{ $bendahara->nip ?? '' }}</td>
        </tr>
    </tfoot>
</table>
