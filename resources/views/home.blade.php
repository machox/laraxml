@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel with-nav-tabs panel-primary">
                <div class="panel-heading">
                        <ul class="nav nav-tabs"  role="tablist">
                            <li><a href="#departement" class="button-tab" id="ByDepartement" role="tab" data-toggle="tab">By Departement</a></li>
                            <li class="active"><a href="#city" class="button-tab" id="ByCity" role="tab" data-toggle="tab">By City</a></li>
                        </ul>
                </div>
                <div class="panel-body">
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="city">
                            <table class="table table-striped" id="employees">
                            <thead>
                              <tr>
                                <th class="no-sort">Name</th>
                                <th class="no-sort">City</th>
                                <th class="no-sort">Departement</th>
                                <th class="no-sort"></th>
                              </tr>
                            </thead>
                            <tbody>
                            @foreach ($employees as $employee)
                                  <tr>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $employee->city }}</td>
                                    <td>{{ $employee->department }}</td>
                                    <td>
                                        @if ($employee->status == 1)
                                            <span class="label label-success">Masuk</span>
                                        @elseif ($employee->status == 2)
                                            <span class="label label-danger">Cuti</span>
                                        @else
                                            <span class="label label-default">Libur</span>
                                        @endif
                                    </td>
                                  </tr>
                            @endforeach
                            </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="departement">Primary 2</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
