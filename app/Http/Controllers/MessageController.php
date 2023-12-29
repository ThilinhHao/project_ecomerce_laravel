<?php

namespace App\Http\Controllers;
use App\Events\MessageSent;
use App\Http\Requests\Message\MessageStoreRequest;
use App\Repositories\Message\MessageRepositoryInterface;
use App\Traits\SessionTrait;

class MessageController extends Controller
{

    use SessionTrait;

    protected MessageRepositoryInterface $messageRepo;

    public function __construct(MessageRepositoryInterface $messageRepo)
    {
        $this->messageRepo = $messageRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $messages = $this->messageRepo->messagePanigate();

        return view('backend.message.index')->with('messages',$messages);
    }

    public function messageFive()
    {
        $message = $this->messageRepo->getMessageLimit();
        return response()->json($message);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MessageStoreRequest $request)
    {
        $message = $this->messageRepo->create($request->all());

        $data = array();
        $data['url'] = route('message.show',$message->id);
        $data['date'] = $message->created_at->format('F d, Y h:i A');
        $data['name'] = $message->name;
        $data['email'] = $message->email;
        $data['phone'] = $message->phone;
        $data['message'] = $message->message;
        $data['subject'] = $message->subject;
        $data['photo'] = Auth()->user()->photo;

        event(new MessageSent($data));
        exit();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $message = $this->messageRepo->find($id);

        if ($message) {
            $message['read_at'] = \Carbon\Carbon::now();

            $message = $this->messageRepo->update($id, ['read_at' => $message['read_at']]);

            return view('backend.message.show')->with('message',$message);
        } else {
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = $this->messageRepo->delete($id);

        if ($status) {
            $this->success();
        } else{
            $this->error();
        }
        return back();
    }
}
