<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index');
    }
    public function string()
    {
        return view('user.string');
    }
    public function city()
    {
        return view('user.city');
    }
    public function city_list()
    {
        $list = DB::table('city')->get();

        if (isset($list) && count($list) > 0) {
            return response()->json([
                "msg" => "Success",
                "status" => true,
                "data" => $list
            ]);
        }
    }

    public function city_save(Request $request)
    {

        $file = $request->file('city');

        // This would be used for the payload
        $file_path = $file->getPathName();

        if (!file_exists($file_path) || !is_readable($file_path)) {
            return false;
        }
        if (!($_FILES["city"]["type"] == "text/csv")) {
            echo "Please Upload csv file";
            return false;
        }

        $header = null;
        $data = array();
        if (($handle = fopen($file_path, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000)) !== false) {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        foreach ($data as $item) {
            $prev = DB::table('city')->where('name', $item['name'])->first();

            if (isset($prev)) {
                continue;
            }
            DB::table('city')->insert($item);
        }

        return redirect()->route('city');
    }
    public function my_file()
    {
        return view('user.my_files');
    }
    public function my_file_list()
    {
        $list = DB::table('my_files')->get();

        if (isset($list) && count($list) > 0) {
            return response()->json([
                "msg" => "Success",
                "status" => true,
                "data" => $list
            ]);
        }
    }

    public function my_file_save(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'my_file' => 'max:5120', //5MB 
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Max file size exceeded');
        }


        $image_name = $_FILES['my_file']['name'];
        $tmp_name = $_FILES['my_file']['tmp_name'];
        $o_size = $_FILES['my_file']['size'];

        $item = [
            "name" => $image_name,
            "original_size" => $this->formatSizeUnits($o_size)
        ];


        $directory_name = public_path('/uploads/');

        $file_name = $directory_name . $image_name;

        move_uploaded_file($tmp_name, $file_name);

        $compress_file = "compress_" . $image_name;
        $compressed_img = $directory_name . $compress_file;
        $compress_image = $this->compress($file_name, $compressed_img);
        unlink($file_name);

        move_uploaded_file($compress_image, $directory_name . $compressed_img);

        $save_size = filesize($compress_image);
        $item['compressed_size'] = $this->formatSizeUnits($save_size);
        $item['url'] = $compressed_img;

        DB::table('my_files')->insert($item);

        return redirect()->route('my_file');
    }


    public function getSection()
    {

        $data = array();

        $sections = DB::table('section')->get();
        if (isset($sections) && count($sections) > 0) {
            foreach ($sections as $s_key => $sec_item) {

                $ec_count = 0;
                $data[$s_key]['section_id'] = $sec_item->id;
                $data[$s_key]['section_name'] = $sec_item->section_name;
                $data[$s_key]['enclosure_count'] = 0;
                $data[$s_key]['animal_list'] = [];
                $enc = DB::table('enclosures')->where('section_id', $sec_item->id)->get();
                if (isset($enc) && count($enc) > 0) {
                    $animal_list = [];
                    foreach ($enc as $enc_item) {
                        $ec_count++;
                        $animals = DB::table('animals')->select('id as animal_id', 'name as animal_name')->where('enclosur_id', $enc_item->id)->get();
                        if (isset($animals) && count($animals) > 0) {
                            $data[$s_key]['animal_list'] = $animals;
                        }
                    }
                }
                $data[$s_key]['enclosure_count'] = $ec_count;
            }
        }

        return response()->json([
            "msg" => "success",
            "status" => true,
            "data" => $data
        ]);
    }

    function compress($source_image, $compress_image)
    {
        $image_info = getimagesize($source_image);
        if ($image_info['mime'] == 'image/jpeg') {
            $source_image = imagecreatefromjpeg($source_image);
            imagejpeg($source_image, $compress_image, 20);             //for jpeg or gif, it should be 0-100
        } elseif ($image_info['mime'] == 'image/png') {
            $source_image = imagecreatefrompng($source_image);
            imagepng($source_image, $compress_image, 3);
        }
        return $compress_image;
    }
    function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    function check_string(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'string' => [
                'required',
                'string',
                'min:5',
                'max:100',
                'regex:/^(?!.*[ \-\'\x{00A0}]{2})(?!^[ \-\'\x{00A0}])(?!.*[ \-\'\x{00A0}]$)[A-Za-z \-\'\x{00A0}]+$/u'
            ],
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', $validator->errors());
        }

        dd(["data" => $request->string, "msg" => "Success"]);
    }

    
}

