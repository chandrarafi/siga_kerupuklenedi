<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Jika belum login, redirect ke halaman login
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        // Jika tidak ada argumen, lewati filter
        if (empty($arguments)) {
            return $request;
        }

        // Ambil role dari session
        $role = session()->get('role');

        // Cek apakah role user sesuai dengan yang diizinkan
        if (!in_array($role, $arguments)) {
            // Redirect sesuai role
            if ($role == 'admin') {
                return redirect()->to('/admin/dashboard');
            } elseif ($role == 'pegawai') {
                return redirect()->to('/pegawai/dashboard');
            } else {
                return redirect()->to('/login');
            }
        }

        return $request;
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
