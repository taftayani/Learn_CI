<?php

namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
    }



    public function index()
    {
        $userRole = session()->get('user_role');
        if (!in_array($userRole, ['Super Admin', 'Admin', 'Leader'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $data = $this->getBaseViewData();
        $data['users'] = $this->userModel->findAll();
        return view('users/index', $data);
    }

    public function create()
    {
        $userRole = session()->get('user_role');
        if (!in_array($userRole, ['Super Admin', 'Admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'role' => $this->request->getPost('role'),
            ];

            if ($this->userModel->insert($data)) {
                return redirect()->to('/users')->with('success', 'User created successfully');
            }
            
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        return view('users/create', $this->getBaseViewData());
    }

    public function edit($id)
    {
        $userRole = session()->get('user_role');
        if (!in_array($userRole, ['Super Admin', 'Admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('error', 'User not found');
        }

        if ($this->request->getMethod() === 'PUT') {
            $data = [
                'name' => $this->request->getVar('name'),
                'email' => $this->request->getVar('email'),
                'role' => $this->request->getVar('role'),
            ];
            
            if ($this->request->getVar('password')) {
                $data['password'] = $this->request->getVar('password');
            }

            if ($this->userModel->update($id, $data)) {
                return redirect()->to('/users')->with('success', 'User updated successfully');
            }
            
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        $data = $this->getBaseViewData();
        $data['edit_user'] = $user;
        return view('users/edit', $data);
    }

    public function delete($id)
    {
        $userRole = session()->get('user_role');
        if (!in_array($userRole, ['Super Admin', 'Admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        if (session()->get('user_id') == $id) {
            return redirect()->to('/users')->with('error', 'Cannot delete your own account');
        }
        
        if ($this->userModel->delete($id)) {
            return redirect()->to('/users')->with('success', 'User deleted successfully');
        }
        
        return redirect()->to('/users')->with('error', 'Error deleting user');
    }
}
