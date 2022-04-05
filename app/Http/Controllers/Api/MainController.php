<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\follow;
use App\Models\Video;
use App\Models\User;
use App\Models\Like;

class MainController extends Controller{
// admin 0
// recruiters 1
// player 2
    public function follow(Request $request){
        try{
            $validator = \Validator::make($request->all(), [
                'follow_by' => 'bail|required',
                'follow_to' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      => 0,
                ], 400);
            }else{
                $follow = new Follow;
                $follow->follow_by = $request->follow_by;
                $follow->follow_to = $request->follow_to;
                if ($follow->save()) {
                    return response()->json([
                        'status'    => true,
                        'message'   => 'Followed Successfully',
                        'data'      => 0,
                    ], 200);
                }
            }
        }catch(\Exception $e){

            return response()->json([
                'status'    => false,
                'error'   => $e->getMessage(),
                'data'      =>  0,
            ], 400);
        }
    }

    public function unfollow(Request $request){
        try{
            $validator = \Validator::make($request->all(), [
                'follow_by' => 'bail|required',
                'follow_to' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      => 0,
                ], 400);
            }else{
                $unfollow = Follow::where('follow_by', $request->follow_by)->where('follow_to', $request->follow_to)->delete();
                if ($unfollow) {
                    return response()->json([
                        'status'    => true,
                        'message'     => 'Unfollowed Successfully',
                        'data'      => 0,
                    ], 200);
                }
            }
        } catch(\Exception $e){

            return response()->json([
                'status'    => false,
                'message'   => 'There is some trouble to proceed your action!',
                'data'      =>  0,
            ], 400);
        }
    }

    public function allplayers(Request $request){
        try{
            $user = User::where('type', '2')->get();
            return response()->json([
                'status'    => true,
                'message'   => 'all players',
                'data'      => $user->makeHidden(['status','type','coach_type', 'email_verified_at', 'updated_at', 'created_at', 'otp', 'token']),
            ], 200);

        } catch(\Exception $e){

            return response()->json([
                'status'    => false,
                'error'     => $e->getMessage(),
                'data'      =>  0,
            ], 400);
        }
    }

    public function playergender(Request $request){
        try{
            $user = User::where('type', '2')->where('gender', $request->gender)->get();
            return response()->json([
                'status'    => true,
                'message'   => $request->gender.' players List',
                'data'      => $user->makeHidden(['status','type','coach_type', 'email_verified_at', 'updated_at', 'created_at', 'otp', 'token']),
            ], 200);
        } catch(\Exception $e){

            return response()->json([
                'status'    => false,
                'error'   => $e->getMessage(),
                'data'      =>  0,
            ], 400);
        }
    }

    public function latestplayer(Request $request){
        try{

            $user = User::where('type', '2')->where('gender', $request->gender)->limit(3)->orderby('id', 'DESC')->get();
            return response()->json([
                'status'    => true,
                'Message'   =>'Latest '. $request->gender .' Players List',
                'data'      => $user->makeHidden(['status','type','coach_type', 'email_verified_at', 'updated_at', 'created_at', 'otp', 'token']),
            ], 200);

        } catch(\Exception $e){

            return response()->json([
                'status'    => false,
                'error'     => $e->getMessage(),
                'data'      =>  0,
            ], 400);
        }
    }

    public function player_position(Request $request){
        try{
            if( $request->has('gender')){
                $users = User::where('type', '2')->where('gender', $request->gender)->whereIn('position', $request->position)->get();
            }else{
                $users = User::where('type', '2')->whereIn('position', $request->position)->get();
            }
            return response()->json([
                'status'    => true,
                'error'     => 'player list with position',
                'data'      => $users->makeHidden(['status','type','coach_type', 'email_verified_at', 'updated_at', 'created_at', 'otp', 'token']),
            ], 200);
        } catch(\Exception $e){

            return response()->json([
                'status'    => false,
                'message'   => 'There is some trouble to proceed your action!',
                'data'      =>  0,
            ], 400);
        }
    }

    public function player_country(Request $request){
        try{
            // return $request->country;
            $users = User::where('type', '2')->where('country', $request->country)->get();
            return response()->json([
                'status'    => true,
                'message'   => 'Users list',
                'data'      => $users->makeHidden(['verified_at', 'updated_at', 'created_at', 'otp', 'token']),
            ], 200);
        } catch(\Exception $e){

            return response()->json([
                'status'    => false,
                'error'   => $e->getMessage(),
                'data'      =>  0,
            ], 400);
        }
    }

    public function singleplayer(Request $request){
        try{
            $user = User::find($request->id);
            $followers = follow::where('follow_to', $request->user_id)->count();
                if($followers  ==  0){
                    $user->followers =  0;
                }else{
                    $user->followers =  $followers;
                }
                $following = follow::where('follow_by', $request->user_id)->count();
                if($followers  ==  0) {
                    $user->following =  0;
                }else{
                    $user->following =  $following;
                }
                return response()->json([
                    'status'    => true,
                    'message'     => 'User Details',
                    'user'      => $user->makeHidden(['status','type','coach_type', 'email_verified_at', 'updated_at', 'created_at', 'otp', 'token']),
                ], 200);
        } catch(\Exception $e){

            return response()->json([
                'status'    => false,
                'error'   => $e->getMessage(),
                'user'      =>  0,
            ], 400);
        }
    }

    public function upload_video(Request $request){
        try{
            $validator = \Validator::make($request->all(), [
                'user_id'   => 'bail|required',
                'file'     => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      => 0,
                ], 400);
            }else{
                $user = User::find($request->user_id);
                if(empty($user)){
                    return response()->json([
                        'status' => false,
                        'error' => 'User Does Not Exists!',
                        'data' =>  0,
                    ], 200);
                }else{
                    $base64_file = $request->file;
                    if (preg_match('/^data:video\/(\w+);base64,/', $base64_file)){
                        $data = substr($base64_file, strpos($base64_file, ',') + 1);
                        $data = base64_decode($data);
                        $img = preg_replace('/^data:video\/\w+;base64,/', '', $base64_file);
                        $type = explode(';', $base64_file)[0];
                        $type = explode('/', $type)[1]; //
                        if($type == 'mp4' || $type == 'MP4' || $type == 'mov' || $type == 'MOV' || $type == 'wmv' || $type == 'WMV' || $type == 'flv' || $type == 'FLV' || $type == 'avi' || $type == 'AVI' || $type == 'mkv' || $type == 'MKV'){
                            $videoName = Str::random(10).'.'.$type;
                            \Storage::disk('post_videos')->put($videoName, $data);
                            // this disk is defined in config/filesystems.php under Disks section
                            $video_path = 'post_videos/'.$videoName;
                        }else{
                            return response()->json([
                                'status' => false,
                                'error' => 'Please Choose a Valid Video!',
                                'data' =>  0,
                            ], 400);
                        }
                    }else{
                        return 'Video type invalid';
                    }
                    $video = new Video;
                    $video->url = $video_path;
                    $video->user_id  = $request->user_id;
                    if($video->save()){
                        return response()->json([
                            'status' => true,
                            'message' => 'Video Successfully Uploaded !',
                            'data' => $video->makeHidden(['created_at', 'updated_at']),
                        ], 200);
                    }
                }
            }
        }catch(\Exception $e)
        {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' =>  0,
            ], 400);
        }
    }

    public function my_video(Request $request){
        try{
            $videos = Video::where('user_id', $request->id)->get();
            return response()->json([
                'status'    => true,
                'message'     => 'User Own Videos',
                'data'      => $videos->makeHidden(['verified_at', 'updated_at', 'created_at']),
            ], 200);
        } catch(\Exception $e){

            return response()->json([
                'status'    => false,
                'message'   => 'There is some trouble to proceed your action!',
                'data'      =>  0,
            ], 400);
        }
    }

    public function follow_video(Request $request){
        try
        {
            // $feed = array();
            // $followed = array();
            $people_follow_by_me = follow::where('follow_by', $request->id)->pluck('follow_to');
            // return $people_follow_by_me;

            $videos = Video::whereIn('user_id', $people_follow_by_me)->orderBy('id', 'DESC')->get();
            return $videos;


            // foreach ($videos as $video)
            // {
            //     $video->user  = User::where('id', $video->user_id)->first();
            // }

            // return $videos;
            // return response()->json([
            //     'status'    => true,
            //     'message'   => 'Users list',
            //     'data'      => $videos,
            // ], 200);
        }catch(\Exception $e)
        {
            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage(),
                'data'      =>  0,
            ], 400);
        }
    }

    public function like(Request $request){
        try{
            $validator = \Validator::make($request->all(), [
                'video_id' => 'bail|required',
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      => 0,
                ], 400);
            }else{
                $like = new Like;
                $like->video_id         = $request->video_id;
                $like->user_id          = $request->user_id;
                if ($like->save()) {
                    return response()->json([
                        'status'    => true,
                        'message'     => 'Liked Successfully',
                        'data'      => 0,
                    ], 200);
                }
            }
        } catch(\Exception $e){

            return response()->json([
                'status'    => false,
                'error'   => $e->getMessage(),
                'data'      =>  0,
            ], 400);
        }
    }

    public function dislike(Request $request){
        try{
            $validator = \Validator::make($request->all(), [
                'video_id' => 'bail|required',
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      => 0,
                ], 400);
            }else{
                $dislike = Like::where('video_id', $request->video_id)->where('user_id', $request->user_id)->delete();
                if ($dislike) {
                    return response()->json([
                        'status'    => true,
                        'Message'     => 'Like Removed Successfully',
                        'data'      => 0,
                    ], 200);
                }
            }
        } catch(\Exception $e){

            return response()->json([
                'status'    => false,
                'error'   => $e->getMessage(),
                'data'      =>  0,
            ], 400);
        }
    }


    public function recruiters(Request $request){
        try{
            $users = User::where('type', '1')->get();
            return response()->json([
                'status'    => true,
                'message'     => 'User Details',
                'data'      => $users->makeHidden(['verified_at', 'updated_at', 'created_at']),
            ], 200);

        } catch(\Exception $e){

            return response()->json([
                'status'    => false,
                'error'   => $e->getMessage(),
                'data'      =>  0,
            ], 400);
        }
    }
}


