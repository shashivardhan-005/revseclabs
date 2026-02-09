<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProfileChangeRequestModel;
use App\Models\AssignmentModel;

class AdminNotificationFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Only run for logged in staff
        if ($session->get('isLoggedIn') && $session->get('is_staff')) {
            $requestModel = new ProfileChangeRequestModel();
            $profileCount = $requestModel->where('is_approved', false)
                                         ->where('is_rejected', false)
                                         ->countAllResults();

            $assignmentModel = new AssignmentModel();
            $retestCount = $assignmentModel->where('retest_requested', true)
                                           ->countAllResults();
            
            $pendingCount = $profileCount + $retestCount;
            
            // Set for global use in views
            \Config\Services::renderer()->setData([
                'profile_requests_count' => $profileCount,
                'retest_requests_count' => $retestCount
            ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
