<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    protected $session;
    protected $userModel;

    public function __construct()
    {
        $this->session = session();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->has('user_id')) {
            return redirect()->to(site_url('backend/home'));
        }
        return view('auth/login');
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            // Set session data
            $this->session->set([
                'user_id' => $user['id_user'],
                'username' => $user['username'],
                'nama' => $user['nama'],
                'role' => $user['role'],
                'logged_in' => true
            ]);

            // Redirect ke backend/home setelah login berhasil
            return redirect()->to(site_url('backend/home'))
                           ->with('success', 'Login berhasil');
        }

        return redirect()->back()
                        ->with('error', 'Username atau password salah')
                        ->withInput();
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to(base_url('login'))
                        ->with('success', 'Berhasil logout');
    }
}