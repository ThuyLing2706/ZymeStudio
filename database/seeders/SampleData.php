<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SampleData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //DB::table('ten bang)-><cac lenh query>(); // de ket noi toi bang 
        /**
         * Query buider: thao tac truc tiep toi bang csdl
         * Uu diem: toc do nhanh vi thao tac truc tiep toi csdl
         * Nhuoc diem: xu ly logic ko manh
         */
        // Tao 100 editor va 1 admin
        for ($i = 0; $i < 100; $i++) {
            DB::table('users')->insert([
                'name' => 'editor-' . $i,
                'email' => 'editorEmail'.$i.'@gmail.com',
                /**
                 * Ma hoa mat khau duoi dang SHA
                 * Khoa bi mat la gia tri cua APP_KEY trong env
                 */
                'password' => Hash::make('12345678'),
                'role' => 0
            ]);
        }
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 1
        ]);
        // Tao 1000 file tương ứng với 100 editor

    }
}
