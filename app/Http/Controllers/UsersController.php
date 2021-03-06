<?php

namespace App\Http\Controllers;

use App\Helpers\Contracts\LdapHelperContract;
use App\Http\Requests\UserStoreRequest;
use App\Profile;
use App\Role;
use App\School;
use App\Student;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class UsersController extends Controller
{
    /**
     * Controller constructor. Middleware can be defined here.
     */
    public function __construct()
    {
        $this->middleware('auth')->only([
            'index',
            'create',
            'store',
            'edit',
            'update',
            'destroy',
            'confirmDestroy',
        ]);

        $this->middleware('can:viewAdminIndex,App\User')->only('index');

        $this->middleware('can:view,user')->only('show');

        $this->middleware('can:create,App\User')->only([
            'create',
            'store',
        ]);
        
        $this->middleware('can:update,user')->only([
            'edit',
            'update',
        ]);

        $this->middleware('can:delete,user')->only([
            'destroy',
            'confirmDestroy',
        ]);
    }

    /**
     * Display a listing of Users.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('users.index');
    }

    /**
     * Show the User info.
     *
     * @param  User   $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the User info.
     *
     * @param  User   $user
     * @return \Illuminate\View\View
     */
    public function showBookmarks(User $user)
    {
        return view('users.bookmarks', [
            'user' => $user,
            'profile_bookmarks' => $user->bookmarked(Profile::class)->orderBy('last_name')->get(),
            'student_bookmarks' => $user->bookmarked(Student::class)->orderBy('full_name')->get(),
        ]);
    }

    /**
     * Show the view to add a new user
     *
     * @return void
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a new user
     *
     * @param Request $request
     * @return void
     */
    public function store(UserStoreRequest $request, LdapHelperContract $ldap)
    {
        $name = $request->input('name');
        $user = $ldap->getUser($name);

        if ($user) {
            if ($request->input('create_profile')) {
                return redirect()->route('profiles.create', ['user' => $user]);
            }

            return redirect()->route('users.index')->with('flash_message', "Added user {$name}");
        }

        return back()->with('flash_message', "Unable to find user with username &ldquo;{$name}&rdquo; in the directory.");
    }

    /**
     * Show the view to edit the User
     * 
     * @param  User   $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $school_editor_role = $roles->firstWhere('name', 'school_profiles_editor');
        $department_editor_role = $roles->firstWhere('name', 'department_profiles_editor');
        $schools = School::pluck('display_name','id')->all();
        $departments = User::whereNotNull('department')
                        ->orderBy('department')
                        ->select('department')
                        ->distinct()
                        ->pluck('department', 'department')
                        ->all();

        return view('users.edit', compact('user','roles','schools','departments','school_editor_role','department_editor_role'));
    }

    /**
     * Update the User in the database.
     * 
     * @param  User        $user
     * @param  UserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(User $user, Request $request)
    {
        $user->update($request->all());

        if ($request->additional_departments || $request->additional_schools || $user->setting()->exists()) {
            $user->setting()->updateOrCreate(['user_id' => $user->id,], [
                'additional_departments' => $request->additional_departments ? explode(',', $request->additional_departments) : null,
                'additional_schools' => $request->additional_schools ?? null,
            ]);
        }

        $user->roles()->sync($request->input('role_list') ?: []);

        Cache::flush();

        return redirect()->route('users.show', [$user->pea])
            ->with('flash_message', 'The user has been updated.');
    }

    /**
     * Confirm deletion of the user.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function confirmDestroy(User $user)
    {
        return view('users.delete', compact('user'));
    }

    /**
     * Remove the User from the database.
     * 
     * @param  User   $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('flash_message', 'The user has been removed.');
    }

}
