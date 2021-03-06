@extends('layouts.admin')
@section('content')
    <div class="container">
        <form action="{{route('user.manage_list')}}" method="post" id="manageList" onsubmit="return false;">
            <input name="action" value="" type="hidden">
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="page-header">
                        App. Users
                        <span class="pull-right">
                        <!-- Split button -->
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#newUserModal">New User</button>
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                &nbsp;<span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                        <ul class="dropdown-menu">
                            <li><a href="#" class="disabled small">with selected...</a></li>
                            <li role="separator" class="divider"></li>
                            <li>
                                <button type="submit" value="delete" class="btn-link">Delete</button>
                            </li>
                            <li>
                                <button type="submit" value="restore" class="btn-link">Restore</button>
                            </li>
                            <li role="separator" class="divider"></li>
                            <li>
                                <button type="submit" value="discard" class="btn-link">Delete Permanently</button>
                            </li>
                        </ul>
                        </div>
                    </span>
                    </h2>
                </div>
            </div>
            <div class="text-center padding-1em"><span id="notify"></span></div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Names</th>
                        <th width="20%">Email</th>
                        <th width="15%">Phone</th>
                        <th width="3%"><input type="checkbox" class="toggle-btn" data-toggle="input.togglable"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $sn = startSN($users); ?>
                    @foreach($users as $user)
                        <tr @if($user->trashed()) class="warning" @endif >
                            <td>{{$sn++}}</td>
                            <td>{{$user->name()}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->phone}}</td>
                            <td><input name="id[]" type="checkbox" value="{{$user->id}}" class="togglable"></td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="5" class="text-center">{{$users->links()}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="newUserModal" tabindex="-1" role="dialog" aria-labelledby="modal-title">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form onsubmit="return false;" id="newUserForm" action="{{route('user.add')}}">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modal-title">Add New User</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label for="role" class="col-sm-3 control-label">Role</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="role" name="role" required>
                                        @foreach($roles as $role)
                                            <option value="{{$role->id}}">{{$role->label}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="f-name" class="col-sm-3 control-label">Names</label>
                                <div class="col-sm-3">
                                    <input type="text" maxlength="255" class="form-control" id="f-name" name="first-name" placeholder="First Name"
                                           required>
                                </div>
                                <div class="col-sm-3">
                                    <label for="m-name" class="sr-only">Middle Name</label>
                                    <input type="text" maxlength="255" class="form-control" id="m-name" name="middle-name" placeholder="Middle Name">
                                </div>
                                <div class="col-sm-3">
                                    <label for="l-name" class="sr-only">Last Name</label>
                                    <input type="text" maxlength="255" class="form-control" id="l-name" name="last-name" placeholder="Last Name"
                                           required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-sm-3 control-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" maxlength="255" class="form-control" id="email" name="email" placeholder="name@domain.com"
                                           required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="col-sm-3 control-label">Phone</label>
                                <div class="col-sm-9">
                                    <input type="tel" maxlength="255" class="form-control" id="phone" name="phone" placeholder="+234 xxx xxx xxxx"
                                           required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password" class="col-sm-3 control-label">Password</label>
                                <div class="col-sm-9">
                                    <input type="password" maxlength="255" class="form-control" id="password" name="password" required
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation" class="col-sm-3 control-label">Password Confirmation</label>
                                <div class="col-sm-9">
                                    <input type="password" maxlength="255" class="form-control" id="password_confirmation"
                                           name="password_confirmation" required autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="text-center padding-1em"><span id="notify"></span></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('extra_scripts')
    <script type="text/javascript" src="{{asset('js/app.list-manager.js')}}"></script>
    <script type="text/javascript">
      $(function () {
        var $newUserForm = $('#newUserForm');
        var NP = $('#notify', $newUserForm);
        $newUserForm.submit(function (e) {
          e.preventDefault();
          $('button[type=submit]', $newUserForm).attr('disabled', true);
          ajaxCall({
            url: $newUserForm.prop('action'),
            method: "POST",
            data: $newUserForm.serialize(),
            onSuccess: function (response) {
              notify(NP, response);
              if (response.status == true) {
                window.location.reload();
              }
            },
            onFailure: function (xhr) {
              handleHttpErrors(xhr, $newUserForm, '#notify');
            },
            onComplete: function () {
              $('button[type=submit]', $newUserForm).removeAttr('disabled');
            }
          });
        });
      });
    </script>
@endsection