<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProdukController extends Controller {

    public function setHeader($headerType, $contentTypeHeader = null){
        $type = "";
        if($headerType == 'application/json' || $headerType == 'application/xml'){
            if($contentTypeHeader == 'application/json'){
                $type = 'json';
            }else if($contentTypeHeader == 'application/xml'){
                $type = 'xml';
            }else if($contentTypeHeader == null){
                return true;
            }
            return $type;
        }else{
            return false;
        }
    }

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
                        $product = Produk::OrderBy("id_produk", "DESC")->paginate(10)->toArray();
                    }
            }
        }

        $response = [
            'total_count' => $product['total'],
            'limit' => $product['per_page'],
            'pagination' => [
                'next_page' => $product['next_page_url'],
                'current_page' => $product['current_page']
            ],
            'data' => $product['data']
        ];

        return response()->json($response, 200);
    }



    public function store(Request $request)
    {

        $headerType = $request->header('Accept');
        $contentTypeHeader = $request->header('Content-Type');

        $input = $request->all();
        $validationRules = [
            'nama_produk' => 'required|min:5',
            'jenis_produk' => 'required|in:minuman,makanan',
            'harga_produk' => 'required',
            'stok_produk' => 'required',
            'id_supplier' => 'required'
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
            $product = Produk::create($input);
        }

        if($this->setHeader($headerType, $contentTypeHeader) === 'json'){
            return response()->json($product, 200);
        }else if($this->setHeader($headerType, $contentTypeHeader) === 'xml'){
            $xml = new \SimpleXMLElement('<Produk/>');
            $xmlItem->addChild('id_produk', $product->id_produk);
            $xmlItem->addChild('nama_produk', $product->nama_produk);
            $xmlItem->addChild('jenis_produk', $product->jenis_produk);
            $xmlItem->addChild('harga_produk', $product->harga_produk);
            $xmlItem->addChild('stok_produk', $product->stok_produk);
            $xmlItem->addChild('id_supplier', $product->id_supplier);
            $xmlItem->addChild('created_at', $product->created_at);
            $xmlItem->addChild('updated_at', $product->updated_at);

            return $xml->asXML();
        }else{
            return response('Not acceptable', 406);
        }
    }

    public function show(Request $request, $id)
    {
        $headerType = $request->header('Accept');

        $product = Produk::find($id);

        if($headerType == 'application/xml'){
            $xml = new \SimpleXMLElement('<Produk/>');
            $xmlItem = $xml->addChild('produk');

            $xmlItem->addChild('id_produk', $product->id_produk);
            $xmlItem->addChild('nama_produk', $product->nama_produk);
            $xmlItem->addChild('jenis_produk', $product->jenis_produk);
            $xmlItem->addChild('harga_produk', $product->harga_produk);
            $xmlItem->addChild('stok_produk', $product->stok_produk);
            $xmlItem->addChild('id_supplier', $product->id_supplier);
            $xmlItem->addChild('created_at', $product->created_at);
            $xmlItem->addChild('updated_at', $product->updated_at);

            return $xml->asXML();
        }else if($headerType == 'application/json'){
            return response()->json($product, 200);
        }else{
            return response('Unsupported media', 415);
        }
    }

    public function update(Request $request, $id){


        $headerType = $request->header('Accept');
        $contentType = $request->header('Content-Type');

        $input = $request->all();
        $product = Produk::find($id);

        if(!$product){
            abort(404);
        }


        if(Gate::denies('check-role')){
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized.'
            ], 403);
        }

        if(Auth::user()->level === 'admin'){
            $product->fill($input);
            $product->save();
        }else{
            return response()->json('Unauthorized levels.');
        }

        if($this->setHeader($headerType, $contentType) == 'json'){
            return response()->json($product, 200);
        }else if($this->setHeader($headerType, $contentType) == 'xml'){
            return $this->generateXML($product, 200);
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
            $product = Produk::find($id);
            $product->delete();
        }
        $msg = ['message' => 'data has been successfully deleted', 'produk_id' => $id];

        if($this->setHeader($headerType) == 'json'){
            return response()->json($msg, 200);
        }else if($this->setHeader($headerType) == 'xml'){
            return $this->generateXML($product);
        }else{
            return response('Not acceptable', 406);
        }
    }

}