@extends('layouts.admin.app')
@section('content')
    <div class="row">
        <div class="col-md-12 p-4 d-flex justify-content-end">
            <!-- <div class="text-white custom_card_1 py-2 py-md-3 mx-1">
                <h3 align="center" class="mb-0 pb-0">3010</h3>
                <h5 align="center">Users</h5>
            </div>
            <div class="text-white custom_card_2 py-2 py-md-3 mx-1">
                <h3 align="center" class="mb-0 pb-0">3010</h3>
                <h5 align="center">Screens</h5>
            </div>
            <div class="text-white custom_card_3 py-2 py-md-3 mx-1">
                <h3 align="center" class="mb-0 pb-0">3010</h3>
                <h5 align="center">Images</h5>
            </div> -->
            <div class="text-white custom_card_4  py-1">
                <h3 align="center" class="mb-0 pb-0">{{ $count }}</h3>
                <h5 align="center"> All Users</h5>
            </div>
        </div>
    </div>
    <hr/>
    <div class="row">
    <div class="col-md-12 pt-3 px-3">
            <h3> User matrix</h3>
        </div>
        <div class="col-md-12 overflow-auto">
            <table class="table" style="min-width: 700px">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nmae</th>
                        <th scope="col">Email</th>
                        <th scope="col">Gender</th>
                        <th scope="col">DOB</th>
                        <th scope="col">Country</th>
                        <th scope="col">Screens</th>
                        <th scope="col">Images</th>
                        <th scope="col">Logins</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                <tr>
                    <th scope="">1</th>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->gender }}</td>
                        <td>{{ $user->dob }}</</td>
                        <td>{{ $user->country }}</</td>
                        <td>3</td>
                        <td>30</td>
                        <td>10</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

