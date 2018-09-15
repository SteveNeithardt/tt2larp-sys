<?php

namespace tt2larp\Http\Controllers;

use Auth;
use Validator;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use tt2larp\Models\Chat;
use tt2larp\Models\Message;

class ChatController extends Controller
{
    /**
     * Show the chat.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$title = config('app.name', 'Laravel') . ' - ' . __('i.chat interface');

		$chat_id = Chat::all()->first()->id;

        return view('chat')->with(compact('title', 'chat_id'));
    }

	/**
	 * Get the list of Messages
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function messages(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'chat_id' => 'required|integer',
			//'offset' => 'sometimes|integer',
			//'amount' => 'sometimes|integer',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$chat = Chat::find($request->chat_id);
		if ($chat === null) {
			return new JsonResponse([
				'success' => false,
				'message' => __('i.The requested :instance doesn\'t exist.', [ 'instance' => 'Chat' ])
			], 400);
		}

		$user = Auth::user();
		$user_id = $user === null ? null : $user->id;

		$query = $chat->messages()->recent()->select('user_id', 'deleted', 'read', 'message', 'created_at');
		if ($user_id === null) {
			$query->where('deleted', false);
		}
		$messages = $query->get();

		return new JsonResponse([
			'success' => true,
			'messages' => $messages->map(function ($m) use ($user_id) {
				$out = [ 'message' => $m->time . ($m->user_id == $user_id ? '>>' : '<<') . ' ' . $m->message ];
				if ($m->deleted) {
					$out['deleted'] = true;
				}
				return $out;
			}),
		]);
	}

	/**
	 * Post a new Message
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function newMessage(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'chat_id' => 'required|integer',
			'message' => 'required|string',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$chat = Chat::find($request->chat_id);
		if ($chat === null) {
			return new JsonResponse([
				'success' => false,
				'message' => __('i.The requested :instance doesn\'t exist.', [ 'instance' => 'Chat' ])
			], 400);
		}

		$user = Auth::user();
		$user_id = $user === null ? null : $user->id;

		if ($request->message === 'DELTA COMMAND') {
			$message = $chat->messages()->recent()->where('deleted', false)->first();
			$message->deleted = true;
			$message->save();
			return new JsonResponse([
				'success' => true,
				'message' => now()->format('Hms') . '>> ' . $request->message,
			]);
		} else {
			$message = new Message();
			$message->user_id = $user_id;
			$message->message = $request->message;
			$message->deleted = false;
			$message->read = $user_id !== null;
			$chat->messages()->save($message);
			return new JsonResponse([
				'success' => true,
				'message' => $message->time . '>> ' . $message->message,
			]);
		}
	}

	/**
	 * Get list of unread messages
	 */
	public function unread(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'chat_id' => 'required|integer',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$chat = Chat::find($request->chat_id);
		if ($chat === null) {
			return new JsonResponse([
				'success' => false,
				'message' => __('i.The requested :instance doesn\'t exist.', [ 'instance' => 'Chat' ])
			], 400);
		}

		$unreadCount = $chat->messages()->where('read', false)->count();

		return new JsonResponse([
			'success' => true,
			'unreadCount' => $unreadCount,
		]);
	}

	/**
	 * Mark all messages as read
	 */
	public function markRead(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'chat_id' => 'required|integer',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$chat = Chat::find($request->chat_id);
		if ($chat === null) {
			return new JsonResponse([
				'success' => false,
				'message' => __('i.The requested :instance doesn\'t exist.', [ 'instance' => 'Chat' ])
			], 400);
		}

		$chat->messages()->where('read', false)->update([ 'read' => true ]);

		return new JsonResponse([
			'success' => true,
		]);
	}
}
