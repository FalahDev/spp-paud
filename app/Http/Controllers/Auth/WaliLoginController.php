<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class WaliLoginController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |

     */

    use AuthenticatesUsers;

    protected $guard = 'wali';

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/info';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->redirectTo = route('dasborwali.index');
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.wali-login');
    }

    protected function guard()
    {
        return Auth::guard('wali');
    }

    public function username()
    {
        return 'ponsel';
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ponsel'   => 'required|numeric',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        $auth = $request->only('ponsel', 'password');

        // Log::debug($auth);

        if (Auth::guard('wali')->attempt($auth)) {

            // dd(auth()->guard('wali')->user());
            // Log::debug($auth);
            return Redirect::route('dasborwali.index');

        }

        return redirect()->back()->withErrors(['unauthorized' => 'Nomor ponsel atau password salah.']);

    }

    public function loggedOut()
    {
        return redirect()->route('dasborwali.index');
    }

}
