@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs profile-tab" role="tablist">
                    <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#settings" role="tab" aria-expanded="true">Loft Bot</a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#log" role="tab" aria-expanded="false">Entry Log</a> </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- first tab -->
                    <div class="tab-pane active" id="settings" role="tabpanel" aria-expanded="true">
                        <div class="card-body">
                            <p> Loft Bot Settings </p>
                        </div>
                    </div>
                    <!-- end first tab -->
                    <!--second tab-->
                    <div class="tab-pane" id="log" role="tabpanel" aria-expanded="false">
                        <div class="card-body">
                            <p> Door Entry Table </p>
                        </div>
                    </div>
                    <!-- end second tab -->
                </div>
                <!-- end Nav tabs -->
            </div>
        </div>

        <div class="col-lg-4 col-xlg-3 col-md-5">
            <div class="card">
                <div class="card-body">
                    <center class="m-t-30"> <img src="{{ Auth::user()->avatar }}" class="img-circle" width="150">
                        <h4 class="card-title m-t-10">{{ Auth::user()->name }}</h4>
                        <h6 class="card-subtitle">Accoubts Manager Amix corp</h6>
                        <div class="row text-center justify-content-md-center">
                            <div class="col-4"><a href="javascript:void(0)" class="link"><i class="icon-people"></i> <font class="font-medium">254</font></a></div>
                            <div class="col-4"><a href="javascript:void(0)" class="link"><i class="icon-picture"></i> <font class="font-medium">54</font></a></div>
                        </div>
                    </center>
                </div>
                <div>
                    <hr> </div>
                <div class="card-body"> <small class="text-muted">Email address </small>
                    <h6>{{ Auth::user()->email }}</h6> <small class="text-muted p-t-30 db">Phone</small>
                    <h6>+91 654 784 547</h6> <small class="text-muted p-t-30 db">Address</small>
                    <h6>71 Pilgrim Avenue Chevy Chase, MD 20815</h6>
                    <div class="map-box">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d470029.1604841957!2d72.29955005258641!3d23.019996818380896!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e848aba5bd449%3A0x4fcedd11614f6516!2sAhmedabad%2C+Gujarat!5e0!3m2!1sen!2sin!4v1493204785508" style="border:0" allowfullscreen="" width="100%" height="150" frameborder="0"></iframe>
                    </div> <small class="text-muted p-t-30 db">Social Profile</small>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
