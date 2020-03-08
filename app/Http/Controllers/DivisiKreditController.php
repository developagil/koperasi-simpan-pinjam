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
use Session;
use Illuminate\Support\Facades\DB;
use App\Divisi;
use App\Biaya;

class DivisiKreditController extends Controller
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
            $transaksiHarian = TransaksiHarian::with(['divisi', 'sumKreditAll'])
                ->whereNotIn('divisi_id', ['1', '2'])
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
                        'form_url' => route('divisi-kredit.destroy', $transaksiHarian->id),
                        'edit_url' => route('divisi-kredit.edit', $transaksiHarian->id),
                        'confirm_message' => 'Apakah anda yakin mau Transaksi'
                    ]);
                })
                ->editColumn('sumKreditAll', function($transaksiHarian){
                    return Money::stringToRupiah($transaksiHarian->sumKreditAll->sum('nominal'));
                })
                ->editColumn('is_close', function($transaksiHarian){
                    if($transaksiHarian->is_close == '0')
                    {
                        return '<p class="text-primary">Aktif</p>';
                    }else {
                        return '<p class="text-danger">None Aktif</p>';
                    }
                })
                ->rawColumns(['jenis_pembayaran', 'jenis_transaksi', 'action', 'is_close'])
                ->make(true);
        }
        return view('divisi-kredit.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('divisi-kredit.create');
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
        $transaksi_biaya = new TransaksiHarianBiaya();
        $transaksi_biaya->biaya_id = $request->biaya_id;
        $transaksi_biaya->transaksi_harian_id = $transaksiHarian->id;
        $transaksi_biaya->nominal = Money::rupiahToString($request->nominal);
        $transaksi_biaya->save();
        
        Session::flash("flash_notification", [
            "level" => "success",
            "message" => "Berhasil Menambah Data Transaksi !!!"
        ]);

        return redirect()->route('divisi-kredit.index');
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
            ->where('transaksi_harians.id', $id)
            ->first();
        $transaksiHarian->nominal = Money::stringToRupiah($transaksiHarian->nominal);
        $transaksiHarian->tgl = date('d-m-Y', strtotime($transaksiHarian->tgl));
        return view('divisi-kredit.edit')->with(compact('transaksiHarian'));
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
        //dd(Tanggal::convert_tanggal($request->tgl));
        $transaksiHarian = TransaksiHarian::find($id);
        $transaksiHarian->tgl = Tanggal::convert_tanggal($request->tgl);
        $transaksiHarian->divisi_id = $request->divisi_id;
        $transaksiHarian->jenis_pembayaran = $request->jenis_pembayaran;
        $transaksiHarian->jenis_transaksi = $request->jenis_transaksi;
        $transaksiHarian->keterangan = $request->keterangan;
        $transaksiHarian->update();

        //Update Transaksi Biaya
        $transaksi_biaya = TransaksiHarianBiaya::where('transaksi_harian_id', $id)->first();
        $transaksi_biaya->biaya_id = $request->biaya_id;
        $transaksi_biaya->transaksi_harian_id = $transaksiHarian->id;
        $transaksi_biaya->nominal = Money::rupiahToString($request->nominal);
        $transaksi_biaya->save();

        Session::flash("flash_notification", [
            "level" => "success",
            "message" => "Berhasil Merubah Data Transaksi !!!"
        ]);

        return redirect()->route('divisi-kredit.index');
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
        TransaksiHarianBiaya::where('transaksi_harian_id', $id)->delete();
        TransaksiHarian::find($id)->delete();
        Session::flash("flash_notification", [
            "level" => "success",
            "message" => "Berhasil Menghapus Transaksi !!!"
        ]);
        return redirect()->route('divisi-kredit.index');
    }

    public function closeBook()
    {
        $periode = Periode::where('status', '1')->first();
        $want_close = Periode::where('status', 2)->first();
        $divisi = Divisi::select()->whereNotIn('id', [1, 2])->get();
        foreach($divisi as $row)
        {
            $biaya = Biaya::select()->where('divisi_id', $row->id)->where('jenis_biaya', 2)->first();
            $transaksiHarian = new TransaksiHarian();
            $transaksiHarian->tgl = Tanggal::convert_tanggal($periode->open_date);
            $transaksiHarian->divisi_id = $row->id;
            $transaksiHarian->jenis_pembayaran = '1';
            $transaksiHarian->jenis_transaksi = '2';
            $transaksiHarian->keterangan = 'Saldo Awal Periode '.$periode->name;
            $transaksiHarian->periode_id = $periode->id;
            $transaksiHarian->save();
            //SUMP SIMPANAN KREDIT
            $sum_kredit_divisi = DB::table('transaksi_harians')
                    ->join('transaksi_harian_biayas', 'transaksi_harians.id', '=', 'transaksi_harian_biayas.transaksi_harian_id')
                    ->whereBetween('transaksi_harians.tgl', [$want_close->open_date, $want_close->close_date])
                    ->where('transaksi_harian_biayas.biaya_id', $biaya->id)
                    ->where('divisi_id', $row->id)
                    ->sum('transaksi_harian_biayas.nominal');

            //Store Biaya Divisi Debet
            $transaksi_biaya = new TransaksiHarianBiaya();
            $transaksi_biaya->biaya_id = $biaya->id;
            $transaksi_biaya->transaksi_harian_id = $transaksiHarian->id;
            $transaksi_biaya->nominal = $sum_kredit_divisi;
            $transaksi_biaya->save();

            //Update Simpanan All
            $transaksi_harian = DB::table('transaksi_harians')
                                    ->join('transaksi_harian_biayas', 'transaksi_harians.id', '=', 'transaksi_harian_biayas.transaksi_harian_id')
                                    ->whereBetween('transaksi_harians.tgl', [$want_close->open_date, $want_close->close_date])
                                    ->where('transaksi_harian_biayas.biaya_id', $biaya->id)
                                    ->where('divisi_id', $row->id)
                                    ->select('transaksi_harians.id as id')
                                    ->get();
            
            foreach($transaksi_harian as $item)
            {
                $transaksi_harian = TransaksiHarian::find($item->id);
                $transaksi_harian->is_close = '1';
                $transaksi_harian->update();
            }
        }
        return redirect()->route('divisi-kredit.index');
    }
}
