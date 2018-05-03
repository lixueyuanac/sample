<?php

namespace App\Http\Controllers;

use App\Models\User ;
use Illuminate\Http\Request;
use Mail;
use Illuminate\Support\Facades\Auth;
class UsersController extends Controller
{
    public function __construct(){
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail']
        ]);
        //只允许未登录用户访问注册和登录页面
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }
    public function index(){
        $users = User::paginate(10);
        return view('users.index',compact('users'));
    }
    //
    public function create(){
        return view('users.create');
    }

    public function show(User $user){
        //session()->put('success', '欢迎，您将在这里开启一段新的旅程~');
        //dump($user->name);exit;
        $statuses = $user->statuses()
                           ->orderBy('created_at', 'desc')
                           ->paginate(30);
        return view('users.show', compact('user', 'statuses'));
    }
    //用户注册
    public function store(Request $request){
        $this->validate($request,[
            'name'=>'required|max:50',
            'email'=>'required|email|unique:users|max:255',
            'password'=>'required|confirmed|min:6'
        ]);
        $user=User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>bcrypt($request->password),
            ]);
        //Auth::login($user);登录
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已经发送您填写都邮箱内，请注意查收。');
        return redirect()->route('users.show',[$user]) ;
    }
    //发送邮件
    public function sendEmailConfirmationTo($user){
        $view = 'emails.confirm';
        $data = compact('user');
        $to = $user->email;
        $subject='感谢注册论坛！请确认您的邮箱激活';
        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }
    //激活成功
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }

    public function edit(User $user){
        $this->authorize('update', $user);
        return view('users.edit',compact('user'));
    }

    public function update(User $user,Request $request){
        $this->validate($request,[
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6',
        ]);
        //应用授权策略
        $this->authorize('update', $user);

        $data=[];
        $data['name'] = $request->name;
        if ($request->password){
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('info', '个人资料更新成功');
        return redirect()->route('users.show',$user->id);
    }
    //删除指定用户
    public function destroy(User $user){
        $user->delete();
        session()->flash('success','成功删除指定用户');
        return back();
    }
    //我关注的
    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }
    //关注我的
    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = '粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }

}
