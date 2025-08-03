<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Validation\Rule;

use Illuminate\Validation\ValidationException; 

class UserController extends Controller
{

    protected $userService;

    // Injeção de dependência do UserService
    public function __construct(UserService $userService)
    {
        $this->middleware('auth'); // Garante que o usuário está autenticado
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user) 
    {
        $this->authorize('manage-users', $user);
        $roles = \Spatie\Permission\Models\Role::all();
        return view('Backoffice.partials.user-edit-form', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user) 
    {
        $this->authorize('manage-users', $user);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        try {
            $this->userService->update($user, $request->all());
            if ($request->ajax()) {
                return response()->json(['message' => 'User updated successfully!'], 200);
            }
            return redirect()->route('Backoffice.users.index')->with('success', 'User updated successfully!');
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Error updating user: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error updating user: ' . $e->getMessage())->withInput();
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
