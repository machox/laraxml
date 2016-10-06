@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <ul  class="nav nav-pills">
                <li class="active"><a  href="#city" data-toggle="tab">By City</a></li>
                <li><a href="#department" data-toggle="tab">By Department</a></li>
            </ul>

            <div class="tab-content clearfix">
                <div class="tab-pane active" id="city">
                    <table id="myTable1" class="table table-striped table-bordered display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>City</th>
                                <th>Department</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="tab-pane" id="department">
                    <table id="myTable2" class="table table-striped table-bordered display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>City</th>
                                <th>Department</th>
                                <th></th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<div class="modal fade" id="detailEmployee" tabindex="-1" role="dialog" aria-labelledby="detailEmployeeLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content col-md-10">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="detailEmployeeLabel">Detail Employee</h4>
      </div>
      <div class="modal-body">
        <div id="detailContent">
        <div class="panel panel-borderless">
                <div class="panel-body">
                    <div class="box box-info">
                    <div class="box-body">
                        <div class="col-sm-6">
                            <div  align="center"> 
                            <img alt="User Pic" src="https://x1.xingassets.com/assets/frontend_minified/img/users/nobody_m.original.jpg" class="img-circle img-responsive" id="avatarE" width="100" height="100"> 
                            </div>
                            <br>
                          <!-- /input-group -->
                        </div>
                        <div class="col-sm-6">
                        <h4 style="color:#00b1b1;" id="nameE"></h4></span>    
                        <h5 style="color:#00b1b1;" id="emailE"></h5></span>    
                        </div>
                        <div class="clearfix"></div>
                        <hr style="margin:5px 0 5px 0;">

                        <div class="col-sm-5 col-xs-6 tital" >ID</div><div class="col-sm-7" id="idE"></div>
                        <div class="clearfix"></div>
                        <div class="bot-border"></div>

                        <div class="col-sm-5 col-xs-6 tital" >Position</div><div class="col-sm-7" id="positionE"></div>
                        <div class="clearfix"></div>
                        <div class="bot-border"></div>

                        <div class="col-sm-5 col-xs-6 tital" >Department</div><div class="col-sm-7" id="departmentE"></div>
                        <div class="clearfix"></div>
                        <div class="bot-border"></div>

                        <div class="col-sm-5 col-xs-6 tital " >City</div><div class="col-sm-7" id="cityE"></div>
                        <div class="clearfix"></div>
                        <div class="bot-border"></div>

                        <div class="col-sm-5 col-xs-6 tital " >Status</div><div class="col-sm-7" id="statusE"></div>
                    <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                    </div>    
                </div> 
            </div>
        </div>
        <div class="js-loading text-center">
            <h3>Loading...</h3>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" class="close" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="statusEmployee" tabindex="-1" role="dialog" aria-labelledby="statusEmployeeLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content col-md-10">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="statusEmployeeLabel">Change Employee Status</h4>
      </div>
      <form class="form-horizontal" id="formStatus">
      <div class="modal-body">
            <div class="alert alert-success" id="message-success" style="display: none;">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Success !</strong>
            </div>
            <div class="alert alert-info" id="message-info" style="display: none;">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Proccessing, please wait...</strong>
            </div>
            <div class="alert alert-danger" id="message-error" style="display: none;">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong id="error-m">Error !</strong>
            </div>
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Status</label>
                <div class="col-sm-10">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="HiddenId" id="HiddenId">
                    <select class="form-control" name="statusName" id="statusId">
                      <option value="1">Masuk</option>
                      <option value="2">Cuti</option>
                      <option value="3">Libur</option>
                    </select>
                </div>
              </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success" id="saveStatus" name="saveStatus">Save</button>
        <button type="button" class="btn btn-primary" class="close" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
</div>
