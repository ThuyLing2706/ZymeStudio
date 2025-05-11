<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    const STATUS_ASSIGN = 1;

    const STATUS_CONFIRM = 2;

    const STATUS_DONE = 3;

    const PRIORITY_LOW = 1;

    const PRIORITY_MEDIUM = 2;

    const PRIORITY_HIGH = 3;

    const SYNC = 1;

    const UN_SYNC = 0;

    const CONVERT_STATUS_TXT = [
        1 => '処理',
        2 => '確認する',
        3 => '終わり'
    ];

    const CONVERT_PRIORITY_TXT = [
        1 => 'LOW',
        2 => 'MEDIUM',
        3 => 'HIGH'
    ];

    const CONVERT_SYNC_TXT = [
        0 => 'Not synchronize',
        1 => 'Synchronize'
    ];

    // Lien ket toi bang co so du lieu
    protected $table = 'files';

    // Anh xa cac truong trong bang co so du lieu
    protected $filltable = [
        "filename",
        "deadline",
        "status", // 1:assign, 2:confirm, 3:done // định nghĩa hàm số đối tượng
        "priority", // 1: low, 2: medium, 3:high
        "user_id",
        "synchronize", // 1: synchronized, 0: not synchronized
        "created_at",
        "updated_at"
    ];

    /**
     * Lien ket 1-1
     * 
     * Su dung belongTo hoac hasOne
     */
    public function user()
    {
        return $this->belongTo('App\Models\User', 'user_id', 'id');
    }
}
