<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserController extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $users = $userModel->orderBy('id_user', 'DESC')->findAll();

        return view('backend/register', ['users' => $users]);
    }

    public function create()
    {
        $request = $this->request;
        if (!$request->is('post')) {
            return redirect()->back();
        }

        $nama = trim((string)$request->getPost('nama'));
        $username = trim((string)$request->getPost('username'));
        $password = (string)$request->getPost('password');
        $role = (string)$request->getPost('role');

        // Basic validation
        $errors = [];
        if (empty($nama)) $errors[] = 'Nama harus diisi';
        if (empty($username)) $errors[] = 'Username harus diisi';
        if (empty($password)) $errors[] = 'Password harus diisi';
        if (!in_array($role, ['admin', 'pustakawan'])) $role = 'pustakawan';

        $userModel = new UserModel();
        $existing = $userModel->where('username', $username)->first();
        if ($existing) $errors[] = 'Username sudah digunakan';

        if (!empty($errors)) {
            session()->setFlashdata('errors', $errors);
            return redirect()->back()->withInput();
        }

        // Hash password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'nama' => $nama,
            'username' => $username,
            'password' => $hash,
            'role' => $role,
        ];

        $userModel->insert($data);
        session()->setFlashdata('success', 'Akun berhasil dibuat');
        return redirect()->to(base_url('backend/register'));
    }

    public function edit($id = null)
    {
        if (empty($id)) return redirect()->to(base_url('backend/register'));

        $userModel = new UserModel();
        $user = $userModel->find($id);
        if (!$user) return redirect()->to(base_url('backend/register'));

        $users = $userModel->orderBy('id_user', 'DESC')->findAll();
        return view('backend/register', ['users' => $users, 'editUser' => $user]);
    }

    public function update($id = null)
    {
        $request = $this->request;
        if (empty($id) || !$request->is('post')) return redirect()->to(base_url('backend/register'));

        $nama = trim((string)$request->getPost('nama'));
        $username = trim((string)$request->getPost('username'));
        $password = (string)$request->getPost('password');
        $role = (string)$request->getPost('role');

        $errors = [];
        if (empty($nama)) $errors[] = 'Nama harus diisi';
        if (empty($username)) $errors[] = 'Username harus diisi';
        if (!in_array($role, ['admin', 'pustakawan'])) $role = 'pustakawan';

        $userModel = new UserModel();
        $existing = $userModel->where('username', $username)->where('id_user !=', $id)->first();
        if ($existing) $errors[] = 'Username sudah digunakan oleh akun lain';

        if (!empty($errors)) {
            session()->setFlashdata('errors', $errors);
            return redirect()->back()->withInput();
        }

        $data = ['nama' => $nama, 'username' => $username, 'role' => $role];
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $userModel->update($id, $data);
        session()->setFlashdata('success', 'Akun berhasil diperbarui');
        return redirect()->to(base_url('backend/register'));
    }

    public function delete($id = null)
    {
        $request = $this->request;
        if (empty($id) || !$request->is('post')) return redirect()->to(base_url('backend/register'));

        $userModel = new UserModel();
        $user = $userModel->find($id);
        if (!$user) {
            session()->setFlashdata('errors', ['Data tidak ditemukan']);
            return redirect()->to(base_url('backend/register'));
        }

        $userModel->delete($id);
        session()->setFlashdata('success', 'Akun berhasil dihapus');
        return redirect()->to(base_url('backend/register'));
    }
}
