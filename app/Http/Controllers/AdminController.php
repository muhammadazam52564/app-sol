<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Category;
use App\Models\Image;
use App\Models\Screen;
use Http;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::where('role', '3')->get();
        $count = User::where('role', '3')->count();
        // foreach ($users as $user) {
        //     $categories = User::where('role', '3')->get();
        // }
        return view('admin.dashboard', compact('users', 'count'));
    }

    public function categories()
    {
        $categories = Category::where('user_id', '1')->get();
        return view('admin.categories', compact('categories'));
    }

    public function add_category(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status'    => false,
                'error'     => $validator->errors()->first()
            ]);
        }
        $cat = new Category;
        $cat->user_id = 1;
        $cat->name = $req->name;
        if($cat->save())
        {
            return response()->json([
                'status' => true,
                'msg' => 'Successfully Created'
            ]);
        }
    }

    public function category($id){
        $cat = Category::find($id);
        return response()->json([
            'status'    => true,
            'data'     => $cat
        ]);
    }

    public function ecategory(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status'    => false,
                'error'     => $validator->errors()->first()
            ]);
        }
        $cat =  Category::where('id', $req->id)->first();
        $cat->name = $req->name;
        if($cat->save())
        {
            return response()->json([
                'status' => true,
                'msg' => 'Successfully Created'
            ]);
        }
    }

    public function del_category($id){
        $cat = Category::find($id);
        $cat->delete();
        return back()->with(',essage', "successfully deleted");
    }

    public function images($id)
    {
        $images = Image::where('category_id', $id)->get();
        return view('admin.images', compact('images', 'id'));
    }

    public function add_image(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'image' => 'required'
        ]);
        if($validator->fails())
        {
            return response()->json([
                'status'    => false,
                'error'     => $validator->errors()->first()
            ]);
        }

        $img      = new image;
        $file     = $req->image;
        $fileName = time().$file->getClientOriginalExtension();
        $req->image->move(public_path('/uploadedimages'), $fileName);
        $img->image_address = 'uploadedimages/'.$fileName;
        $img->category_id   = $req->cat__id;
        if($img->save()){
            return response()->json([
                'status' => true,
                'msg' => 'Successfully Created'
            ]);
        }
    }

    public function del_img($id){
        $cat = Image::find($id);
        $cat->delete();
        return back()->with(',essage', "successfully deleted");
    }
    public function test2()
    {
        $response =  Http::get('https://maps.googleapis.com/maps/api/place/textsearch/json?query=restaurants%20in%20toronto%20canada&key=AIzaSyCbHPL4G1R7KqoGxpZAc2V5wK54cpDN6IY');
        $data = $response['results'];
        while (isset($response["next_page_token"]))
        {
            $next = $response['next_page_token'];
            $response =  Http::get('https://maps.googleapis.com/maps/api/place/textsearch/json?pagetoken='.$next.'&key=AIzaSyCbHPL4G1R7KqoGxpZAc2V5wK54cpDN6IY');

            $data = array_merge($data, $response['results']);
        }
        return $data;




        // $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json?place_id=&key=AIzaSyCbHPL4G1R7KqoGxpZAc2V5wK54cpDN6IY');

        // $response = Http::get('https://maps.googleapis.com/maps/api/place/photo?maxwidth=600&photo_reference=Aap_uEBMZR7QnRH3fMUp0kFGkSZY7kwg6Q-aQ8jrhytio3ssce2Dbp0rzgsSQc9rC0YxwR_QTeISUQ2WN1t5-3mT4GKLOmmijNv3LpVtlBUhK0HSctq3NySVLrP98iOif5dgAGC4XBb8LNI2gM15Z1rkTBHzfeTJa7jhAcw7Om_B02u1OkCu&key=AIzaSyCbHPL4G1R7KqoGxpZAc2V5wK54cpDN6IY');

    }



}




