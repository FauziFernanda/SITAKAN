<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->has('logged_in')) {
            return redirect()->to(base_url('login'));
        }

        // For register page, check if user is admin
        $currentPath = $request->getPath();
        if ($currentPath == 'backend/register' && $session->get('role') !== 'admin') {
            return redirect()->to(base_url('backend/home'))
                           ->with('error', 'Akses ditolak. Anda tidak memiliki izin.');
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after
    }
}