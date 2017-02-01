@extends('layouts.app')
@section('extra_heads')
    <style type="text/css">
        body {
            padding-top: 50px;
        }
    </style>
@endsection
@section('body')
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-admin-top"
                        aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{route('app.home')}}">IDS-4-IDPs</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="navbar-admin-top">
                <form class="navbar-form navbar-left hidden-sm">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Search for...">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button">
                                <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                            </button>
                        </span>
                    </div>
                </form>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="{{route('deo.persons')}}">IDP Records</a></li>
                    <li><a href="{{route('deo.camps')}}">Camps</a></li>
                    <li><a href="{{route('deo.organizations')}}">Organizations</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                            <span class="hidden-lg hidden-md hidden-sm">My Account</span>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{route('account.profile')}}">Update Profile</a></li>
                            <li><a href="{{route('account.password')}}">Change Password</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#" id="logout-button">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
    <div class="sh-100vh">
        @yield('content')
    </div>
    <nav class="navbar navbar-default no-margin navbar-static-top">
        <div class="container">
            <div id="navbar-admin-bottom">
                <ul class="nav navbar-nav">
                    <li><a href="#">About</a></li>
                    <li><a href="#">Credits</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#">View on GitHub</a></li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
@endsection