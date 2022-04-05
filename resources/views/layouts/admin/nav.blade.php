<div id="menu_area">
    <img src="{{ asset('images/logo.png') }}" width="150" />
    <div class="" style="margin-top: 10px;">
    <ul class="w-100">
        <li class="w-100" id="dashboard">
            <a href="{{ route('admin.dashboard') }}" class="" >
                <i class="fa fa-home"></i>Home
            </a>
        </li>
        <li id="categories">
            <a href="{{ route('admin.categories') }}">
                <i class="fa fa-list-alt " ></i>
                Categories
            </a>
        </li>
    </ul>
    </div>
</div>
<div id="spacing"></div>
<div id="logout_area">
    <label id="label" for="logout" >
        <i class="fa fa-sign-out-alt" ></i>Logout
    </label>
</div>



<form method="post" action="{{ route('logout')}}" class="d-none">
    @csrf
    <input  type="submit" id="logout">
</form>


