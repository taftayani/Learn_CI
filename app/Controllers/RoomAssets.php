<?php

namespace App\Controllers;

use App\Models\RoomModel;
use App\Models\AssetModel;
use App\Models\RoomAssetModel;

class RoomAssets extends BaseController
{
    protected $roomModel;
    protected $assetModel;
    protected $roomAssetModel;
    
    public function __construct()
    {
        $this->roomModel = new RoomModel();
        $this->assetModel = new AssetModel();
        $this->roomAssetModel = new RoomAssetModel();
    }

    public function index()
    {
        $data = $this->getBaseViewData();
        $rooms = $this->roomModel->findAll();
        
        // Add assets to each room
        foreach ($rooms as &$room) {
            $room['assets'] = $this->roomAssetModel->getAssetsByRoom($room['id']);
        }
        
        $data['rooms'] = $rooms;
        $data['total_rooms'] = count($rooms);
        $data['rooms_with_assets'] = count(array_filter($rooms, function($room) { return count($room['assets']) > 0; }));
        $data['empty_rooms'] = count(array_filter($rooms, function($room) { return count($room['assets']) == 0; }));
        $data['total_relations'] = array_sum(array_map(function($room) { return count($room['assets']); }, $rooms));
        
        return view('room_assets/index', $data);
    }

    public function showRoom($roomId)
    {
        $room = $this->roomModel->find($roomId);
        if (!$room) {
            return redirect()->to('/room-assets')->with('error', 'Room not found');
        }

        $data = $this->getBaseViewData();
        $room['assets'] = $this->roomAssetModel->getAssetsByRoom($roomId);
        $data['room'] = $room;
        $data['assets'] = $room['assets'];
        $data['available_assets'] = $this->getAvailableAssets($roomId);
        
        return view('room_assets/show_room', $data);
    }

    public function create($roomId = null)
    {
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'room_id' => $this->request->getPost('room_id'),
                'asset_id' => $this->request->getPost('asset_id'),
            ];

            // Check if relationship already exists
            $existing = $this->roomAssetModel->where([
                'room_id' => $data['room_id'],
                'asset_id' => $data['asset_id']
            ])->first();

            if ($existing) {
                return redirect()->back()->with('error', 'Asset is already assigned to this room');
            }

            if ($this->roomAssetModel->insert($data)) {
                return redirect()->to('/room-assets/show/' . $data['room_id'])->with('success', 'Asset assigned to room successfully');
            }
            
