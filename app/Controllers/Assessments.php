<?php

namespace App\Controllers;

use App\Models\AssessmentModel;
use App\Models\RoomModel;
use App\Models\RoomAssetModel;
use App\Models\AssetModel;

class Assessments extends BaseController
{
    protected $assessmentModel;
    protected $roomModel;
    protected $roomAssetModel;
    protected $assetModel;

    public function __construct()
    {
        $this->assessmentModel = new AssessmentModel();
        $this->roomModel = new RoomModel();
        $this->roomAssetModel = new RoomAssetModel();
        $this->assetModel = new AssetModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('user_role');
        
        if ($userRole === 'GA Staff') {
            return $this->staffAssessments();
        } elseif (in_array($userRole, ['Super Admin', 'Admin', 'Leader'])) {
            return $this->viewAllAssessments();
        } else {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
    }

    protected function staffAssessments()
    {
        $data = $this->getBaseViewData();
        $data['title'] = 'My Assessments';

        $userId = session()->get('user_id');
        $data['assessments'] = $this->assessmentModel->getAssessmentWithDetails($userId);

        return view('assessments/index', $data);
    }

    protected function viewAllAssessments()
    {
        $data = $this->getBaseViewData();
        $data['title'] = 'All Assessments';

        $data['assessments'] = $this->assessmentModel->getAssessmentWithDetails();

        return view('assessments/admin_index', $data);
    }
    
    public function adminIndex()
    {
        $userRole = session()->get('user_role');
        if (!in_array($userRole, ['Super Admin', 'Admin', 'Leader'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        return $this->viewAllAssessments();
    }

    public function rooms()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        if (session()->get('user_role') !== 'GA Staff') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $data = $this->getBaseViewData();
        $data['title'] = 'Select Room for Assessment';

        $data['rooms'] = $this->roomModel->findAll();

        return view('assessments/rooms', $data);
    }

    public function assessRoom($roomId = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        if (session()->get('user_role') !== 'GA Staff') {
            return redirect()->to('/dashboard')->with('error', 'Only GA Staff can perform assessments');
        }

        if (!$roomId) {
            return redirect()->to('/assessments/rooms')->with('error', 'Room not specified');
        }

        $room = $this->roomModel->find($roomId);
        if (!$room) {
            return redirect()->to('/assessments/rooms')->with('error', 'Room not found');
        }

        $assets = $this->roomAssetModel->getAssetsByRoom($roomId);
        if (empty($assets)) {
            return redirect()->to('/assessments/rooms')->with('error', 'No assets found for this room');
        }

        $data = $this->getBaseViewData();
        $data['title'] = 'Assess Room: ' . $room['name'];
        $data['room'] = $room;
        $data['assets'] = $assets;

        return view('assessments/assess_room', $data);
    }

    public function saveAssessment()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        if (session()->get('user_role') !== 'GA Staff') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'room_id' => 'required|integer',
            'assessments' => 'required',
            'overall_notes' => 'permit_empty|max_length[500]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $roomId = $this->request->getPost('room_id');
        $assessments = $this->request->getPost('assessments');
        $overallNotes = $this->request->getPost('overall_notes') ?? '';
        $notes = $this->request->getPost('notes') ?? [];
        $userId = session()->get('user_id');

        // Validate room exists
        $room = $this->roomModel->find($roomId);
        if (!$room) {
            return redirect()->back()->with('error', 'Room not found');
        }

        $this->assessmentModel->db->transStart();

        $savedCount = 0;
        $errors = [];

        foreach ($assessments as $assetId => $score) {
            // Validate score range
            if (!is_numeric($score) || $score < 1 || $score > 10) {
                $errors[] = "Invalid score for asset ID {$assetId}. Score must be between 1-10.";
                continue;
            }

            // Check if asset exists
            $asset = $this->assetModel->find($assetId);
            if (!$asset) {
                $errors[] = "Asset with ID {$assetId} not found.";
                continue;
            }

            // Check if assessment already exists for this user, room, and asset
            $existing = $this->assessmentModel->where([
                'user_id' => $userId,
                'room_id' => $roomId,
                'asset_id' => $assetId
            ])->first();

            $assessmentData = [
                'user_id' => $userId,
                'room_id' => $roomId,
                'asset_id' => $assetId,
                'score' => (int)$score,
                'notes' => isset($notes[$assetId]) ? $notes[$assetId] : $overallNotes
            ];

            if ($existing) {
                // Update existing assessment
                if ($this->assessmentModel->update($existing['id'], $assessmentData)) {
                    $savedCount++;
                } else {
                    $errors[] = "Failed to update assessment for asset: {$asset['name']}";
                }
            } else {
                // Create new assessment
                if ($this->assessmentModel->insert($assessmentData)) {
                    $savedCount++;
                } else {
                    $errors[] = "Failed to save assessment for asset: {$asset['name']}";
                }
            }
        }

        if ($this->assessmentModel->db->transStatus() === false || !empty($errors)) {
            $this->assessmentModel->db->transRollback();
            return redirect()->back()->with('error', 'Failed to save some assessments: ' . implode(', ', $errors));
        } else {
            $this->assessmentModel->db->transComplete();
            return redirect()->to('/assessments/history')->with('success', "Successfully saved {$savedCount} assessments for {$room['name']}");
        }
    }

    public function history()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        if (session()->get('user_role') !== 'GA Staff') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $data = $this->getBaseViewData();
        $data['title'] = 'My Assessment History';

        $userId = session()->get('user_id');
        $data['assessments'] = $this->assessmentModel->getAssessmentWithDetails($userId);

        // Group assessments by room and date for better display
        $groupedAssessments = [];
        foreach ($data['assessments'] as $assessment) {
            $date = date('Y-m-d', strtotime($assessment['created_at']));
            $roomName = $assessment['room_name'];
            $key = $roomName . '_' . $date;
            
            if (!isset($groupedAssessments[$key])) {
                $groupedAssessments[$key] = [
                    'room_name' => $roomName,
                    'date' => $date,
                    'created_at' => $assessment['created_at'],
                    'assessments' => []
                ];
            }
            
            $groupedAssessments[$key]['assessments'][] = $assessment;
        }

        // Sort by date (newest first)
        usort($groupedAssessments, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        $data['grouped_assessments'] = $groupedAssessments;

        return view('assessments/history', $data);
    }

    public function details($assessmentId = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        if (!$assessmentId) {
            return redirect()->to('/assessments')->with('error', 'Assessment not specified');
        }

        $assessment = $this->assessmentModel->getAssessmentSummaryByRoom($assessmentId);
        
        if (!$assessment) {
            return redirect()->to('/assessments')->with('error', 'Assessment not found');
        }

        $userRole = session()->get('user_role');
        $userId = session()->get('user_id');

        // GA Staff can only view their own assessments
        if ($userRole === 'GA Staff' && $assessment['user_id'] != $userId) {
            return redirect()->to('/assessments')->with('error', 'Access denied');
        }

        $data = $this->getBaseViewData();
        $data['title'] = 'Assessment Details';
        $data['assessment'] = $assessment;
        $data['asset_scores'] = [];
        $data['score_distribution'] = ['poor' => 0, 'average' => 0, 'good' => 0, 'excellent' => 0];
        $data['assessment_history'] = [];

        return view('assessments/details', $data);
    }
}
