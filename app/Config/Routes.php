<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login/attempt', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');

$routes->get('forgot-password', 'Auth::forgotPassword');
$routes->post('forgot-password/send', 'Auth::sendResetLink');
$routes->get('reset-password/(:any)', 'Auth::resetPassword/$1');
$routes->post('reset-password/update', 'Auth::updatePasswordWithToken');

$routes->get('password/change', 'Auth::changePassword');
$routes->post('password/update', 'Auth::updatePassword');

$routes->get('dashboard', 'Quiz::dashboard');
$routes->get('quiz/(:num)/start', 'Quiz::start/$1');
$routes->get('quiz/(:num)/take', 'Quiz::take/$1');
$routes->post('quiz/(:num)/submit', 'Quiz::submit/$1');
$routes->post('quiz/(:num)/log-violation', 'Quiz::logViolation/$1');
$routes->get('quiz/success', 'Quiz::success');
$routes->post('quiz/retest/(:num)', 'Quiz::requestRetest/$1');
$routes->get('quiz/certificate/(:num)', 'Quiz::certificate/$1');

// Profile & Settings
$routes->get('results/(:num)', 'Quiz::results/$1');
$routes->get('profile', 'Quiz::profile');
$routes->post('profile/request', 'Quiz::saveProfileRequest');

$routes->group('admin', ['namespace' => 'App\Controllers'], static function ($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('analytics', 'Admin::analytics');
    $routes->get('audit-logs', 'Admin::auditLogs');
    
    // User Management
    $routes->get('users', 'Admin::users');
    $routes->get('users/create', 'Admin::createUser');
    $routes->get('users/edit/(:num)', 'Admin::editUser/$1');
    $routes->post('users/save', 'Admin::saveUser');
    $routes->get('users/import', 'Admin::importUsers');
    $routes->post('users/process-import', 'Admin::processUserImport');
    $routes->get('users/delete/(:num)', 'Admin::deleteUser/$1');
    $routes->post('users/bulk-assign', 'Admin::bulkAssignQuiz');
    $routes->post('users/bulk-delete', 'Admin::bulkDeleteUsers');
    
    // Quiz Management
    $routes->get('quizzes', 'Admin::quizzes');
    $routes->get('quizzes/create', 'Admin::createQuiz');
    $routes->get('quizzes/edit/(:num)', 'Admin::editQuiz/$1');
    $routes->post('quizzes/save', 'Admin::saveQuiz');
    $routes->get('quizzes/delete/(:num)', 'Admin::deleteQuiz/$1');
    $routes->get('quizzes/toggle-release/(:num)', 'Admin::toggleResultsRelease/$1');
    
    // Question Management
    $routes->get('questions', 'Admin::questions');
    $routes->get('questions/create', 'Admin::createQuestion');
    $routes->get('questions/edit/(:num)', 'Admin::editQuestion/$1');
    $routes->post('questions/save', 'Admin::saveQuestion');
    $routes->get('questions/delete/(:num)', 'Admin::deleteQuestion/$1');
    
    // Topic Management
    $routes->get('topics', 'Admin::topics');
    $routes->post('topics/save', 'Admin::saveTopic');
    $routes->get('topics/delete/(:num)', 'Admin::deleteTopic/$1');

    // Assignment & Retest management
    $routes->get('assignments', 'Admin::assignments');
    $routes->post('assignments/approve-retest/(:num)', 'Admin::approveRetest/$1');
    $routes->post('assignments/reject-retest/(:num)', 'Admin::rejectRetest/$1');
    $routes->get('assignments/delete/(:num)', 'Admin::deleteAssignment/$1');
    $routes->post('assignments/bulk-delete', 'Admin::bulkDeleteAssignments');
    
    // Profile Request Management
    $routes->get('profile-requests', 'Admin::profileRequests');
    $routes->post('profile-requests/approve/(:num)', 'Admin::approveProfileRequest/$1');
    $routes->post('profile-requests/reject/(:num)', 'Admin::rejectProfileRequest/$1');

    // Settings
    $routes->get('settings', 'Admin::settings');
    $routes->post('settings/save', 'Admin::saveSettings');

    // Export
    $routes->get('quizzes/export/(:num)', 'Admin::exportResults/$1');

    // API for Real-time Updates
    $routes->get('get-pending-count', 'Admin::getPendingCount');
});
