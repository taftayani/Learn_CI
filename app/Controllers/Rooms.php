<?php

namespace App\Controllers;

use App\Models\RoomModel;

class Rooms extends BaseController
{
    protected $roomModel;
    
    public function __construct()
    {
        $this->roomModel = new RoomModel();
    }

    public function index()
    {
        $data = $this->getBaseViewData();
        $data['rooms'] = $this->roomModel->findAll();
        return view('rooms/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'location' => $this->request->getPost('location'),
                'capacity' => $this->request->getPost('capacity'),
            ];

            if ($this->roomModel->insert($data)) {
                return redirect()->to('/rooms')->with('success', 'Room created successfully');
            }
            
            return redirect()->back()->withInput()->with('errors', $this->roomModel->errors());
        }

        return view('rooms/create', $this->getBaseViewData());
    }

    public function edit($id)
    {
        $room = $this->roomModel->find($id);
        if (!$room) {
            return redirect()->to('/rooms')->with('error', 'Room not found');
        }

        if ($this->request->getMethod() === 'PUT') {
            $data = [
                'name' => $this->request->getVar('name'),
                'description' => $this->request->getVar('description'),
                'location' => $this->request->getVar('location'),
                'capacity' => $this->request->getVar('capacity'),
            ];

            if ($this->roomModel->update($id, $data)) {
                return redirect()->to('/rooms')->with('success', 'Room updated successfully');
            }
            
            return redirect()->back()->withInput()->with('errors', $this->roomModel->errors());
        }

        $data = $this->getBaseViewData();
        $data['room'] = $room;
        return view('rooms/edit', $data);
    }

    public function delete($id)
    {
        if ($this->roomModel->delete($id)) {
            return redirect()->to('/rooms')->with('success', 'Room deleted successfully');
        }
        
        return redirect()->to('/rooms')->with('error', 'Error deleting room');
    }
}
