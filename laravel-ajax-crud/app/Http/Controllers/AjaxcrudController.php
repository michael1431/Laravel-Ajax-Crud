<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Ajaxcrud;
use Validator;


class AjaxcrudController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //receive ajax request
        if(request()->ajax())
        {
            return datatables()->of(Ajaxcrud::latest()->get())
                    // define action column
                    ->addColumn('action', function($data){
                        $button = '<button type="button" name="edit" id="'.$data->id.'" class="edit btn btn-primary btn-sm">Edit</button>';
                        $button .= '&nbsp;&nbsp;';
                        $button .= '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-danger btn-sm">Delete</button>';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
       }
        return view('ajax_index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // server side validation
        $rules = array(
            'first_name'    =>  'required',
            'last_name'     =>  'required',
            'image'         =>  'required|image|max:2048'
        );
        // store all the errors in this variable
        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            // msg pass from server to browser
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $image = $request->file('image');

        $new_name = rand() . '.' . $image->getClientOriginalExtension();
        // store the image in image folder
        $image->move(public_path('images'), $new_name);

        $form_data = array(
            'first_name'        =>  $request->first_name,
            'last_name'         =>  $request->last_name,
            'image'             =>  $new_name
        );
        // data store into db
        Ajaxcrud::create($form_data);
        // msg pass from server to browser
        return response()->json(['success' => 'Data Added successfully.']);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        if(request()->ajax())
        {
            $data = Ajaxcrud::findOrFail($id);
            return response()->json(['data' => $data]);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $image_name = $request->hidden_image;
        $image = $request->file('image');
        if($image != '')
        {
            //if user select image from the file then this code will be executed
            $rules = array(
                'first_name'    =>  'required',
                'last_name'     =>  'required',
                'image'         =>  'image|max:2048'
            );
            $error = Validator::make($request->all(), $rules);
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $image_name = rand() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $image_name);
        }
        else
        {
    //if user donot any select image from the file then this code will be executed
            $rules = array(
                'first_name'    =>  'required',
                'last_name'     =>  'required'
            );

            $error = Validator::make($request->all(), $rules);

            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }
        }

        $form_data = array(
            'first_name'       =>   $request->first_name,
            'last_name'        =>   $request->last_name,
            'image'            =>   $image_name
        );
        Ajaxcrud::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $data = Ajaxcrud::findOrFail($id);
        $data->delete();

    }
}
