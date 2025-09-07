<?php

namespace App\Models;

use CodeIgniter\Model;

class RoomModel extends Model
{
    protected $table = 'rooms';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['name', 'description', 'location'];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    public function getRoomWithAssets($roomId)
    {
        return $this->select('rooms.*, GROUP_CONCAT(assets.name) as asset_names')
                   ->join('room_assets', 'room_assets.room_id = rooms.id', 'left')
                   ->join('assets', 'assets.id = room_assets.asset_id', 'left')
                   ->where('rooms.id', $roomId)
                   ->groupBy('rooms.id')
                   ->first();
    }
}