            return redirect()->back()->withInput()->with('errors', $this->roomAssetModel->errors());
        }

        $data = $this->getBaseViewData();
        $data['rooms'] = $this->roomModel->findAll();
        $data['assets'] = $this->assetModel->findAll();
        $data['selectedRoomId'] = $roomId;
        
        if ($roomId) {
            $data['availableAssets'] = $this->getAvailableAssets($roomId);
        }

        return view('room_assets/create', $data);
    }

    public function addAsset($roomId)
    {
        $room = $this->roomModel->find($roomId);
        if (!$room) {
            return redirect()->to('/room-assets')->with('error', 'Room not found');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'room_id' => $roomId,
                'asset_id' => $this->request->getPost('asset_id'),
            ];

            // Check if relationship already exists
            $existing = $this->roomAssetModel->where($data)->first();
            if ($existing) {
                return redirect()->back()->with('error', 'Asset is already assigned to this room');
            }

            if ($this->roomAssetModel->insert($data)) {
                return redirect()->to('/room-assets/show/' . $roomId)->with('success', 'Asset added to room successfully');
            }
            
            return redirect()->back()->withInput()->with('errors', $this->roomAssetModel->errors());
        }

        $data = $this->getBaseViewData();
        $data['room'] = $room;
        $data['availableAssets'] = $this->getAvailableAssets($roomId);

        return view('room_assets/add_asset', $data);
    }

    public function addAssets($roomId)
    {
        $room = $this->roomModel->find($roomId);
        if (!$room) {
            return redirect()->to('/room-assets')->with('error', 'Room not found');
        }

        if ($this->request->getMethod() === 'POST') {
            $assetIds = $this->request->getPost('asset_ids');
            
            if (empty($assetIds)) {
                return redirect()->back()->with('error', 'Please select at least one asset');
            }

            $successCount = 0;
            $errors = [];

            foreach ($assetIds as $assetId) {
                // Check if relationship already exists
                $existing = $this->roomAssetModel->where([
                    'room_id' => $roomId,
                    'asset_id' => $assetId
                ])->first();

                if (!$existing) {
                    $data = [
                        'room_id' => $roomId,
                        'asset_id' => $assetId,
                    ];

                    if ($this->roomAssetModel->insert($data)) {
                        $successCount++;
                    } else {
                        $asset = $this->assetModel->find($assetId);
                        $errors[] = 'Failed to add ' . ($asset['name'] ?? 'Asset #' . $assetId);
                    }
                }
            }

            if ($successCount > 0) {
                $message = $successCount . ' asset(s) added to room successfully';
                if (!empty($errors)) {
                    $message .= '. Some assets could not be added: ' . implode(', ', $errors);
                }
                return redirect()->to('/room-assets/show/' . $roomId)->with('success', $message);
            } else {
                return redirect()->back()->with('error', 'No assets were added. ' . implode(', ', $errors));
            }
        }

        return redirect()->to('/room-assets/show/' . $roomId);
    }

    public function removeAsset($roomId, $assetId)
    {
        $roomAsset = $this->roomAssetModel->where([
            'room_id' => $roomId,
            'asset_id' => $assetId
        ])->first();

        if (!$roomAsset) {
            return redirect()->to('/room-assets/show/' . $roomId)->with('error', 'Asset not found in this room');
        }

        if ($this->roomAssetModel->delete($roomAsset['id'])) {
            return redirect()->to('/room-assets/show/' . $roomId)->with('success', 'Asset removed from room successfully');
        }
        
        return redirect()->to('/room-assets/show/' . $roomId)->with('error', 'Error removing asset from room');
    }

    public function edit($id)
    {
        $roomAsset = $this->roomAssetModel->find($id);
        if (!$roomAsset) {
            return redirect()->to('/room-assets')->with('error', 'Room-Asset relationship not found');
        }

        if ($this->request->getMethod() === 'PUT') {
            $data = [
                'room_id' => $this->request->getVar('room_id'),
                'asset_id' => $this->request->getVar('asset_id'),
            ];

            // Check if new relationship already exists (excluding current record)
            $existing = $this->roomAssetModel->where($data)->where('id !=', $id)->first();
            if ($existing) {
                return redirect()->back()->with('error', 'This asset is already assigned to the selected room');
            }

            if ($this->roomAssetModel->update($id, $data)) {
                return redirect()->to('/room-assets')->with('success', 'Room-Asset relationship updated successfully');
            }
            
            return redirect()->back()->withInput()->with('errors', $this->roomAssetModel->errors());
        }

        $data = $this->getBaseViewData();
        $data['roomAsset'] = $roomAsset;
        $data['rooms'] = $this->roomModel->findAll();
        $data['assets'] = $this->assetModel->findAll();

        return view('room_assets/edit', $data);
    }

    public function delete($id)
    {
        if ($this->roomAssetModel->delete($id)) {
            return redirect()->to('/room-assets')->with('success', 'Room-Asset relationship deleted successfully');
        }
        
        return redirect()->to('/room-assets')->with('error', 'Error deleting room-asset relationship');
    }

    private function getAvailableAssets($roomId)
    {
        $assignedAssetIds = $this->roomAssetModel->where('room_id', $roomId)->findColumn('asset_id');
        
        if (empty($assignedAssetIds)) {
            return $this->assetModel->select('id, name as asset_name, category, description as asset_description, weight, benefit_score')->findAll();
        }
        
        return $this->assetModel->select('id, name as asset_name, category, description as asset_description, weight, benefit_score')->whereNotIn('id', $assignedAssetIds)->findAll();
    }
}
