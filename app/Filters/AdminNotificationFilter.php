<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProfileChangeRequestModel;

class AdminNotificationFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Only run for logged in staff
        if ($session->get('isLoggedIn') && $session->get('is_staff')) {
            $requestModel = new ProfileChangeRequestModel();
            $pendingCount = $requestModel->where('is_approved', false)
                                         ->where('is_rejected', false)
                                         ->countAllResults();
            
            // Set for global use in views
            \Config\Services::renderer()->setData(['pending_requests_count' => $pendingCount]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
