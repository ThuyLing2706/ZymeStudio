<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\File;
use App\Models\User;
use App\Models\Salary;
use Illuminate\Support\Facades\DB;

class DemoController extends Controller
{
    public function index(Request $requets)
    {
        // Query buider: thao tác trực tiếp tới cơ sở dữ liệu bằng lệnh sql
        // viết bằng laravel, dịch sang sql, áp dụng vào csdl
        $data = DB::table('users')
            ->select(
                "id", "email", "name", "created_at", // sử dụng select để lấy ra các trường muốn hiển thị
                "role"
            )->where('role', User::ROLE_ADMIN) // không viết số 1 role_admin
            ->get();
        if (!empty($data)) {
            foreach($data as $user) {
                $user->role = "admin"; // muốn thay đổi thuộc tính thì trỏ đến thuộc tính của đối tượng đó và gán giá trị cho nó
            }
        }
        return $data;
    }

    public function listJob (Request $requets, $editorId)
    {
        $files = DB::table ('files')
        ->select(
            "users.name", "files.filename", "files.deadline",
            "files.status", "files.priority", "files.synchronize",
            "files.id"
        ) 
        ->join('users', 'users.id', 'files.user_id')
        ->where('users.id', $editorId)
        ->where('users.role', User::ROLE_EDITOR)
        ->orderBy('files.id', 'DESC')
        ->paginate(config('const.paginate')) //paginate + số lượng: để lấy ra số bản ghi mong muốn=>phân trang trong laravel
        ->map(function($item){ // mỗi bản ghi tương ứng với 1 item 
            $item->status = File::CONVERT_STATUS_TXT[$item->status];
            $item->priority = File::CONVERT_PRIORITY_TXT[$item->priority];
            $item->synchronize = File::CONVERT_SYNC_TXT[$item->synchronize];
            return $item;
        }); 
        return $files;
    }

    public function sortUserFile(Request $requets)
    {
        $param = $requets->all();
        $users = DB::table('users')
            ->select(
                'users.id', 'users.name', 'users.email',
                DB::raw("COUNT(files.id) as total_file")
                )->join('files', 'users.id', 'files.user_id')
            ->where('users.role', User::ROLE_EDITOR)
            ->groupBy('users.id')
            ->groupBy('users.name')
            ->groupBy('users.email')
            ->orderBy('total_file', isset($param['sort']) ? $param['sort'] : 'DESC') // nếu 2 user có cùng total file thì sẽ xét theo id
            ->get();
        return $users;
    }

    public function eloquent(Request $requets)
    {
        $param = $requets->all();
        /**
         * Eloquent se render ra lenh sql thong qua model
         * 
         * De su dung relationship se goi thong qua with
         * Tim kiem theo dieu kien o trong relationship su dung whereHas hoac orWhereHas
         */
        $users = User::with('files')
            ->where(function ($query) use ($param) {
                if (isset($param['editor_name']) && isset($param['file_name'])) {
                    $query->where('name', 'like', '%' . $param['editor_name'] . '%');
                }
                return $query;
            })->whereHas('files', function ($queryFile) use ($param) {
                if (isset($param['file_name']) && isset($param['editor_name'])) {
                    $queryFile->where('filename', 'like', '%' . $param['file_name'] . '%');
                }
                return $queryFile;
            })

            ->where('role', User::ROLE_EDITOR)
            ->get()->map(
                function ($item) {
                    foreach ($item->files as $file) {
                        $file->status = File::CONVERT_STATUS_TXT[$file->status];
                        $file->priority = File::CONVERT_PRIORITY_TXT[$file->priority];
                        $file->synchronize = File::CONVERT_SYNC_TXT[$file->synchronize];
                        }
                        return $item;
                    }
                );

            //truyen du lieu ve view su dung compact
            return view('demo_eloquent', compact('users'));
        }
    }
