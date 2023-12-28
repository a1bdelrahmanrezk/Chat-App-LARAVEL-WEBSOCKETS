<?php

namespace App\Http\Controllers;

use App\Events\deleteMessageEvent;
use App\Events\messageEvent;
use App\Events\updateMessageEvent;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
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
        $request->validate([
            'sender_id' => ['exists:users,id'],
            'receiver_id' => ["exists:users,id"],
            'message' => 'string'
        ]);
        try {
            $chat = Chat::create([
                'sender_id' => $request->sender_id,
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
            ]);
            event(new messageEvent($chat));
            return response()->json([
                'data' => $chat,
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
            })->get();
            return response()->json([
                'data' => $chats,
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
    public function deleteChat(Request $request)
    {
        try {
            Chat::where('id', '=', $request->id)->delete();
            event(new deleteMessageEvent($request->id));
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
    public function updateChat(Request $request)
    {
        // $dataValidated = $request->validate([
        //     'id'=>'exists:chats,sender_id',
        //     'message'=> ['string'],
        // ]);
        try {
            Chat::where('id', '=', $request->id)->update([
                'message' => $request->message,
            ]);
            $chat = Chat::where('id','=',$request->id)->first();
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
}
