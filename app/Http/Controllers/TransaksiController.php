<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TransaksiController extends Controller {

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
                    if(Auth::user()->level === 'admin' || Auth::user()->level === 'pegawai'){
                        $transaksi = Transaksi::OrderBy("id_transaksi", "DESC")->paginate(10)->toArray();
                        $response = [
                            'total_count' => $transaksi['total'],
                            'limit' => $transaksi['per_page'],
                            'pagination' => [
                                'next_page' => $transaksi['next_page_url'],
                                'current_page' => $transaksi['current_page']
                            ],
                            'data' => $transaksi['data']
                        ];

                        return response()->json($response, 200);
                    }else{
                        return response()->json([
                            'success' => false,
                            'status' => 403,
                            'message' => 'You are unauthorized.'
                        ], 403);
                    }
            }
        }
    }


    public function show(Request $request, $id){
        $headerType = $request->header('Accept');

        $transaksi = Transaksi::find($id);

        if($headerType == 'application/xml'){
            $xml = new \SimpleXMLElement('<transaksi/>');
            $xmlItem = $xml->addChild('transaksi');

            $xmlItem->addChild('id_transaksi', $transaksi->id_transaksi);
            $xmlItem->addChild('id_produk', $transaksi->id_produk);
            $xmlItem->addChild('id_user', $transaksi->id_user);
            $xmlItem->addChild('jml_transaksi', $transaksi->jml_transaksi);
            $xmlItem->addChild('total_harga', $transaksi->total_harga);
            $xmlItem->addChild('created_at', $transaksi->created_at);
            $xmlItem->addChild('updated_at', $transaksi->updated_at);

            return $xml->asXML();
        }else if($headerType == 'application/json'){
            return response()->json($transaksi, 200);
        }else{
            return response('Unsupported media', 415);
        }
    }

    public function update(Request $request, $id){

        $headerType = $request->header('Accept');
        $contentType = $request->header('Content-Type');

        $input = $request->all();
        $transaksi = Transaksi::find($id);


        if(Gate::denies('check-role')){
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized.'
            ], 403);
        }

        if(Auth::user()->level === 'admin' || Auth::user()->level === 'pegawai'){
            $transaksi->fill($input);
            $transaksi->save();
            if($headerType == 'application/json' || $headerType == 'application/xml'){
                if($contentTypeHeader == 'application/json'){
                    return response()->json($transaksi, 200);
                }else if($contentTypeHeader == 'application/xml'){
                    $xml = new \SimpleXMLElement('<transaksi/>');
                    $xmlItem = $xml->addChild('transaksi');

                    $xmlItem->addChild('id_transaksi', $transaksi->id_transaksi);
                    $xmlItem->addChild('id_produk', $transaksi->id_produk);
                    $xmlItem->addChild('id_user', $transaksi->id_user);
                    $xmlItem->addChild('jml_transaksi', $transaksi->jml_transaksi);
                    $xmlItem->addChild('total_harga', $transaksi->total_harga);
                    $xmlItem->addChild('created_at', $transaksi->created_at);
                    $xmlItem->addChild('updated_at', $transaksi->updated_at);

                    return $xml->asXML();
                }else{
                    return response('Not acceptable', 406);
                }
             }
        }else{
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized.'
            ], 403);
        }
    }

    public function store(Request $request)
    {

        $headerType = $request->header('Accept');
        $contentTypeHeader = $request->header('Content-Type');

        $input = $request->all();
        $validationRules = [
            'id_produk' => 'required',
            'id_user' => 'required',
            'jml_transaksi' => 'required',
            'total_harga' => 'required'
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

        if(Auth::user()->level === 'admin' || Auth::user()->level === 'pegawai'){
            $transaksi = Transaksi::create($input);
            if($headerType == 'application/json' || $headerType == 'application/xml'){
                if($contentTypeHeader == 'application/json'){
                    return response()->json($transaksi, 200);
                }else if($contentTypeHeader == 'application/xml'){
                    $xml = new \SimpleXMLElement('<transaksi/>');
                    $xmlItem = $xml->addChild('transaksi');

                    $xmlItem->addChild('id_transaksi', $transaksi->id_transaksi);
                    $xmlItem->addChild('id_produk', $transaksi->id_produk);
                    $xmlItem->addChild('id_user', $transaksi->id_user);
                    $xmlItem->addChild('jml_transaksi', $transaksi->jml_transaksi);
                    $xmlItem->addChild('total_harga', $transaksi->total_harga);
                    $xmlItem->addChild('created_at', $transaksi->created_at);
                    $xmlItem->addChild('updated_at', $transaksi->updated_at);

                    return $xml->asXML();
                }else{
                    return response('Not acceptable', 406);
                }
        }else{
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized.'
            ], 403);
        }


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

        if(Auth::user()->level === 'admin' || Auth::user()->level === 'pegawai'){
            $transaksi = Transaksi::find($id);
            $transaksi->delete();
            $msg = ['message' => 'data has been successfully deleted', 'id_transaksi' => $id];
            if($headerType == 'application/json'){
                return response()->json($msg, 200);
            }else if($headerType == 'application/xml'){
                $xml = new \SimpleXMLElement('<transaksi/>');
                    $xmlItem = $xml->addChild('transaksi');

                    $xmlItem->addChild('id_transaksi', $transaksi->id_transaksi);
                    $xmlItem->addChild('id_produk', $transaksi->id_produk);
                    $xmlItem->addChild('id_user', $transaksi->id_user);
                    $xmlItem->addChild('jml_transaksi', $transaksi->jml_transaksi);
                    $xmlItem->addChild('total_harga', $transaksi->total_harga);
                    $xmlItem->addChild('created_at', $transaksi->created_at);
                    $xmlItem->addChild('updated_at', $transaksi->updated_at);

                    return $xml->asXML();
            }else{
                return response('Not acceptable', 406);
            }
        }else{
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized.'
            ], 403);
        }
    }

}