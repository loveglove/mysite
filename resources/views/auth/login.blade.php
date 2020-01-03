@extends('layouts.auth')

@section('content')
<style>
	
</style>

<section id="wrapper" class="login-register login-sidebar"  style="background-image:url('images/pineapple.jpeg');">
  

    <div class="login-box card">
        <div class="card-body" style="margin: 0 auto; min-width:350px;">


            <!-- LOGO -->
            <a href="/">
                <div class="row mx-auto mt-1 mt-lg-5">
                    <div class="logo">
                        <img src="{{ asset('images/bulb_logo.png') }}" height="78"  alt="">
                    </div>
                    <div class="logo_text">
                        <span>Matt Glover</span><br>
                        <span style='font-size: 18px;'>Developements</span>
                    </div>
                </div>
            </a>


            <!-- LOGIN FORM -->
            <form class="form-horizontal form-material" id="loginform" action="{{ route('login') }}">
          	    {{ csrf_field() }}
                <div class="form-group m-t-40 {{ $errors->has('email') ? ' has-error' : '' }}">
                    <div class="col-xs-12">
                        <input class="form-control" type="text" name="email" required autofocus placeholder="Email" value="{{ old('email') }}">
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                    <div class="col-xs-12">
                        <input class="form-control" type="password" required="" placeholder="Password" value="{{ old('password') }}">
                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                  <div class="col-md-12">
                    <div class="checkbox checkbox-primary pull-left p-t-0">
                      <input id="checkbox-signup" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                      <label for="checkbox-signup"> Remember me </label>
                    </div>
                    <a href="#" id="to-recover" class="text-dark pull-right"><i class="fa fa-lock m-r-5"></i> Forgot pwd?</a> </div>
                </div>
                <div class="form-group text-center m-t-20">
                  <div class="col-xs-12">
                    <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Log In</button>
                  </div>
                </div>
                <div class="row mb-4">
                <div class="col-xs-12 col-sm-12 col-md-12 m-t-10 text-center">
                    <a href="{{ url('/auth/google') }}" class="btn btn-google waves-effect waves-light"><i class="fa fa-google"></i> Google</a>
                    <a href="{{ url('/auth/facebook') }}" class="btn btn-facebook waves-effect waves-light"><i class="fa fa-facebook"></i> Facebook</a>
                    <a href="{{ url('/auth/github') }}" class="btn btn-github waves-effect waves-light"><i class="fa fa-github"></i> Github</a>
                </div>
                </div>
                <div class="form-group m-b-0">
                    <div class="col-sm-12 text-center">
                        <p>Don't have an account? <a id="to-signup" class="text-primary m-l-5 "><b>Sign Up</b></a></p>
                    </div>
                </div>
            </form>


            <!-- REGISTER FORM -->
            <form class="form-horizontal form-material mt-5" id="signupform" method="POST" action="{{ route('register') }}" style="display:none;">
          	    {{ csrf_field() }}
                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <div class="col-xs-12">
                        <input class="form-control" type="text" name="name" required autofocus placeholder="Name" value="{{ old('name') }}">
                        @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <div class="col-xs-12">
                        <input id="email" type="email" class="form-control" name="email" placeholder="Email"  value="{{ old('email') }}" required>
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <div class="col-xs-12">
                        <input id="password" type="password" class="form-control" name="password" placeholder="Password" required>
                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required>
                    </div>
                </div>
                <div class="form-group text-center m-t-20">
                  <div class="col-xs-12">
                    <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Register</button>
                  </div>
                </div>
                <div class="row mb-4">
	                <div class="col-xs-12 col-sm-12 col-md-12 m-t-10 text-center">
	                    <a href="{{ url('/auth/google') }}" class="btn btn-google waves-effect waves-light"><i class="fa fa-google"></i> Google</a>
	                    <a href="{{ url('/auth/facebook') }}" class="btn btn-facebook waves-effect waves-light"><i class="fa fa-facebook"></i> Facebook</a>
	                    <a href="{{ url('/auth/github') }}" class="btn btn-github waves-effect waves-light"><i class="fa fa-github"></i> Github</a>
	                </div>
                </div>
                <div class="form-group m-b-0">
                    <div class="col-sm-12 text-center">
                        <p>Already have an account? <a href="#" id="to-login" class="text-primary m-l-5 "><b>Log In</b></a></p>
                    </div>
                </div>
            </form>

            <!-- FORGOT PASSWORD -->
            <form class="form-horizontal form-material mt-1 mt-lg-5" id="recoverform" method="POST" action="{{ route('password.email') }}" style="display:none;">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="form-group ">
                  <div class="col-xs-12">
                    <h3>Recover Password</h3>
                    <p class="text-muted">Enter your Email and instructions <br>will be sent to you! </p>
                  </div>
                </div>
                <div class="form-group ">
                  <div class="col-xs-12">
                    <input class="form-control" type="text" required="" placeholder="Email">
                  </div>
                </div>
                <div class="form-group text-center m-t-20">
                  <div class="col-xs-12">
                    <button class="btn btn-primary btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Reset</button>
                  </div>
                </div>
                <div class="form-group text-center m-t-20">
                    <div class="col-sm-12 text-center">
                        <p>Already have an account? <a href="#" id="to-login" class="text-primary m-l-5 "><b>Log In</b></a></p>
                    </div>
                </div>
            </form>


        </div>
    </div>
</section>

@endsection

@section('scripts')

<script>
    
    // water ripples 
    $('#wrapper').ripples({
        resolution: 512,
        dropRadius: 20,
        perturbance: 0.01,
    });
    
    $('body').on('click','#to-recover',function(){
        $("#loginform, #signupform").fadeOut(function() {
            setTimeout(function(){
                $("#recoverform").fadeIn();
            },500);
        }); 
    });

    $('body').on('click','#to-login',function(){
        $("#recoverform, #signupform").fadeOut(function() {
            setTimeout(function(){
                $("#loginform").fadeIn();
            },500);
        }); 
    });

    $('body').on('click','#to-signup',function(){
        $("#recoverform, #loginform").fadeOut(function() {
            setTimeout(function(){
                $("#signupform").fadeIn();
            },500);
        }); 
    });


</script>

@endsection
<!-- 


<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>

                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <a href="{{ url('/auth/google') }}" class="btn btn-google"><i class="fa fa-google"></i> Google</a>
                                <a href="{{ url('/auth/facebook') }}" class="btn btn-facebook"><i class="fa fa-facebook"></i> Facebook</a>
                                <a href="{{ url('/auth/github') }}" class="btn btn-github"><i class="fa fa-github"></i> Github</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> -->
</div>