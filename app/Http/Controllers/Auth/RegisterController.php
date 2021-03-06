<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Category;
use App\Http\Requests\RequestRegister;
use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterSuccess;
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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function getFormRegister(){
        $register= User::all();
        $category =Category::all();
        $viewData=[
            'register'=>$register,
            'category' =>$category,
        ];
        return view('auth.register',$viewData);
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
    public function postRegister(RequestRegister $request){
        $data =$request->except('_token');
        $data['password'] = Hash::make($data['password']);
        $data['created_at'] = Carbon::now();
        $id = User::InsertGetId($data);
        if($id){
            \Session::flash('toastr',[
                'type'      =>'success',
                'message'   =>'Ch??o m???ng b???n ?????n v???i shop ch??ng t??i'
            ]); 
            
            Mail::to($request->email)->send(new RegisterSuccess($request->name));
            if (\Auth::attempt(['email' => $request->email,'password' => $request->password])) {
                return redirect()->intended('/');
            }
            return redirect()->route('get.login');
        }
        return redirect()->back();
    }
}
