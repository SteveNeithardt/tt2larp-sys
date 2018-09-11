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

		$messages = $chat->messages()->recent()->select('user_id', 'message', 'created_at')->get();

		$user = Auth::user();
		$user_id = $user === null ? null : $user->id;

		return new JsonResponse([
			'success' => true,
			'messages' => $messages->map(function ($m) use ($user_id) {
				return [
					'message' => $m->time . ($m->user_id == $user_id ? '>>' : '<<') . ' ' . $m->message,
					//'time' => $m->time
				];
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

		$message = new Message();
		$message->user_id = $user_id;
		$message->message = $request->message;
		$chat->messages()->save($message);

		return new JsonResponse([
			'success' => true,
			'message' => $message->time . '>> ' . $message->message,
		]);
	}
}
