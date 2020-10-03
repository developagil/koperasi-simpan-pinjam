<?php

use Illuminate\Database\Seeder;
use Laratrust\Models\LaratrustRole as Role;
use App\User;
use App\RoleUser;
use Laratrust\Models\LaratrustPermission as Permission;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission = [
            [
                'name' => 'manage-anggota',
                'display_name' => 'Manage Anggota',
                'description' => 'Bisa Memanage Anggota'
            ],
            [
                'name' => 'create-anggota',
                'display_name' => 'create Anggota',
                'description' => 'Bisa Membuat Anggota'
            ],
            [
                'name' => 'edit-anggota',
                'display_name' => 'edit anggota',
                'description' => 'Bisa Mengubah Anggota'
            ],
            [
                'name' => 'download-anggota',
                'display_name' => 'download anggota',
                'description' => 'Bisa Download Anggota'
            ],
            [
                'name' => 'manage-divi',
                'display_name' => 'Manage Divisi',
                'description' => 'Bisa Memanage Divis'
            ],
            [
                'name' => 'create-divisi',
                'display_name' => 'Create Divisi',
                'description' => 'Bisa Membuat Divisi'
            ],
            [
                'name' => 'edit-divisi',
                'display_name' => 'Edit Divisi',
                'description' => 'Bisa Mengubah Divisi'
            ],
            [
                'name' => 'manage-periode',
                'display_name' => 'Manage Periode',
                'description' => 'Bisa Memanage Periode'
            ],
            [
                'name' => 'create-periode',
                'display_name' => 'Create Periode',
                'description' => 'Bisa Membuat Periode'
            ],
            [
                'name' => 'edit-periode',
                'display_name' => 'Edit Periode',
                'description' => 'Bisa Mengubah Periode'
            ],
            [
                'name' => 'manage-biaya',
                'display_name' => 'Manage biaya',
                'description' => 'Bisa Memanage biaya'
            ],
            [
                'name' => 'create-biaya',
                'display_name' => 'create biaya',
                'description' => 'Bisa Membuat biaya'
            ],
            [
                'name' => 'manage-debet-simpanan',
                'display_name' => 'Manage Debet Simpanan',
                'description' => 'Bisa Memanage debet simpanan'
            ],
            [
                'name' => 'create-debet-simpanan',
                'display_name' => 'Buat Debet Simpanan',
                'description' => 'Bisa Membuat debet simpanan'
            ],
            [
                'name' => 'edit-debet-simpanan',
                'display_name' => 'Edit Debet Simpanan',
                'description' => 'Bisa Mengubah debet simpanan'
            ],
            [
                'name' => 'download-debet-simpanan',
                'display_name' => 'Download Debet simpanan',
                'description' => 'Bisa Mendownload debet simpanan'
            ],
            [
                'name' => 'upload-debet-simpanan',
                'display_name' => 'Upload Debet Simpanan',
                'description' => 'Bisa Upload debet simpanan'
            ],
            [
                'name' => 'manage-kredit-simpanan',
                'display_name' => 'Manage Kredit Simpanan',
                'description' => 'Bisa Memanage kredit simpanan'
            ],
            [
                'name' => 'create-kredit-simpanan',
                'display_name' => 'Create Kredit Simpanan',
                'description' => 'Bisa Membuat kredit simpanan'
            ],
            [
                'name' => 'edit-kredit-simpanan',
                'display_name' => 'Edit Credit Simpanan',
                'description' => 'Bisa Mengubah kredit simpanan'
            ],
            [
                'name' => 'download-kredit-simpanan',
                'display_name' => 'Download Kredit Simpanan',
                'description' => 'Bisa Mendownload kredit simpanan'
            ],
            [
                'name' => 'upload-kredit-simpanan',
                'display_name' => 'Upload Kredit Simpanan',
                'description' => 'Bisa Upload kredit simpanan'
            ],
            [
                'name' => 'manage-debet-pinjaman',
                'display_name' => 'Manage Debet Pinjaman',
                'description' => 'Bisa Memanage debet pinjaman'
            ],
            [
                'name' => 'create-debet-pinjaman',
                'display_name' => 'Create Debet Pinjaman',
                'description' => 'Bisa Membuat debet pinjaman'
            ],
            [
                'name' => 'edit-debet-pinjaman',
                'display_name' => 'Edit Debet Pinjaman',
                'description' => 'Bisa Mengubah debet pinjaman'
            ],
            [
                'name' => 'download-debet-pinjaman',
                'display_name' => 'Download Debet Pinjaman',
                'description' => 'Bisa Mendownload debet pinjaman'
            ],
            [
                'name' => 'upload-debet-pinjaman',
                'display_name' => 'Upload Debet Pinjaman',
                'description' => 'Bisa Upload debet pinjaman'
            ],
            [
                'name' => 'manage-kredit-pinjaman',
                'display_name' => 'Manage Debet Pinjaman',
                'description' => 'Bisa Memanage kredit pinjaman'
            ],
            [
                'name' => 'create-kredit-pinjaman',
                'display_name' => 'Create Kredit Pinjaman',
                'description' => 'Bisa Membuat kredit pinjaman'
            ],
            [
                'name' => 'edit-kredit-pinjaman',
                'display_name' => 'Edit Kredit Pinjaman',
                'description' => 'Bisa Mengubah kredit pinjaman'
            ],
            [
                'name' => 'download-kredit-pinjaman',
                'display_name' => 'Download Kredit Pinjaman',
                'description' => 'Bisa Mendownload kredit pinjaman'
            ],
            [
                'name' => 'upload-kredit-pinjaman',
                'display_name' => 'Upload Kredit Pinjaman',
                'description' => 'Bisa Upload kredit pinjaman'
            ],
            [
                'name' => 'manage-debet-divisi',
                'display_name' => 'Manage Debet Divisi',
                'description' => 'Bisa Memanage debet divisi'
            ],
            [
                'name' => 'create-debet-divisi',
                'display_name' => 'Create Debet Divisi',
                'description' => 'Bisa Membuat debet divisi'
            ],
            [
                'name' => 'edit-debet-divisi',
                'display_name' => 'Edit Debet Divisi',
                'description' => 'Bisa Mengubah debet divisi'
            ],
            [
                'name' => 'manage-debet-kredit',
                'display_name' => 'Manage debet kredit',
                'description' => 'Bisa Memanage debet kredit'
            ],
            [
                'name' => 'create-debet-kredit',
                'display_name' => 'Create Debet Kredit',
                'description' => 'Bisa Membuat debet kredit'
            ],
            [
                'name' => 'edit-debet-kredit',
                'display_name' => 'Edit Debet Kredit',
                'description' => 'Bisa Mengubah debet kredit'
            ],
            [
                'name' => 'manage-laporan-kas-bank',
                'display_name' => 'Manage laporan kas bank',
                'description' => 'Bisa Memanage laporan kas bank'
            ],
            [
                'name' => 'search-laporan-kas-bank',
                'display_name' => 'search laporan kas bank',
                'description' => 'Bisa Mencari laporan kas bank'
            ],
            [
                'name' => 'excell-laporan-kas-bank',
                'display_name' => 'excell laporan kas bank',
                'description' => 'Bisa Download laporan kas bank'
            ],
            [
                'name' => 'manage-laporan-simpanan-all',
                'display_name' => 'Manage laporan simpanan all',
                'description' => 'Bisa Memanage laporan simpanan all'
            ],
            [
                'name' => 'search-laporan-simpanan-all',
                'display_name' => 'Search Laporan Simpanan All',
                'description' => 'Bisa Mencari laporan simpanan all'
            ],
            [
                'name' => 'excell-laporan-simpanan-all',
                'display_name' => 'excell laporan simpanan all',
                'description' => 'Bisa Download laporan simpanan all'
            ],
            [
                'name' => 'manage-laporan-pinjaman-all',
                'display_name' => 'Manage laporan pinjaman all',
                'description' => 'Bisa Memanage laporan pinjaman all'
            ],
            [
                'name' => 'search-laporan-pinjaman-all',
                'display_name' => 'search laporan pinjaman all',
                'description' => 'Bisa Mencari laporan pinjaman all'
            ],
            [
                'name' => 'excell-laporan-pinjaman-all',
                'display_name' => 'excell laporan pinjaman all',
                'description' => 'Bisa Download laporan pinjaman all'
            ],
            [
                'name' => 'manage-simpanan-anggota',
                'display_name' => 'Manage simpanan anggota',
                'description' => 'Bisa Memanage simpanan anggota'
            ],
            [
                'name' => 'search-simpanan-anggota',
                'display_name' => 'search simpanan anggota',
                'description' => 'Bisa Mencari simpanan anggota'
            ],
            [
                'name' => 'excell-simpanan-anggota',
                'display_name' => 'Excell Simpanan Anggota',
                'description' => 'Bisa Download simpanan anggota'
            ],
            [
                'name' => 'manage-pinjaman-anggota',
                'display_name' => 'Manage pinjaman anggota',
                'description' => 'Bisa Memanage pinjaman anggota'
            ],
            [
                'name' => 'search-pinjaman-anggota',
                'display_name' => 'Search Pinjaman Anggota',
                'description' => 'Bisa Mencari pinjaman anggota'
            ],
            [
                'name' => 'excell-pinjaman-anggota',
                'display_name' => 'excell pinjaman anggota',
                'description' => 'Bisa Download pinjaman anggota'
            ],
            [
                'name' => 'manage-laporan-devisi',
                'display_name' => 'Manage laporan devisi',
                'description' => 'Bisa Memanage laporan devisi'
            ],
            [
                'name' => 'search-laporan-devisi',
                'display_name' => 'search laporan devisi',
                'description' => 'Bisa Mencari laporan devisi'
            ],
            [
                'name' => 'excell-laporan-devisi',
                'display_name' => 'excell laporan devisi',
                'description' => 'Bisa Download laporan devisi'
            ],
            [
                'name' => 'manage-user',
                'display_name' => 'Manage user',
                'description' => 'Bisa Memanage user'
            ],
            [
                'name' => 'edit-user',
                'display_name' => 'edit user',
                'description' => 'Bisa Mengubah user'
            ],
            [
                'name' => 'create-user',
                'display_name' => 'create user',
                'description' => 'Bisa menambah user'
            ],
            [
                'name' => 'manage-option',
                'display_name' => 'Manage option',
                'description' => 'Bisa Memanage option'
            ],
            [
                'name' => 'edit-option',
                'display_name' => 'edit option',
                'description' => 'Bisa Mengubah option'
            ],
            [
                'name' => 'create-option',
                'display_name' => 'create option',
                'description' => 'Bisa menambah option'
            ],
        ];

        foreach ($permission as $key) {

            Permission::create([
                'name' => $key['name'],
                'display_name' => $key['display_name'],
                'description' => $key['description']
            ]);
        }

        //Adminstrator Rules
        Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Ini Adalah Role Admin'
        ]);

        $adminRole = Role::where('name', 'admin')->first();
        $adminPermission = [
            'create-anggota',
            'create-biaya',
            'create-debet-divisi',
            'create-debet-kredit',
            'create-debet-pinjaman',
            'create-debet-simpanan',
            'create-divisi',
            'create-kredit-pinjaman',
            'create-kredit-simpanan',
            'create-option',
            'create-periode',
            'create-user',
            'download-anggota',
            'download-debet-pinjaman',
            'download-debet-simpanan',
            'download-kredit-pinjaman',
            'download-kredit-simpanan',
            'edit-anggota',
            'edit-debet-divisi',
            'edit-debet-kredit',
            'edit-debet-pinjaman',
            'edit-debet-simpanan',
            'edit-divisi',
            'edit-kredit-pinjaman',
            'edit-kredit-simpanan',
            'edit-option',
            'edit-periode',
            'edit-user',
            'excell-laporan-devisi',
            'excell-laporan-kas-bank',
            'excell-laporan-pinjaman-all',
            'excell-laporan-simpanan-all',
            'excell-pinjaman-anggota',
            'excell-simpanan-anggota',
            'manage-anggota',
            'manage-biaya',
            'manage-debet-divisi',
            'manage-debet-kredit',
            'manage-debet-pinjaman',
            'manage-debet-simpanan',
            'manage-divi',
            'manage-kredit-pinjaman',
            'manage-kredit-simpanan',
            'manage-laporan-devisi',
            'manage-laporan-kas-bank',
            'manage-laporan-pinjaman-all',
            'manage-laporan-simpanan-all',
            'manage-option',
            'manage-periode',
            'manage-pinjaman-anggota',
            'manage-simpanan-anggota',
            'manage-user',
            'search-laporan-devisi',
            'search-laporan-kas-bank',
            'search-laporan-pinjaman-all',
            'search-laporan-simpanan-all',
            'search-pinjaman-anggota',
            'search-simpanan-anggota',
            'upload-debet-pinjaman',
            'upload-debet-simpanan',
            'upload-kredit-pinjaman',
            'upload-kredit-simpanan',
        ];

        $userAdmin = User::create([
            'name' => 'Admin Koperasi',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('Kopkar2019')
        ]);


        foreach ($adminPermission as $ap) {
            $permission = Permission::where('name', $ap)->first();
            $adminRole->attachPermission($permission->id);
        }

        $userAdmin->attachRole($adminRole);

        //Member Rules
        Role::create([
            'name' => 'member',
            'display_name' => 'Member',
            'description' => 'Role Member'
        ]);

        $memberRole = Role::where('name', 'member')->first();
        $memberPermission = [
            'create-debet-simpanan',
        ];

        $memberUser = User::create([
            'name' => 'Member Koperasi',
            'email' => 'member@gmail.com',
            'password' => bcrypt('Kopkar2019')
        ]);


        foreach ($memberPermission as $ap) {
            $permission = Permission::where('name', $ap)->first();
            $memberRole->attachPermission($permission->id);
        }

        $memberUser->attachRole($memberRole);
    }
}
