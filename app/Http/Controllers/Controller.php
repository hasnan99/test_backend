<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Request;

class Controller extends BaseController{
    use AuthorizesRequests, ValidatesRequests;

    //1
    public function api_login(Request $request){
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('username', 'password');
        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            $response = [
                'user' => $user,
                'token' => $token,
            ];
            return response()->json($response, 200);
        }
        return response()->json(['message' => 'username atau password salah'], 400);
    }

    //2
    public function api_register(Request $request){
        $rules=[
            'username'=>'required|string',
            'email'=>'required|string|unique:users',
            'password'=>'required|string',
        ];
        $validator=Validator::make($request->all(),$rules);
        if ($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $user=User::create([
            'username'=>$request->username,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
        ]);
        $token=$user->createToken('Personal accsess token')->plainTextToken;
        $response=['user'=>$user, 'token'=>$token];
        return response()->json($response,200);
    }
    
    //3
    public function create_checklist(Request $request){
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        $checklist = Checklist::create([
            'user_id' => auth()->id(),
            'name' => $validatedData['title'],
        ]);
    
        return response()->json($checklist, 200);
    }

    //4
    public function delete_checklist($id){
        $data = Checklist::where('id', $id)->where('user_id', auth()->id())->first();
        $data->delete();
        return response()->json(['message' => 'Checklist berhasil sihapus']);
    }

    //5
    public function show_checklist(){
        $data = Checklist::where('user_id', auth()->id())->get();
        return response()->json($data);
    }

    //6
    public function detail_checklist($id){
        $data_checklist = Checklist::with('items')->where('id', $id)->where('user_id', auth()->id())->first();
        if (!$data_checklist) {
            return response()->json(['error' => 'data tidak ada'], 400);
        }
        return response()->json($data_checklist);
    }

    //7
    public function create_item(Request $request,$id){
        $checklist_item = $request->validate([
            'ItemName' => 'required|string|max:255',
        ]);
    
        $checklist_data = Checklist::where('id', $id)->where('user_id', auth()->id())->first();
        
        if (!$checklist_data) {
            return response()->json(['error' => 'Data tidak ditemukan'], 400);
        }
    
        $item = $checklist_data->items()->create([
            'ItemName' => $checklist_item['ItemName'],
            'is_completed' => false,
        ]);
    
        return response()->json($item, 200);
    }

    //8
    public function detail_item($id,$id_item){
        $item_data = Checklist::where('id', $id)
        ->where('user_id', auth()->id())
        ->with(['items' => function($query) use ($id_item) {
            $query->where('id', $id_item);
        }])
        ->first();

    if (!$item_data) {
        return response()->json(['error' => 'Checklist tidak ada'], 400);
    }
    
    if ($item_data->items->isEmpty()) {
        return response()->json(['error' => 'Item tidak ada'], 400);
    }
    return response()->json($item_data);
    }

    //9
    public function rename_item(Request $request, $id, $id_item){
        $item_updated=request()->validate([
            'ItemName'=>'required|string|max:255',
        ]);
        $checklist = checklist::where('id', $id)
        ->where('user_id', auth()->id())
        ->first();

        if (!$checklist) {
            return response()->json(['error' => 'Data tidak ada'], 400);
        }
    
        $item = Item::where('id', $id_item)
        ->where('checklist_id', $id) 
        ->first();

        if (!$item) {
            return response()->json(['error' => 'Item tidak ditemukan'], 400);
        }
        $item->description = $item_updated['ItemName'];
        $item->save();
        return response()->json(['message' => 'Item berhasil di update', 'item' => $item]);
    }

    //10
    public function update_status($id,$id_item){
        $checklist = checklist::where('id', $id)
        ->where('user_id', auth()->id())
        ->first();

        if (!$checklist) {
            return response()->json(['error' => 'Checklist tidak ada'], 400);
        }

        $item_updated = Item::where('id', $id_item)
        ->where('checklist_id', $id) 
        ->first();

        if (!$item_updated) {
            return response()->json(['error' => 'Item tidak ada'], 400);
        }
        
        $item_updated->is_completed = !$item_updated->is_completed;
        $item_updated->save();
        return response()->json(['message' => 'Status Item berhasil di updated', 'item' => $item_updated]);
    }

    //11
    public function delete_item($id,$id_item){
        $checklist = checklist::where('id', $id)
        ->where('user_id', auth()->id())
        ->first();

        if (!$checklist) {
            return response()->json(['error' => 'Checklist tidak ada'], 400);
        }
        $item_deleted= Item::where('id', $id_item)
        ->where('checklist_id', $id)
        ->first();

        if (!$item_deleted) {
            return response()->json(['error' => 'Item tidak ditemukan'], 400);
        }

        $item_deleted->delete();
        return response()->json(['message' => 'Item berhasil dihapus'],200);
    }



}
