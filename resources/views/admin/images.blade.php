@extends('layouts.admin.app')
@section('content')
<div class="container">
    <div class="row">
    <div class="col-md-12 px-5 py-2 pb-3 d-flex justify-content-between">
        <h3>Images</h3>
        <button class="btn btn-danger" data-toggle="modal" data-target="#add_image">
            <i class="fa fa-plus"></i>
        </button>
    </div>
        <div class="col-md-12 overflow-auto">
            <table class="table" style="min-width: 400px">
                <thead class="thead-light">
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Category</th>
                    <th scope="col">Image</th>
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($images as $image)
                    <tr>
                        <td> 1 </td>
                        <td>{{ $image->name }}</td>
                        <td>
                            <img src="../../public/{{ $image->image_address}}" width="160px" alt="" class="rounded">
                        </td>
                        <td>
                            <!-- <button class="btn btn-sm btn-primary" title="Edit Image" data-toggle="modal" data-target="#edit_image">
                                <i class="fa fa-edit"></i>
                            </button> -->

                            <a href="{{ route('admin.del_img', [$image->id]) }}" class="btn btn-sm btn-danger" onclick="category()" title="Delete Image">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    <tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>












<!--Add New  Modal -->
<div class="modal fade" id="add_image" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">New Image </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id='image__form'>
          <meta name="_token" content="{{ csrf_token() }}" />
          <div class="modal-body">
            <duv class="form-group">
                <label for="">Category Title</label>
                <input type="hidden" name="cat__id" value="{{ $id }}">
                <input type="file" name="image" id="image" class="form-control">
            </duv>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-danger" data-dismiss="modal">
                <i class="fa fa-ban"></i> Cancel</button>
            <button id="__image" type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Save
            </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!--Edit  Modal -->
<div class="modal fade" id="edit_image" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                <label for="">Category Title</label>
                <input type="text" class="form-control">
            </duv>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-danger" data-dismiss="modal">
                <i class="fa fa-ban"></i> Cancel</button>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Save
            </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

