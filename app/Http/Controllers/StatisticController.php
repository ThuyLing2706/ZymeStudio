<?php


namespace App\Http\Controllers;


// use App\Console\Commands\Salary;
use App\Exports\SalaryExport;
use App\Models\File;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class StatisticController extends Controller
{
    //
    public function index(Request $request)
    {
        $param = $request->all();
        // Query get the user and salary of this month
        $timeNow = Carbon::now();
        $salaries = Salary::with(['user', 'user.files' => function ($fileQuery) {
            return $fileQuery->where('status', File::STATUS_DONE);
        }])->where(function ($query) use ($param, $timeNow) {
            if (!isset($param['date'])) {
                return $query->whereMonth('month', $timeNow);
            }
            $selectedMonth = Carbon::create($param['date']);
            return $query->whereMonth('month', $selectedMonth);
        })
            ->orderBy('id', 'DESC')
            ->get();
        foreach ($salaries as $salary) {
            $salary->txt_status = Salary::CONVERT_PAID_STATUS[$salary->status];
        }
        return view('admin.statistic.index', compact('salaries'));
    }


    public function paid(Request $request, $id) {
        $salary = Salary::find($id);
        $salary->status = Salary::STATUS_PAID;
        $salary->update();
        return redirect()->back();
    }


    public function export(Request $request)
    {
       
        $timeNow = Carbon::now();
        return Excel::download(new SalaryExport($timeNow), '給料.xlsx');
    }
}
