<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use App\Events\messageEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Events\deleteMessageEvent;
use App\Events\updateMessageEvent;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function loadDashboard()
    {
        $users = User::whereNotIn('id', [auth()->user()->id])->get();
        return view('dashboard', compact('users'));
    }
    public function saveChat(Request $request)
    {
        $validatedRequest = $request->validate([
            'sender_id' => ['exists:users,id'],
            'receiver_id' => ["exists:users,id"],
            'message' => 'required_without:file|string',
            'file' => 'required_without:message',
        ]);
        try {
            $chat = Chat::create([
                // 'sender_id' => $request->sender_id,
                'sender_id' => auth()->user()->id,
                'receiver_id' => $validatedRequest['receiver_id'],
                'message' => $request->hasFile('file') ? 'Photo' :$validatedRequest['message'],
            ]);
            if($request->hasFile('file')){
                $chat->addMediaFromRequest('file')->toMediaCollection('chat_file');
                $chat->update([
                    'message'=> $chat->getFirstMediaUrl('chat_file'),
                ]);
                event(new messageEvent($chat));
                // event(new messageEvent('Photo Sending now'));
            }else{
                event(new messageEvent($chat));
            }
            return response()->json([
                'data' => $chat,
                'file' => $request->hasFile('file') ?  $chat->getFirstMediaUrl('chat_file') : 'NULL' ,
                'responseMessage' => $request->hasFile('file') ?  'File Sent' : 'Text Sent',
                'success' => true,
                'statusCode' => Response::HTTP_CREATED,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
    public function loadChats(Request $request)
    {
        $request->validate([
            'sender_id' => ['exists:users,id'],
            'receiver_id' => ["exists:users,id"],
        ]);
        try {
            $chats = Chat::where(function ($query) use ($request) {
                $query->where('sender_id', '=', $request->sender_id)
                    ->orWhere('sender_id', '=', $request->receiver_id);
            })->where(function ($query) use ($request) {
                $query->where('receiver_id', '=', $request->sender_id)
                    ->orWhere('receiver_id', '=', $request->receiver_id);
            })->orderBy('created_at', 'asc')
            ->get();
            return response()->json([
                'data' => $chats,
                'success' => true,
                'statusCode' => Response::HTTP_OK,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
    public function deleteChat(Request $request)
    {
        $dataValidated = $request->validate([
            'id'=>'required',
        ]);
        try {
            Chat::where('id', '=', $dataValidated['id'])->delete();
            event(new deleteMessageEvent($dataValidated['id']));
            return response()->json([
                'message' => 'Chat deleted',
                'success' => true,
                'statusCode' => Response::HTTP_OK,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function updateChat(Request $request) // $request[message], $id
    {
        $dataValidated = $request->validate([
            'id'=>'required',
            'message'=> ['string'],
        ]);
        try {
            Chat::where('id', '=', $dataValidated['id'])->update([
                'message' => $dataValidated['message'],
            ]);
            $chat = Chat::where('id', '=', $dataValidated['id'])->first();
            event(new updateMessageEvent($chat));
            return response()->json([
                'message' => 'Chat updated',
                'success' => true,
                'statusCode' => Response::HTTP_OK,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function loginUser(Request $request)
    {
        
        $data = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);
        // search about user by email
        $patient = User::where('email', $data['email'])->first();
        // check email with given password
        if ($patient && Hash::check($data['password'], $patient->password)) {
            // delete old tokens
            $patient->tokens()->delete();
            // create new token
            $token = $patient->createToken("token")->plainTextToken;
            // return response
            return $this->signResponse('user login successfully',$patient, $token,200);
        }
        // return response
        return $this->errorResponse('invalid email or password');
    }


}
