@extends('layouts.admin.app')
@section('content')
    <div class="container">
        <div class="row">
        <div class="col-md-12 py-2 px-md-5 pb-3 d-flex justify-content-between ">
            <h3>Categories</h3>
            <button class="btn btn-danger" data-toggle="modal" data-target="#add_category">
                <i class="fa fa-plus"></i>
            </button>
        </div>
            <div class="col-md-12 overflow-auto">
                <table class="table" style="min-width: 400px">
                    <thead class="thead-light">
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nmae</th>
                        <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</th>
                            <td>{{ $category->name }}</td>
                            <td>
                                <a href="{{ route('admin.images', [$category->id]) }}" title="Show images" class="btn btn-sm btn-success">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-primary" onclick="category({{ $category->id }})" title="Edit Category" >
                                    <i class="fa fa-edit"></i>
                                </button>

                                <a href="{{ route('admin.del_cat', [$category->id]) }}" class="btn btn-sm btn-danger" title="Delete Category">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>




    <!--Add New  Modal -->
    <div class="modal fade" id="add_category"
    tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="">Add New Category</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form>
            <meta name="_token" content="{{ csrf_token() }}" />
            <div class="modal-body">
                <duv class="form-group">
                    <label for="">Category Title</label>
                    <input type="text" id="category__name" class="form-control">
                </duv>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" data-dismiss="modal">
                    <i class="fa fa-ban"></i> Cancel</button>
                <button id="create__category" class="btn btn-primary">
                    <i class="fa fa-save"></i> Save
                </button>
            </div>
        </form>
        </div>
    </div>
    </div>




    <!--Edit  Modal -->
    <div class="modal fade" id="edit_category" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Edit Category</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form>
            <div class="modal-body">
                <duv class="form-group">
                    <input type="hidden" id="ecategory_id" >
                    <label for="">Category Title</label>
                    <input id="ecat__name" type="text" class="form-control">
                </duv>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" data-dismiss="modal">
                    <i class="fa fa-ban"></i> Cancel</button>
                <button id="e_submit_cat" type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Save
                </button>
            </div>
        </form>
        </div>
    </div>
    </div>
@endsection

