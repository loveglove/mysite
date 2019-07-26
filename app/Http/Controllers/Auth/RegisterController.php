<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\SocialProvider;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

use Socialite;
use Auth;

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
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {

        $user = User::where('email', $data['email'])->first();
        if($user){
            // user exists from a social login creation, add password to model
            $user->password = bcrypt($data['password']);
            $user->save();
            return $user;

        }else{
            // no previous social login, create user;
            $avatar = 'https://www.gravatar.com/avatar/'.md5($data['email']).'/?d=identicon&r=g';
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'avatar' => $avatar,
            ]);
        }

    }


    /**
     * Redirect the user to the OAuth Provider.
     *
     * @return Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }


    /**
     * Obtain the user information from provider.  
     * Check if the user already exists by looking up their provider_id in the database.
     * If the user exists, log them in. Otherwise, create a new user then log them in.  
     * After that redirect them to the authenticated users homepage.
     *
     * @return Response
     */
    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->user();

        // create or find the user
        $authUser = $this->findOrCreateUser($user, $provider);
        // log the user in
        Auth::login($authUser, true);
        // redirect to variable at top
        return redirect($this->redirectTo);
    }

    /**
     * If a user has registered before using social auth, return the user
     * else, create a new user object.
     * @param  $user Socialite user object
     * @param $provider Social auth provider
     * @return  User
     */
    public function findOrCreateUser($user, $provider)
    {
        //find user 
        $sp = SocialProvider::where('provider_id', $user->id)->where('provider', $provider)->first();
        
        //if found return the user, if not create it and return it
        if($sp) {
            $u = User::where('id', $sp->user_id)->first();
            $u->avatar = $sp->avatar;
            $u->save();
            return $u;
        }

        $user_existing = User::where('email', $user->email)->first();
        if($user_existing){
            // if email exists already, add new social provider
            $sp_new = SocialProvider::create([
                'user_id' => $user_existing->id,
                'provider' => $provider,
                'provider_id' => $user->id,
                'avatar'   => $user->avatar,
            ]);

            $user_existing->avatar = $user->avatar;
            $user_existing->save();
            return $user_existing;

        }else{
            // if email doesnt exist yet, create new user and social provider
            $user_new = User::create([
                'name'     => $user->name,
                'email'    => $user->email,  
            ]);

            $sp_new = SocialProvider::create([
                'user_id' => $user_new->id,
                'provider' => $provider,
                'provider_id' => $user->id,
                'avatar'   => $user->avatar,
            ]);

            $user_new->avatar = $user->avatar;
            $user_new->save();
            return $user_new;
        }
        
    }

}
