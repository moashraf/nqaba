<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class imagesController extends Controller
{
    public function uploadImage(Request $request){
        $file = $request->file('image');
        $imageName = time().$file->getClientOriginalName();
        $moving = $file->move(public_path().'/images',$imageName);

        if (! $moving){
            return response()->json([
                'status' => false
            ],402);
        }else{
            return response()->json([
                'status' => true,
                'message' => 'image uploaded successfully ..',
                'image' => $imageName
            ],200);
        }

    }



    public function uploadImageAndroid(Request $request){
        // Path to move uploaded files
        $target_path = "images/";

        // array for final json respone
                $response = array();

        // getting server ip address
                $server_ip = gethostbyname(gethostname());

        // final file url that is being uploaded
                $file_upload_url = 'http://' . $server_ip . '/' . 'AndroidFileUpload' . '/' . $target_path;


        if (isset($_FILES['image']['name'])) {
            $target_path = $target_path . time() . basename($_FILES['image']['name']);

            // reading other post parameters
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $website = isset($_POST['website']) ? $_POST['website'] : '';

            $response['file_name'] = basename($_FILES['image']['name']);
            $response['email'] = $email;
            $response['website'] = $website;

            try {
                // Throws exception incase file is not being moved
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    // make error flag true
                    $response['error'] = true;
                    $response['message'] = 'Could not move the file!';
                }

                // File successfully uploaded
                $response['message'] = 'File uploaded successfully!';
                $response['error'] = false;
                $response['file_path'] = $file_upload_url . $target_path;
            } catch (Exception $e) {
                // Exception occurred. Make error flag true
                $response['error'] = true;
                $response['message'] = $e->getMessage();
            }
        } else {
            // File parameter is missing
            $response['error'] = true;
            $response['message'] = 'Not received any file!F';
        }

// Echo final json response to client
        return response()->json([
            'status' => true,
            'message' => 'uploaded successfully ..',
            'image' => $target_path
        ]);
    }
}
