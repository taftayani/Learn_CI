<?php

namespace App\Controllers;

use App\Models\AssessmentModel;
use App\Models\RoomModel;
use App\Models\AssetModel;

class Reports extends BaseController
{
    protected $assessmentModel;
    protected $roomModel;
    protected $assetModel;
    
    public function __construct()
    {
        $this->assessmentModel = new AssessmentModel();
        $this->roomModel = new RoomModel();
        $this->assetModel = new AssetModel();
    }

    public function index()
    {
        $userRole = session()->get('user_role');
        if ($userRole !== 'Leader') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Only Leaders can view reports.');
        }

        $data = $this->getBaseViewData();
        $data = array_merge($data, $this->getDashboardData());
        return view('reports/dashboard', $data);
    }

    public function assetReports()
    {
        $userRole = session()->get('user_role');
        if ($userRole !== 'Leader') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $filters = [
            'room_id' => $this->request->getGet('room_id'),
            'asset_id' => $this->request->getGet('asset_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
        ];

        $data = $this->getBaseViewData();
        $data['assessments'] = $this->getFilteredAssessments($filters);
        $data['rooms'] = $this->roomModel->findAll();
        $data['assets'] = $this->assetModel->findAll();
        $data['filters'] = $filters;

        return view('reports/asset_reports', $data);
    }

    public function roomReports()
    {
        $userRole = session()->get('user_role');
        if ($userRole !== 'Leader') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $data = $this->getBaseViewData();
        $data['room_assessments'] = $this->getRoomAssessmentSummary();
        $data['rooms'] = $this->roomModel->findAll();

        return view('reports/room_reports', $data);
    }

    public function exportPdf()
    {
        // Placeholder for PDF export functionality
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'PDF export functionality will be implemented'
        ]);
    }

    public function exportExcel()
    {
        // Placeholder for Excel export functionality  
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Excel export functionality will be implemented'
        ]);
    }

    private function getDashboardData()
    {
        $totalAssessments = $this->assessmentModel->countAll();
        $feasibleAssessments = $this->assessmentModel->where('is_feasible', 1)->countAllResults();
        $nonFeasibleAssessments = $totalAssessments - $feasibleAssessments;
        
        $feasibilityPercentage = $totalAssessments > 0 ? 
            round(($feasibleAssessments / $totalAssessments) * 100, 2) : 0;

        $recentAssessments = $this->assessmentModel->getAssessmentWithDetails()
                                                  ->orderBy('created_at', 'DESC')
                                                  ->limit(10)
                                                  ->findAll();

        return [
            'total_assessments' => $totalAssessments,
            'feasible_assessments' => $feasibleAssessments,
            'non_feasible_assessments' => $nonFeasibleAssessments,
            'feasibility_percentage' => $feasibilityPercentage,
            'recent_assessments' => $recentAssessments,
            'total_rooms' => $this->roomModel->countAll(),
            'total_assets' => $this->assetModel->countAll()
        ];
    }

    private function getFilteredAssessments($filters)
    {
        $builder = $this->assessmentModel->select('assessments.*, users.name as user_name, rooms.name as room_name, assets.name as asset_name')
                                        ->join('users', 'users.id = assessments.user_id')
                                        ->join('rooms', 'rooms.id = assessments.room_id')
                                        ->join('assets', 'assets.id = assessments.asset_id');

        if (!empty($filters['room_id'])) {
            $builder->where('assessments.room_id', $filters['room_id']);
        }

        if (!empty($filters['asset_id'])) {
            $builder->where('assessments.asset_id', $filters['asset_id']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('DATE(assessments.created_at) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('DATE(assessments.created_at) <=', $filters['date_to']);
        }

        return $builder->orderBy('assessments.created_at', 'DESC')->findAll();
    }

    private function getRoomAssessmentSummary()
    {
        return $this->assessmentModel->select('
                rooms.name as room_name,
                rooms.id as room_id,
                COUNT(assessments.id) as total_assessments,
                COUNT(CASE WHEN assessments.is_feasible = 1 THEN 1 END) as feasible_count,
                AVG(assessments.feasibility_score) as avg_feasibility_score,
                MAX(assessments.created_at) as last_assessment
            ')
            ->join('rooms', 'rooms.id = assessments.room_id')
            ->groupBy('rooms.id, rooms.name')
            ->orderBy('rooms.name')
            ->findAll();
    }
}
