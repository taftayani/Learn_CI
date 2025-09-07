<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoomModel;
use App\Models\AssetModel;
use App\Models\AssessmentModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $userRole = session()->get('user_role');
        $data = $this->getBaseViewData();
        
        if (in_array($userRole, ['Super Admin', 'Admin'])) {
            $userModel = new UserModel();
            $roomModel = new RoomModel();
            $assetModel = new AssetModel();
            
            $data['total_users'] = $userModel->countAll();
            $data['total_rooms'] = $roomModel->countAll();
            $data['total_assets'] = $assetModel->countAll();
        } elseif ($userRole === 'GA Staff') {
            $assessmentModel = new AssessmentModel();
            $data['my_assessments'] = $assessmentModel->where('user_id', session()->get('user_id'))->countAllResults();
        } elseif ($userRole === 'Leader') {
            $assessmentModel = new AssessmentModel();
            $data['total_assessments'] = $assessmentModel->countAll();
            $data['feasible_assessments'] = $assessmentModel->where('is_feasible', 1)->countAllResults();
        }
        
        return view('dashboard/index', $data);
    }
}
