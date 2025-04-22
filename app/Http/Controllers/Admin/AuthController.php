<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // Assuming UserProfile model for storing user data
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function showRegistrationForm()
    {
        return view('admin.register');
    }
    public function register(Request $request)
    {
        Log::info('Controller reached');

        Log::info('Incoming Request', $request->all());


        // Validate input
        try {
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed|min:8',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        }
        

        // Combine first and last name
        $name = "{$request->first_name} {$request->last_name}";

        Log::info('User registration data', [
            'name' => $name,
            'email' => $request->email
        ]);

        // Create user with hashed password
        try {
            $user = User::create([
                'name' => $name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            Log::info('User created successfully', ['user_id' => $user->id]);
        } catch (\Exception $e) {
            Log::error('User creation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'User registration failed.'])->withInput();
        }

        // Log the user in
        Auth::login($user);

        Log::info('User logged in', ['user_id' => $user->id]);

        // Redirect to the admin dashboard
    return redirect()->route('admin.dashboard');
    }
    
    
    

public function login(Request $request)
{
    // Validate the input data
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Attempt to log in
    if (Auth::attempt($credentials, $request->remember)) {
        // Authentication was successful
           // Redirect to the admin dashboard
    return redirect()->route('admin.dashboard'); 
    }

    // Authentication failed
    return back()->withErrors(['email' => 'Invalid email or password']);
}

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }
}
