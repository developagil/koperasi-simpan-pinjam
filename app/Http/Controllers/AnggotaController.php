<?php

namespace App\Http\Controllers;

use App\Anggota;
use Illuminate\Http\Request;
use DataTables;
use Tanggal;
use App\User;
use Illuminate\Support\Facades\DB;
use App\UserAnggota;
use App\Exports\AnggotaExport;
use Maatwebsite\Excel\Facades\Excel;

class AnggotaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        //
        if(\Auth::user()->can('manage-anggota'))
        {
            if($request->ajax())
            {
                $anggota = Anggota::select();
                return DataTables::of($anggota)
                    ->editColumn('status', function($anggota){
                        if($anggota->status == '0')
                        {
                            return '<span class="badge badge-danger badge-pill">Non Aktif</span>';
                        }else {
                            return '<span class="badge badge-gradient badge-pill">Aktif</span>';
                        }
                    })
                    ->editColumn('tgl_daftar', function($anggota){
                        return Tanggal::tanggal_id($anggota->tgl_daftar);
                    })
                    ->addColumn('action', function($anggota){
                        return view('datatable._nodelete', [
                            'edit_url' => route('anggota.edit', $anggota->id)
                        ]);
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }
            return view('anggota.index');
        }else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('anggota.create');
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
            'nik' => 'required|unique:anggotas',
            'nama' => 'required',
            'tgl_daftar' => 'required'
        ]);
        $anggota = new Anggota();
        $anggota->nik = $request->nik;
        $anggota->nama = $request->nama;
        $anggota->inisial = $request->inisial;
        $anggota->tgl_daftar = Tanggal::convert_tanggal($request->tgl_daftar);
        $anggota->status = $request->status;
        $anggota->homebase = $request->homebase;
        $anggota->save();

        $user = new User();
        $user->email = $request->nik;
        $user->password = bcrypt('Kopkar2019');
        $user->name = $request->nama;
        $user->save();

        DB::table('role_user')->insert([
            'user_id' => $user->id,
            'role_id' => '2',
            'user_type' => 'App\User'
        ]);

        $userAnggota = new UserAnggota();
        $userAnggota->anggota_id = $anggota->id;
        $userAnggota->user_id = $user->id;
        $userAnggota->save();
        activity()->log('Menambahkan Data Anggota');
        return redirect()->route('anggota.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Anggota  $anggota
     * @return \Illuminate\Http\Response
     */
    public function show(Anggota $anggota)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Anggota  $anggota
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $anggota = Anggota::find($id);
        $anggota->tgl_daftar = date('d-m-Y', strtotime($anggota->tgl_daftar));
        return view('anggota.edit')->with(compact('anggota'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Anggota  $anggota
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'nik' => 'required|unique:anggotas,nik,'.$id,
            'nama' => 'required',
            'tgl_daftar' => 'required'
        ]);
        $anggota = Anggota::find($id);
        $anggota->nik = $request->nik;
        $anggota->nama = $request->nama;
        $anggota->inisial = $request->inisial;
        $anggota->status = $request->status;
        $anggota->tgl_daftar = Tanggal::convert_tanggal($request->tgl_daftar);
        $anggota->homebase = $request->homebase;
        $anggota->update();

        $userAnggota = UserAnggota::where('anggota_id', $id)->first();

        $user = User::find($userAnggota->user_id);
        $user->email = $request->nik;
        $user->name = $request->nama;
        $user->save();
        activity()->log('Merubah Data Anggota');
        return redirect()->route('anggota.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Anggota  $anggota
     * @return \Illuminate\Http\Response
     */
    public function destroy(Anggota $anggota)
    {
        //
    }

    public function export()
    {
        $anggota = Anggota::select()->get();
        activity()->log('Mendowload Data Anggota');
        return Excel::download(new AnggotaExport($anggota), 'Anggota-Koperasi.xlsx');
    }
}