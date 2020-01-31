<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UsersController extends Controller {


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
                        $users = Users::OrderBy("id_users", "DESC")->paginate(10)->toArray();
                    }
            }
        }

        $response = [
            'total_count' => $users['total'],
            'limit' => $users['per_page'],
            'pagination' => [
                'next_page' => $users['next_page_url'],
                'current_page' => $users['current_page']
            ],
            'data' => $users['data']
        ];

        return response()->json($response, 200);
    }


    public function show(Request $request, $id){
        $headerType = $request->header('Accept');

        $users = Users::find($id);

        if($headerType == 'application/xml'){
            $xml = new \SimpleXMLElement('<Users/>');
            $xmlItem = $xml->addChild('users');

            $xmlItem->addChild('id_produk', $users->id_produk);
            $xmlItem->addChild('nama_produk', $users->nama_produk);
            $xmlItem->addChild('jenis_produk', $users->jenis_produk);
            $xmlItem->addChild('harga_produk', $users->harga_produk);
            $xmlItem->addChild('stok_produk', $users->stok_produk);
            $xmlItem->addChild('id_supplier', $users->id_supplier);
            $xmlItem->addChild('created_at', $users->created_at);
            $xmlItem->addChild('updated_at', $users->updated_at);

            return $xml->asXML();
        }else if($headerType == 'application/json'){
            return response()->json($users, 200);
        }else{
            return response('Unsupported media', 415);
        }
    }

    public function update(Request $request, $id){

        $headerType = $request->header('Accept');
        $contentType = $request->header('Content-Type');

        $input = $request->all();
        $users = Users::find($id);


        if(Gate::denies('check-role')){
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized.'
            ], 403);
        }

        if(Auth::user()->level === 'admin'){
            $users->fill($input);
            $users->save();
        }else{
            return response()->json('Unauthorized levels.');
        }


        if($this->setHeader($headerType, $contentType) == 'json'){
            return response()->json($users, 200);
        }else if($this->setHeader($headerType, $contentType) == 'xml'){
            return $this->generateXML($users, 200);
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
            $users = Users::find($id);
            $users->delete();
        }
        $msg = ['message' => 'data has been successfully deleted', 'id_users' => $id];

        if($this->setHeader($headerType) == 'json'){
            return response()->json($msg, 200);
        }else if($this->setHeader($headerType) == 'xml'){
            return $this->generateXML($users);
        }else{
            return response('Not acceptable', 406);
        }
    }

}