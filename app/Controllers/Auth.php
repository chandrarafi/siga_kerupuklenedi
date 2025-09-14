<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();

        helper('cookie');
    }

    public function index()
    {

        if (session()->get('logged_in')) {
            return redirect()->to(session()->get('redirect_url') ?? 'admin');
        }

        return view('auth/login');
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember') == 'on';


        $user = $this->userModel->where('username', $username)
            ->orWhere('email', $username)
            ->first();


        if ($user) {


            if ($user['status'] !== 'active') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Akun Anda tidak aktif. Silakan hubungi administrator.'
                ]);
            }


            if (password_verify($password, $user['password'])) {

                $this->userModel->update($user['id'], [
                    'last_login' => date('Y-m-d H:i:s')
                ]);


                $sessionData = [
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'logged_in' => true
                ];
                session()->set($sessionData);


                if ($remember) {
                    $this->setRememberMeCookie($user['id']);
                }

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Login berhasil',
                    'redirect' => site_url('admin')
                ]);
            }
        } else {
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Username/Email atau Password salah'
        ]);
    }

    public function logout()
    {

        if (get_cookie('remember_token')) {
            delete_cookie('remember_token');
            delete_cookie('user_id');
        }


        session()->destroy();

        return redirect()->to('auth')->with('message', 'Anda telah berhasil logout');
    }

    protected function setRememberMeCookie($userId)
    {
        $token = bin2hex(random_bytes(32));


        $this->userModel->update($userId, [
            'remember_token' => $token
        ]);


        $expires = 30 * 24 * 60 * 60; // 30 hari
        $secure = isset($_SERVER['HTTPS']); // Set secure hanya jika HTTPS


        set_cookie(
            'remember_token',
            $token,
            $expires,
            '',  // domain
            '/', // path
            '', // prefix
            $secure, // secure - hanya set true jika HTTPS
            true  // httponly
        );


        set_cookie(
            'user_id',
            $userId,
            $expires,
            '',
            '/',
            '',
            $secure,
            true
        );
    }
}
