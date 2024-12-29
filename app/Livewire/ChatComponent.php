<?php

namespace App\Livewire;

use App\Events\MessageSendEvent;
use App\Models\Message;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class ChatComponent extends Component
{
    use WithFileUploads;
    public $user;
    public $sender_id;
    public $receiver_id;
    public $media;
    public $voice;
    public $message = '';
    public $messages = [];

    public function render()
    {
        return view('livewire.chat-component');
    }

    public function mount ($user_id){
        $this->sender_id = auth()->user()->id;
        $this->receiver_id = $user_id;

        $messages = Message::where(function ($query){
            $query->where('sender_id', $this->sender_id)
                    ->where('receiver_id', $this->receiver_id);
        })->orWhere(function ($query){
            $query->where('sender_id', $this->receiver_id)
                    ->where('receiver_id', $this->sender_id);
        })->with('sender:id,name', 'receiver:id,name')->get();

        foreach ($messages as $message) {
            $this->appendChatMessage($message);
        }

        $this->user = User::Where('id', $user_id)->first();
    }

    #[On('echo-private:chat-channel.{sender_id},MessageSendEvent')]
    public function listenForMessage($event){
        $chatMessage = Message::whereId($event['message']['id'])
        ->with('sender:id,name', 'receiver:id,name')
        ->first();
        $this->appendChatMessage($chatMessage);
    }

    public function sendMessage()
    {
        $chatMessage = new Message();
        $chatMessage->sender_id = $this->sender_id;
        $chatMessage->receiver_id = $this->receiver_id;
        $chatMessage->message = $this->message;

        // Handle media upload
        if ($this->media) {
            $mediaPath = $this->media->store('media_messages', 'public');
            $chatMessage->media_path = $mediaPath;
        }

        // Handle voice upload
        if ($this->voice) {
            $voicePath = $this->voice->store('voice_messages', 'public');
            $chatMessage->voice_path = $voicePath;
        }

        $chatMessage->save();

        $this->appendChatMessage($chatMessage);
        broadcast(new MessageSendEvent($chatMessage))->toOthers();

        $this->message = '';
        $this->media = null;
        $this->voice = null;
    }


    public function appendChatMessage($message){
        $this->messages[] = [
            'id' => $message->id,
            'message' => $message->message,
            'sender' => $message->sender->name,
            'receiver' => $message->receiver->name,
            'media_path' => $message->media_path,
            'voice_path' => $message->voice_path,
        ];
    }
}
