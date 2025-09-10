<?php

namespace App\Models;

use CodeIgniter\Model;

class AssetModel extends Model
{
    protected $table = 'assets';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['name', 'description', 'category', 'weight', 'benefit_score'];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
        'weight' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[10]',
        'benefit_score' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[10]',
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}
