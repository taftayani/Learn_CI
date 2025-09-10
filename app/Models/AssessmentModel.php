<?php

namespace App\Models;

use CodeIgniter\Model;

class AssessmentModel extends Model
{
    protected $table = 'assessments';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['user_id', 'room_id', 'asset_id', 'score', 'feasibility_score', 'is_feasible', 'notes'];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'user_id' => 'required|integer',
        'room_id' => 'required|integer',
        'asset_id' => 'required|integer',
        'score' => 'required|integer|greater_than[0]|less_than[11]',
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    protected $beforeInsert = ['calculateFeasibility'];
    protected $beforeUpdate = ['calculateFeasibility'];
    
    protected function calculateFeasibility(array $data)
    {
        if (!isset($data['data']['score']) || !isset($data['data']['asset_id'])) {
            return $data;
        }
        
        $assetModel = new AssetModel();
        $asset = $assetModel->find($data['data']['asset_id']);
        
        if ($asset) {
            $score = $data['data']['score'];
            $weight = $asset['weight'] ?? 1.0;
            $benefitScore = $asset['benefit_score'] ?? 0.0;
            
            $feasibilityScore = (($score * $weight) + $benefitScore) * 10;
            
            $data['data']['feasibility_score'] = $feasibilityScore;
            $data['data']['is_feasible'] = $feasibilityScore > 80 ? 1 : 0;
        }
        
        return $data;
    }
    
    public function getAssessmentWithDetails($userId = null)
    {
        $builder = $this->select('assessments.*, users.name as user_name, rooms.name as room_name, rooms.location, assets.name as asset_name')
                        ->join('users', 'users.id = assessments.user_id')
                        ->join('rooms', 'rooms.id = assessments.room_id')
                        ->join('assets', 'assets.id = assessments.asset_id');
        
        if ($userId) {
            $builder->where('assessments.user_id', $userId);
        }
        
        return $builder->findAll();
    }

    public function getAssessmentSummaryByRoom($assessmentId)
    {
        return $this->select('assessments.*, users.name as assessor_name, rooms.name as room_name, rooms.location, rooms.description as room_description')
                    ->join('users', 'users.id = assessments.user_id')
                    ->join('rooms', 'rooms.id = assessments.room_id')
                    ->find($assessmentId);
    }


}
