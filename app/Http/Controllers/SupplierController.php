<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SupplierController extends Controller {


    public function index(Request $request){
        if(Gate::denies('check-role')){
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized.'
            ], 403);
        }
        $acceptHeader = $request->header('Accept');

            // validasi: hanya application/json atau application/xml yang valid
            if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
                if ($acceptHeader === 'application/json') {
                    if(Auth::user()->level === 'admin'){
                        $supplier = Supplier::OrderBy("id_supplier", "DESC")->paginate(10)->toArray();
                    }
            }
        }

        $response = [
            'total_count' => $supplier['total'],
            'limit' => $supplier['per_page'],
            'pagination' => [
                'next_page' => $supplier['next_page_url'],
                'current_page' => $supplier['current_page']
            ],
            'data' => $supplier['data']
        ];

        return response()->json($response, 200);
    }

    public function store(Request $request)
    {

        $headerType = $request->header('Accept');
        // $contentTypeHeader = $request->header('Content-Type');

        $input = $request->all();
        $validationRules = [
            'nama_supplier' => 'required|min:6'
        ];

        $validator = \Validator::make($input, $validationRules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        if(Gate::denies('check-role')){
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized.'
            ], 403);
        }

        if(Auth::user()->level === 'admin'){
            $supplier = Supplier::create($input);
        }

        if($headerType == 'application/json'){
            return response()->json($supplier, 200);
        }else if($headerType == 'application/xml'){
            $xml = new \SimpleXMLElement('<Suppliers/>');
            $xmlItem = $xml->addChild('supplier');

            $xmlItem->addChild('id_supplier', $supplier->id_supplier);
            $xmlItem->addChild('nama_supplier', $supplier->nama_supplier);
            $xmlItem->addChild('created_at', $supplier->created_at);
            $xmlItem->addChild('updated_at', $supplier->updated_at);
        }else{
            return response('Not acceptable', 406);
        }
    }


    public function show(Request $request, $id){
        $headerType = $request->header('Accept');

        $supplier = Supplier::find($id);

        if($headerType == 'application/xml'){
            $xml = new \SimpleXMLElement('<Suppliers/>');
            $xmlItem = $xml->addChild('supplier');

            $xmlItem->addChild('id_supplier', $supplier->id_supplier);
            $xmlItem->addChild('nama_supplier', $supplier->nama_supplier);
            $xmlItem->addChild('created_at', $supplier->created_at);
            $xmlItem->addChild('updated_at', $supplier->updated_at);

            return $xml->asXML();
        }else if($headerType == 'application/json'){
            return response()->json($supplier, 200);
        }else{
            return response('Unsupported media', 415);
        }
    }

    public function update(Request $request, $id){

        $headerType = $request->header('Accept');
        $contentType = $request->header('Content-Type');

        $input = $request->all();
        $supplier = Supplier::find($id);


        if(Gate::denies('check-role')){
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized.'
            ], 403);
        }

        if(Auth::user()->level === 'admin'){
            $supplier->fill($input);
            $supplier->save();
        }else{
            return response()->json('Unauthorized levels.');
        }


        if($headerType == 'application/json'){
            return response()->json($supplier, 200);
        }else if($headerType == 'application/xml'){
            $xml = new \SimpleXMLElement('<Suppliers/>');
            $xmlItem = $xml->addChild('supplier');

            $xmlItem->addChild('id_supplier', $supplier->id_supplier);
            $xmlItem->addChild('nama_supplier', $supplier->nama_supplier);
            $xmlItem->addChild('created_at', $supplier->created_at);
            $xmlItem->addChild('updated_at', $supplier->updated_at);

            return $xml->asXML();
        }else{
            return response('Not acceptable', 406);
        }
    }

    public function destroy(Request $request, $id){

        $headerType = $request->header('Accept');


        if(Gate::denies('check-role')){
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized.'
            ], 403);
        }

        if(Auth::user()->level === 'admin'){
            $supplier = Supplier::find($id);
            $supplier->delete();
        }
        $msg = ['message' => 'data has been successfully deleted', 'id_supplier' => $id];

        if($headerType == 'application/json'){
            return response()->json($msg, 200);
        }else if($headerType == 'xml'){
            $xml = new \SimpleXMLElement('<Suppliers/>');
            $xmlItem = $xml->addChild('supplier');

            $xmlItem->addChild('id_supplier', $supplier->id_supplier);
            $xmlItem->addChild('nama_supplier', $supplier->nama_supplier);
            $xmlItem->addChild('created_at', $supplier->created_at);
            $xmlItem->addChild('updated_at', $supplier->updated_at);

            return $xml->asXML();
        }else{
            return response('Not acceptable', 406);
        }
    }

}