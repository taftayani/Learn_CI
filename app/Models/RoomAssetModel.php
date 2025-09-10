<?php

namespace App\Models;

use CodeIgniter\Model;

class RoomAssetModel extends Model
{
    protected $table = 'room_assets';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['room_id', 'asset_id'];
    
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    
    protected $validationRules = [
        'room_id' => 'required|integer',
        'asset_id' => 'required|integer',
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    public function getAssetsByRoom($roomId)
    {
        return $this->select('
                assets.id as asset_id,
                assets.name as asset_name,
                assets.category as asset_category,
                assets.description as asset_description,
                assets.weight,
                assets.benefit_score,
                room_assets.id as relation_id
            ')
                   ->join('assets', 'assets.id = room_assets.asset_id')
                   ->where('room_assets.room_id', $roomId)
                   ->findAll();
    }
}
