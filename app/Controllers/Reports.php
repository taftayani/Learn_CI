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
        $data['asset_reports'] = $this->getFilteredAssessments($filters); // Same data but different name for view
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
        $userRole = session()->get('user_role');
        if ($userRole !== 'Leader') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        // Get dashboard data
        $data = $this->getDashboardData();
        
        // Generate HTML content for PDF-like output
        $filename = 'dashboard_report_' . date('Y-m-d_H-i-s') . '.html';
        
        // Set headers for file download
        $this->response->setHeader('Content-Type', 'text/html');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        // Create HTML content
        $html = '<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .stats { display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 30px; }
        .stat-card { border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Dashboard Report</h1>
        <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>
    </div>
    
    <div class="stats">
        <div class="stat-card">
            <h3>Total Assessments</h3>
            <p>' . ($data['total_assessments'] ?? 0) . '</p>
        </div>
        <div class="stat-card">
            <h3>Feasible Assessments</h3>
            <p>' . ($data['feasible_assessments'] ?? 0) . '</p>
        </div>
        <div class="stat-card">
            <h3>Total Rooms</h3>
            <p>' . ($data['total_rooms'] ?? 0) . '</p>
        </div>
        <div class="stat-card">
            <h3>Average Feasibility</h3>
            <p>' . ($data['avg_feasibility'] ?? 0) . '%</p>
        </div>
    </div>';

        // Add recent assessments table if available
        if (!empty($data['recent_assessments'])) {
            $html .= '<h2>Recent Assessments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Asset</th>
                        <th>Date</th>
                        <th>Feasibility Score</th>
                        <th>Assessor</th>
                    </tr>
                </thead>
                <tbody>';
            
            foreach ($data['recent_assessments'] as $assessment) {
                $html .= '<tr>
                    <td>' . htmlspecialchars($assessment['room_name'] ?? 'N/A') . '</td>
                    <td>' . htmlspecialchars($assessment['asset_name'] ?? 'N/A') . '</td>
                    <td>' . date('Y-m-d', strtotime($assessment['created_at'] ?? 'now')) . '</td>
                    <td>' . ($assessment['feasibility_score'] ?? 0) . '%</td>
                    <td>' . htmlspecialchars($assessment['user_name'] ?? 'N/A') . '</td>
                </tr>';
            }
            
            $html .= '</tbody></table>';
        }
        
        $html .= '</body></html>';
        
        return $this->response->setBody($html);
    }

    public function exportExcel()
    {
        $userRole = session()->get('user_role');
        if ($userRole !== 'Leader') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        // Get dashboard data
        $data = $this->getDashboardData();
        
        // Generate CSV content (Excel will open CSV files)
        $filename = 'dashboard_report_' . date('Y-m-d_H-i-s') . '.csv';
        
        // Set headers for file download
        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        // Create CSV content
        $output = "Dashboard Report - " . date('Y-m-d H:i:s') . "\n\n";
        $output .= "Summary Statistics:\n";
        $output .= "Total Assessments," . ($data['total_assessments'] ?? 0) . "\n";
        $output .= "Feasible Assessments," . ($data['feasible_assessments'] ?? 0) . "\n";
        $output .= "Non-Feasible Assessments," . ($data['non_feasible_assessments'] ?? 0) . "\n";
        $output .= "Total Rooms," . ($data['total_rooms'] ?? 0) . "\n";
        $output .= "Total Assets," . ($data['total_assets'] ?? 0) . "\n";
        $output .= "Average Feasibility," . ($data['avg_feasibility'] ?? 0) . "%\n\n";
        
        // Add recent assessments if available
        if (!empty($data['recent_assessments'])) {
            $output .= "Recent Assessments:\n";
            $output .= "Room,Asset,Date,Feasibility Score,User\n";
            foreach ($data['recent_assessments'] as $assessment) {
                $output .= '"' . ($assessment['room_name'] ?? 'N/A') . '",';
                $output .= '"' . ($assessment['asset_name'] ?? 'N/A') . '",';
                $output .= '"' . date('Y-m-d', strtotime($assessment['created_at'] ?? 'now')) . '",';
                $output .= '"' . ($assessment['feasibility_score'] ?? 0) . '%",';
                $output .= '"' . ($assessment['user_name'] ?? 'N/A') . '"' . "\n";
            }
        }
        
        return $this->response->setBody($output);
    }
    
    public function exportAssetsPdf()
    {
        $userRole = session()->get('user_role');
        if ($userRole !== 'Leader') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        // Get filters and data
        $filters = [
            'room_id' => $this->request->getGet('room_id'),
            'asset_id' => $this->request->getGet('asset_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
        ];
        $assessments = $this->getFilteredAssessments($filters);
        
        // Generate HTML content
        $filename = 'asset_reports_' . date('Y-m-d_H-i-s') . '.html';
        
        $this->response->setHeader('Content-Type', 'text/html');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        $html = '<!DOCTYPE html><html><head><title>Asset Reports</title>
        <style>body{font-family:Arial,sans-serif;margin:20px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ddd;padding:8px;}th{background-color:#f2f2f2;}</style>
        </head><body><h1>Asset Reports</h1><p>Generated: ' . date('Y-m-d H:i:s') . '</p>';
        
        if (!empty($assessments)) {
            $html .= '<table><tr><th>Room</th><th>Asset</th><th>Date</th><th>Feasibility</th><th>Assessor</th></tr>';
            foreach ($assessments as $assessment) {
                $html .= '<tr><td>' . htmlspecialchars($assessment['room_name'] ?? 'N/A') . '</td>';
                $html .= '<td>' . htmlspecialchars($assessment['asset_name'] ?? 'N/A') . '</td>';
                $html .= '<td>' . date('Y-m-d', strtotime($assessment['created_at'] ?? 'now')) . '</td>';
                $html .= '<td>' . ($assessment['feasibility_score'] ?? 0) . '%</td>';
                $html .= '<td>' . htmlspecialchars($assessment['user_name'] ?? 'N/A') . '</td></tr>';
            }
            $html .= '</table>';
        } else {
            $html .= '<p>No assessments found for the selected criteria.</p>';
        }
        
        $html .= '</body></html>';
        return $this->response->setBody($html);
    }

    public function exportAssetsExcel()
    {
        $userRole = session()->get('user_role');
        if ($userRole !== 'Leader') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        // Get filters from request
        $filters = [
            'room_id' => $this->request->getGet('room_id'),
            'asset_id' => $this->request->getGet('asset_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
        ];

        // Get filtered assessments
        $assessments = $this->getFilteredAssessments($filters);
        
        // Generate CSV content
        $filename = 'asset_reports_' . date('Y-m-d_H-i-s') . '.csv';
        
        // Set headers for file download
        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        // Create CSV content
        $output = "Asset Reports Export - " . date('Y-m-d H:i:s') . "\n\n";
        
        // Add filter info if filters are applied
        if (array_filter($filters)) {
            $output .= "Applied Filters:\n";
            if ($filters['room_id']) $output .= "Room ID: " . $filters['room_id'] . "\n";
            if ($filters['asset_id']) $output .= "Asset ID: " . $filters['asset_id'] . "\n";
            if ($filters['date_from']) $output .= "Date From: " . $filters['date_from'] . "\n";
            if ($filters['date_to']) $output .= "Date To: " . $filters['date_to'] . "\n";
            $output .= "\n";
        }
        
        // Add assessment data
        $output .= "Assessment Details:\n";
        $output .= "Room,Asset,Assessor,Assessment Date,Feasibility Score,Physical Condition,Functionality,Safety Compliance,Is Feasible\n";
        
        foreach ($assessments as $assessment) {
            $output .= '"' . ($assessment['room_name'] ?? 'N/A') . '",';
            $output .= '"' . ($assessment['asset_name'] ?? 'N/A') . '",';
            $output .= '"' . ($assessment['user_name'] ?? 'N/A') . '",';
            $output .= '"' . date('Y-m-d', strtotime($assessment['created_at'] ?? 'now')) . '",';
            $output .= '"' . ($assessment['feasibility_score'] ?? 0) . '%",';
            $output .= '"' . ($assessment['physical_condition_score'] ?? 0) . '%",';
            $output .= '"' . ($assessment['functionality_score'] ?? 0) . '%",';
            $output .= '"' . ($assessment['safety_compliance_score'] ?? 0) . '%",';
            $output .= '"' . (($assessment['is_feasible'] ?? 0) ? 'Yes' : 'No') . '"' . "\n";
        }
        
        return $this->response->setBody($output);
    }
    
    public function exportRoomsPdf()
    {
        $userRole = session()->get('user_role');
        if ($userRole !== 'Leader') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        // Get room assessment data
        $roomAssessments = $this->getRoomAssessmentSummary();
        
        // Generate HTML content
        $filename = 'room_reports_' . date('Y-m-d_H-i-s') . '.html';
        
        $this->response->setHeader('Content-Type', 'text/html');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        $html = '<!DOCTYPE html><html><head><title>Room Reports</title>
        <style>body{font-family:Arial,sans-serif;margin:20px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ddd;padding:8px;}th{background-color:#f2f2f2;}</style>
        </head><body><h1>Room Reports</h1><p>Generated: ' . date('Y-m-d H:i:s') . '</p>';
        
        if (!empty($roomAssessments)) {
            $html .= '<table><tr><th>Room</th><th>Total Assessments</th><th>Feasible</th><th>Avg Score</th><th>Last Assessment</th></tr>';
            foreach ($roomAssessments as $room) {
                $html .= '<tr><td>' . htmlspecialchars($room['room_name'] ?? 'N/A') . '</td>';
                $html .= '<td>' . ($room['total_assessments'] ?? 0) . '</td>';
                $html .= '<td>' . ($room['feasible_count'] ?? 0) . '</td>';
                $html .= '<td>' . number_format($room['avg_feasibility_score'] ?? 0, 1) . '%</td>';
                $html .= '<td>' . ($room['last_assessment'] ? date('Y-m-d', strtotime($room['last_assessment'])) : 'Never') . '</td></tr>';
            }
            $html .= '</table>';
        } else {
            $html .= '<p>No room assessment data available.</p>';
        }
        
        $html .= '</body></html>';
        return $this->response->setBody($html);
    }

    public function exportRoomsExcel()
    {
        $userRole = session()->get('user_role');
        if ($userRole !== 'Leader') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        // Get room assessment summary data
        $roomAssessments = $this->getRoomAssessmentSummary();
        
        // Generate CSV content
        $filename = 'room_reports_' . date('Y-m-d_H-i-s') . '.csv';
        
        // Set headers for file download
        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        // Create CSV content
        $output = "Room Reports Export - " . date('Y-m-d H:i:s') . "\n\n";
        $output .= "Room Assessment Summary:\n";
        $output .= "Room Name,Total Assessments,Feasible Assessments,Average Feasibility Score,Last Assessment Date\n";
        
        foreach ($roomAssessments as $room) {
            $output .= '"' . ($room['room_name'] ?? 'N/A') . '",';
            $output .= '"' . ($room['total_assessments'] ?? 0) . '",';
            $output .= '"' . ($room['feasible_count'] ?? 0) . '",';
            $output .= '"' . number_format($room['avg_feasibility_score'] ?? 0, 2) . '%",';
            $output .= '"' . ($room['last_assessment'] ? date('Y-m-d', strtotime($room['last_assessment'])) : 'Never') . '"' . "\n";
        }
        
        return $this->response->setBody($output);
    }

    private function getDashboardData()
    {
        $totalAssessments = $this->assessmentModel->countAll();
        $feasibleAssessments = $this->assessmentModel->where('is_feasible', 1)->countAllResults();
        $nonFeasibleAssessments = $totalAssessments - $feasibleAssessments;
        
        $feasibilityPercentage = $totalAssessments > 0 ? 
            round(($feasibleAssessments / $totalAssessments) * 100, 2) : 0;

        // Get recent assessments properly
        $recentAssessments = $this->assessmentModel->select('assessments.*, users.name as user_name, rooms.name as room_name, assets.name as asset_name')
                                                   ->join('users', 'users.id = assessments.user_id')
                                                   ->join('rooms', 'rooms.id = assessments.room_id')
                                                   ->join('assets', 'assets.id = assessments.asset_id')
                                                   ->orderBy('assessments.created_at', 'DESC')
                                                   ->limit(10)
                                                   ->findAll();

        // Calculate feasibility counts for different ranges
        $highFeasibilityCount = $this->assessmentModel->where('feasibility_score >=', 80)->countAllResults();
        $mediumFeasibilityCount = $this->assessmentModel->where('feasibility_score >=', 60)->where('feasibility_score <', 80)->countAllResults();
        $lowFeasibilityCount = $this->assessmentModel->where('feasibility_score <', 60)->countAllResults();

        // Calculate average feasibility
        $avgFeasibilityResult = $this->assessmentModel->selectAvg('feasibility_score', 'avg_feasibility')->first();
        $avgFeasibility = $avgFeasibilityResult['avg_feasibility'] ?? 0;

        // Generate sample trend data (you can replace this with actual data)
        $trendLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        $trendData = [10, 15, 12, 18, 20, 25];

        return [
            'total_assessments' => $totalAssessments,
            'feasible_assessments' => $feasibleAssessments,
            'non_feasible_assessments' => $nonFeasibleAssessments,
            'feasibility_percentage' => $feasibilityPercentage,
            'recent_assessments' => $recentAssessments,
            'total_rooms' => $this->roomModel->countAll(),
            'total_assets' => $this->assetModel->countAll(),
            'high_feasibility_count' => $highFeasibilityCount,
            'medium_feasibility_count' => $mediumFeasibilityCount,
            'low_feasibility_count' => $lowFeasibilityCount,
            'avg_feasibility' => round($avgFeasibility, 1),
            'trend_labels' => $trendLabels,
            'trend_data' => $trendData
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
