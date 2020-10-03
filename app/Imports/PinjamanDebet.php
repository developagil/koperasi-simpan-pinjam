<?php

namespace App\Imports;

use App\TransaksiHarian;
use Maatwebsite\Excel\Concerns\ToModel;

use Tanggal;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\TransaksiHarianAnggota;
use App\Anggota;
use App\TransaksiHarianBiaya;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\TransaksiPinjaman;
use Maatwebsite\Excel\Facades\Excel;
use File;
use App\Imports\SimpananDebet;

class PinjamanDebet implements ToCollection, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    protected $periode;
	protected $anggota;


    public function __construct($periode)
    {
        $this->periode = $periode;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            $transaksiHarian = TransaksiHarian::create([
                'divisi_id' => '2',
                'tgl' => Tanggal::transformDate($row[1]),
                'jenis_pembayaran' => $row[2],
                'keterangan' => $row[3],
                'jenis_transaksi' => '1',
                'periode_id' => $this->periode->id,
            ]);
			$this->anggota = Anggota::select()->where('nik', '=', $row[4])->value('id');
            TransaksiHarianAnggota::create([
                'transaksi_harian_id' => $transaksiHarian->id,
                'anggota_id' => $this->anggota,
            ]);

            for ($i=0; $i <= 2; $i++) {
                if($i === 1)
                {
                    TransaksiHarianBiaya::create([
                        'transaksi_harian_id' => $transaksiHarian->id,
                        'biaya_id' => '6',
                        'nominal' => $row[5]
                    ]);
                }
                if($i ===  2)
                {
                    TransaksiHarianBiaya::create([
                        'transaksi_harian_id' => $transaksiHarian->id,
                        'biaya_id' => '7',
                        'nominal' => $row[6]
                    ]);
                }
            }
        }
    }
}