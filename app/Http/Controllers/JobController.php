<?php

namespace App\Http\Controllers;

use App\Services\EmployeeManagement\Applicant;
use Illuminate\Http\Request;

class JobController extends Controller
{    
    public function apply(Request $request)
    {
        $applicant = new Applicant();
        $data = $applicant->applyJob();
        
        return response()->json([
            'data' => $data
        ]);
    }
}
