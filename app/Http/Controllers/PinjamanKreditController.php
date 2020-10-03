<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\TransaksiHarian;
use Tanggal;
use App\Periode;
use App\TransaksiHarianAnggota;
use App\TransaksiHarianBiaya;
use Money;
use App\TransaksiPinjaman;
use App\Imports\PinjamanKredit;
use Maatwebsite\Excel\Facades\Excel;
use File;
use Session;
use Illuminate\Support\Facades\DB;
use App\Anggota;

class PinjamanKreditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        if($request->ajax())
        {
            $transaksiHarian = TransaksiHarian::with([
                'divisi', 'nama_anggota' => function($sql){
                    $sql->with('anggota');
                }, 'sumKreditPinjaman'
            ])
                ->where('divisi_id', '2')
                ->where('jenis_transaksi', '2');
            return DataTables::of($transaksiHarian)
                ->editColumn('tgl', function($transaksiHarian){
                    return Tanggal::tanggal_id($transaksiHarian->tgl);
                })
                ->editColumn('jenis_pembayaran', function($transaksiHarian){
                    if($transaksiHarian->jenis_pembayaran == '1')
                    {
                        return '<span class="badge badge-success badge-pill">Cash</span>';
                    }else {
                        return '<span class="badge badge-danger badge-pill">Bank</span>';
                    }
                })
                ->editColumn('jenis_transaksi', function($transaksiHarian){
                    return '<span class="badge badge-info badge-pill">Kredit</span>';
                })
                ->addColumn('action', function($transaksiHarian){
                    return view('datatable._action-default', [
                        'model' => $transaksiHarian,
                        'form_url' => route('pinjaman-kredit.destroy', $transaksiHarian->id),
                        'edit_url' => route('pinjaman-kredit.edit', $transaksiHarian->id),
                        'confirm_message' => 'Apakah anda yakin mau Transaksi'
                    ]);
                })
                ->editColumn('is_close', function($transaksiHarian){
                    if($transaksiHarian->is_close == '0')
                    {
                        return '<p class="text-primary">Aktif</p>';
                    }else {
                        return '<p class="text-danger">None Aktif</p>';
                    }
                })
                ->editColumn('sumKreditPinjaman', function($transaksiHarian){
                    return Money::stringToRupiah($transaksiHarian->sumKreditPinjaman->sum('nominal'));
                })
                ->rawColumns(['jenis_pembayaran', 'jenis_transaksi', 'action', 'is_close'])
                ->make(true);
        }
        return view('pinjaman-kredit.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('pinjaman-kredit.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'tgl' => 'required',
            'divisi_id' => 'required',
            'jenis_transaksi' => 'required'
        ]);

        //Save Transaction Kopkar
        $periode = Periode::where('status', '1')->first();
        $transaksiHarian = new TransaksiHarian();
        $transaksiHarian->tgl = Tanggal::convert_tanggal($request->tgl);
        $transaksiHarian->divisi_id = $request->divisi_id;
        $transaksiHarian->jenis_pembayaran = $request->jenis_pembayaran;
        $transaksiHarian->jenis_transaksi = $request->jenis_transaksi;
        $transaksiHarian->keterangan = $request->keterangan;
        $transaksiHarian->periode_id = $periode->id;
        $transaksiHarian->save();

        //Save Transation Member Kopkar
        $transaksi_harian_anggota = new TransaksiHarianAnggota();
        $transaksi_harian_anggota->transaksi_harian_id = $transaksiHarian->id;
        $transaksi_harian_anggota->anggota_id = $request->anggota_id;
        $transaksi_harian_anggota->save();

        $transaksi_biaya = new TransaksiHarianBiaya();
        $transaksi_biaya->biaya_id = $request->biaya_id;
        $transaksi_biaya->transaksi_harian_id = $transaksiHarian->id;
        $transaksi_biaya->nominal = Money::rupiahToString($request->nominal);
        $transaksi_biaya->save();

        $transaksiPinjaman = new TransaksiPinjaman();
        $transaksiPinjaman->transaksi_harian_biaya_id = $transaksi_biaya->id;
        $transaksiPinjaman->lama_cicilan = $request->lama_cicilan;
        $transaksiPinjaman->save();

        Session::flash("flash_notification", [
            "level" => "success",
            "message" => "Berhasil Menambah Data Transaksi !!!"
        ]);
        activity()->log('Menambahkan Data Pinjaman Kredit');
        return redirect()->route('pinjaman-kredit.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $transaksiHarian = DB::table('transaksi_harians')
            ->join('transaksi_harian_biayas', 'transaksi_harian_biayas.transaksi_harian_id', '=', 'transaksi_harians.id')
            ->join('transaksi_harian_anggotas', 'transaksi_harian_anggotas.transaksi_harian_id', '=', 'transaksi_harians.id')
            ->join('transaksi_pinjamen', 'transaksi_pinjamen.transaksi_harian_biaya_id', '=', 'transaksi_harian_biayas.id')
            ->where('transaksi_harians.id', $id)
            ->first();
        $transaksiHarian->nominal = Money::stringToRupiah($transaksiHarian->nominal);
        $transaksiHarian->tgl = date('d-m-Y', strtotime($transaksiHarian->tgl));
        return view('pinjaman-kredit.edit')->with(compact('transaksiHarian'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'tgl' => 'required',
            'divisi_id' => 'required',
            'jenis_transaksi' => 'required'
        ]);

        //Save Transaction Kopkar
        $transaksiHarian = TransaksiHarian::find($id);
        $transaksiHarian->tgl = Tanggal::convert_tanggal($request->tgl);
        $transaksiHarian->jenis_pembayaran = $request->jenis_pembayaran;
        $transaksiHarian->jenis_transaksi = $request->jenis_transaksi;
        $transaksiHarian->keterangan = $request->keterangan;
        $transaksiHarian->update();

        //Save Transation Member Kopkar
        $transaksi_harian_anggota = TransaksiHarianAnggota::where('transaksi_harian_id', $id)->first();
        $transaksi_harian_anggota->anggota_id = $request->anggota_id;
        $transaksi_harian_anggota->update();

        $transaksi_biaya = TransaksiHarianBiaya::where('transaksi_harian_id', $id)->first();
        $transaksi_biaya->biaya_id = $request->biaya_id;
        $transaksi_biaya->nominal = Money::rupiahToString($request->nominal);
        $transaksi_biaya->update();

        $transaksiPinjaman = TransaksiPinjaman::where('transaksi_harian_biaya_id', $transaksi_biaya->id)->first();
        $transaksiPinjaman->transaksi_harian_biaya_id = $transaksi_biaya->id;
        $transaksiPinjaman->lama_cicilan = $request->lama_cicilan;
        $transaksiPinjaman->update();

        Session::flash("flash_notification", [
            "level" => "success",
            "message" => "Berhasil Merubah Data Transaksi !!!"
        ]);
        activity()->log('Merubah Data Pinjaman Kredit');
        return redirect()->route('pinjaman-kredit.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        TransaksiHarianAnggota::where('transaksi_harian_id', $id)->delete();
        $biaya_id = TransaksiHarianBiaya::where('transaksi_harian_id', $id)->firstOrFail();
        TransaksiPinjaman::where('transaksi_harian_biaya_id', $biaya_id->id)->delete();
        TransaksiHarianBiaya::where('transaksi_harian_id', $id)->delete();
        TransaksiHarian::find($id)->delete();
        Session::flash("flash_notification", [
            "level" => "success",
            "message" => "Berhasil Menghapus Transaksi !!!"
        ]);
        activity()->log('Menghapus Data Pinjaman Kredit');
        return redirect()->route('pinjaman-kredit.index');
    }

    public function upload()
    {
        return view('pinjaman-kredit.upload');
    }

    public function doUpload(Request $request)
    {
		/*
		//Bug Pada Linux centos tidak bisa di Validasi Mime nya
        $this->validate($request, [
			'file' => 'required|mimes:csv,xls,xlsx'
		]);
		*/
        $periode = Periode::where('status', '1')->first();
		$file = $request->file('file');
        // menangkap file excel

        // membuat nama file unik
		$nama_file = rand().$file->getClientOriginalName();

		// upload ke folder file_siswa di dalam folder public
		$file->move('pinjaman_kredit',$nama_file);

		// import data
        Excel::import(new PinjamanKredit($periode), public_path('/pinjaman_kredit/'.$nama_file));

        File::delete(public_path('/pinjaman_kredit/'.$nama_file));
		// notifikasi dengan session
		//Session::flash('sukses','Data Siswa Berhasil Di import!');

        // alihkan halaman kembali
        activity()->log('Upload Data Pinjaman Kredit');
		return redirect()->route('pinjaman-kredit.index');
    }

    public function closeBook()
    {
        $periode = Periode::where('status', '1')->first();
        $want_close = Periode::where('status', 2)->first();
        $anggota = Anggota::select()->where('status', 1)->get();
        foreach($anggota as $row)
        {
            $transaksiHarian = new TransaksiHarian();
            $transaksiHarian->tgl = Tanggal::convert_tanggal($periode->open_date);
            $transaksiHarian->divisi_id = '2';
            $transaksiHarian->jenis_pembayaran = '1';
            $transaksiHarian->jenis_transaksi = '2';
            $transaksiHarian->keterangan = 'Saldo Awal Periode '.$periode->name;
            $transaksiHarian->periode_id = $periode->id;
            $transaksiHarian->save();
            //SUMP SIMPANAN KREDIT
            $sum_pinjaman = DB::table('transaksi_harians')
                            ->join('transaksi_harian_biayas', 'transaksi_harians.id', '=', 'transaksi_harian_biayas.transaksi_harian_id')
                            ->join('transaksi_harian_anggotas', 'transaksi_harians.id', '=', 'transaksi_harian_anggotas.transaksi_harian_id')
                            ->whereBetween('transaksi_harians.tgl', [$want_close->open_date, $want_close->close_date])
                            ->where('transaksi_harian_anggotas.anggota_id', $row->id)
                            ->where('transaksi_harian_biayas.biaya_id', '8')
                            ->where('divisi_id', '2')
                            ->sum('transaksi_harian_biayas.nominal');
            //Store Saldo Pinjaman
            $transaksi_biaya = new TransaksiHarianBiaya();
            $transaksi_biaya->biaya_id = '8';
            $transaksi_biaya->transaksi_harian_id = $transaksiHarian->id;
            $transaksi_biaya->nominal = $sum_pinjaman;
            $transaksi_biaya->save();
            
            //Save Transation Member Kopkar
            $transaksi_harian_anggota = new TransaksiHarianAnggota();
            $transaksi_harian_anggota->transaksi_harian_id = $transaksiHarian->id;
            $transaksi_harian_anggota->anggota_id = $row->id;
            $transaksi_harian_anggota->save();

            //Update Simpanan All
            $transaksi_harian = DB::table('transaksi_harians')
                    ->join('transaksi_harian_biayas', 'transaksi_harians.id', '=', 'transaksi_harian_biayas.transaksi_harian_id')
                    ->join('transaksi_harian_anggotas', 'transaksi_harians.id', '=', 'transaksi_harian_anggotas.transaksi_harian_id')
                    ->whereBetween('transaksi_harians.tgl', [$want_close->open_date, $want_close->close_date])
                    ->where('transaksi_harian_anggotas.anggota_id', $row->id)
                    ->where('divisi_id', '2')
                    ->where('transaksi_harian_biayas.biaya_id', '8')
                    ->select('transaksi_harians.id as id')
                    ->get();
            
            foreach($transaksi_harian as $item)
            {
                $transaksi_harian = TransaksiHarian::find($item->id);
                $transaksi_harian->is_close = '1';
                $transaksi_harian->update();
            }
        }
        activity()->log('Tutup Buku Data Pinjaman Kredit');
        return redirect()->route('pinjaman-kredit.index');
    }

}