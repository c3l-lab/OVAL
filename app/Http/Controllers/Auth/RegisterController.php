<?php

namespace oval\Http\Controllers\Auth;

use oval\User;
use Validator;
use oval\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/register';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest');
        $this->middleware('auth');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            // 'email' => 'required|email|max:255|unique:users',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user or edit existing user's password and role after validation.
     * 
     * Check if user with email address entered already exists.
     * If new, create user with detailes supplied.
     * If exists (because they were created via LTI link), 
     * change password and role.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = User::where('email', '=', $data['email'])->first();
        if (empty($user)) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);

        }
        $user->password = bcrypt($data['password']);
        $user->role = "A";
        $user->save();
        return $user;

        //-- below is original Laravel method 
        // return User::create([
        //     'first_name' => $data['first_name'],
        //     'last_name' => $data['last_name'],
        //     'email' => $data['email'],
        //     'password' => bcrypt($data['password']),
        //     'role'=>'A',
        // ]);
    }

    /**
     * Show the application registration form. (Overrides method from Illuminate\Foundation\Auth\RegistersUsers)
     *
     * If the user requesting this route has admin role, show the form.
     * Else show error message page.
     * 
     * @return \Illuminate\Http\Response
     */
    protected function showRegistrationForm() {
        $user = Auth::user();
        
        if ($user->role=="A") {
            return view('auth.register');
        }
        else {
            return view('pages.message-page', ['title'=>"ERROR", 'message'=>"You must be an administrator to create an account."]);
        }
    }


    /**
     * Handle a registration request for the application. (Overrides method from Illuminate\Foundation\Auth\RegistersUsers)
     *
     * This override method stops the newly created user to be logged in upon account creation,
     * and display message that user was created/updated.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        // $this->guard()->login($user);

        return $this->registered($request, $user)
                        ?: redirect()->back()->with('message','User "'.$user->fullName().'" was added/updated');
    }
}
