<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User; 
use App\Services\UserService; 

class BackofficeController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function admin() 
    {
        if (Gate::denies('view-backoffice')) {
            abort(403);
        }
        return view('Backoffice.dashboard');
    }

    public function getOverviewContent()
    {
        if (Gate::denies('view-backoffice')) {
            abort(403);
        }
        return view('Backoffice.partials.overview');
    }

    public function getUsersContent()
    {
        if (Gate::denies('manage-users')) {
            abort(403);
        }

        $users = $this->userService->getPaginatedUsers(10); 

        return view('Backoffice.partials.users', compact('users'));
    }

}