<?php

namespace App\Controllers;

use App\Models\AssetModel;

class Assets extends BaseController
{
    protected $assetModel;
    
    public function __construct()
    {
        $this->assetModel = new AssetModel();
    }

    public function index()
    {
        $data = $this->getBaseViewData();
        $data['assets'] = $this->assetModel->findAll();
        return view('assets/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'category' => $this->request->getPost('category'),
                'purchase_date' => $this->request->getPost('purchase_date'),
                'purchase_price' => $this->request->getPost('purchase_price'),
                'condition' => $this->request->getPost('condition'),
                'serial_number' => $this->request->getPost('serial_number'),
            ];

            if ($this->assetModel->insert($data)) {
                return redirect()->to('/assets')->with('success', 'Asset created successfully');
            }
            
            return redirect()->back()->withInput()->with('errors', $this->assetModel->errors());
        }

        return view('assets/create', $this->getBaseViewData());
    }

    public function edit($id)
    {
        $asset = $this->assetModel->find($id);
        if (!$asset) {
            return redirect()->to('/assets')->with('error', 'Asset not found');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'category' => $this->request->getPost('category'),
                'purchase_date' => $this->request->getPost('purchase_date'),
                'purchase_price' => $this->request->getPost('purchase_price'),
                'condition' => $this->request->getPost('condition'),
                'serial_number' => $this->request->getPost('serial_number'),
            ];

            if ($this->assetModel->update($id, $data)) {
                return redirect()->to('/assets')->with('success', 'Asset updated successfully');
            }
            
            return redirect()->back()->withInput()->with('errors', $this->assetModel->errors());
        }

        $data = $this->getBaseViewData();
        $data['asset'] = $asset;
        return view('assets/edit', $data);
    }

    public function delete($id)
    {
        if ($this->assetModel->delete($id)) {
            return redirect()->to('/assets')->with('success', 'Asset deleted successfully');
        }
        
        return redirect()->to('/assets')->with('error', 'Error deleting asset');
    }
}
