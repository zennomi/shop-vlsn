<?php namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends BaseModel
{
    protected $builder;
    protected $builderMessages;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('conversations');
        $this->builderMessages = $this->db->table('conversation_messages');
    }

    //add conversation
    public function addConversation()
    {
        $data = [
            'sender_id' => user()->id,
            'receiver_id' => inputPost('receiver_id'),
            'subject' => inputPost('subject'),
            'product_id' => inputPost('product_id'),
            'created_at' => date("Y-m-d H:i:s")
        ];
        if (empty($data['product_id'])) {
            $data['product_id'] = 0;
        }
        if ($this->builder->insert($data)) {
            return $this->db->insertID();
        }
        return false;
    }

    //add message
    public function addMessage($conversationId)
    {
        $data = [
            'conversation_id' => $conversationId,
            'sender_id' => user()->id,
            'receiver_id' => inputPost('receiver_id'),
            'message' => inputPost('message'),
            'is_read' => 0,
            'deleted_user_id' => 0,
            'created_at' => date("Y-m-d H:i:s")
        ];
        if (!empty($data['message'])) {
            if ($this->builderMessages->insert($data)) {
                $messageId = $this->db->insertID();
                $this->addMessageEmail($messageId);
                return $messageId;
            }
        }
        return false;
    }

    //add message
    public function addMessageEmail($messageId)
    {
        $message = $this->getMessage($messageId);
        if (!empty($message)) {
            $conversation = $this->getConversation($message->conversation_id);
            $receiver = getUser($message->receiver_id);
            if (!empty($conversation) && !empty($receiver) && $receiver->send_email_new_message == 1 && !empty($message->message)) {
                $emailData = [
                    'email_type' => 'new_message',
                    'email_address' => $receiver->email,
                    'email_subject' => trans("you_have_new_message"),
                    'email_data' => serialize(['messageSender' => getUsername(user()), 'messageSubject' => $conversation->subject, 'messageText' => $message->message]),
                    'template_path' => 'email/new_message'
                ];
                addToEmailQueue($emailData);
            }
        }
    }

    //get unread conversations
    public function getUnreadConversations($userId)
    {
        return $this->builder->where('conversations.id IN (' . $this->getUserUnreadConversationIdsQuery($userId) . ')')->orderBy('conversations.created_at', 'DESC')->distinct()->get()->getResult();
    }

    //get unread conversation count
    public function getUnreadConversationsCount($receiverId)
    {
        return $this->builder->join('conversation_messages', 'conversation_messages.conversation_id = conversations.id')->select('conversations.*, conversation_messages.is_read as is_read')
            ->where('conversation_messages.receiver_id', clrNum($receiverId))->where('conversation_messages.is_read', 0)->where('conversation_messages.deleted_user_id !=', clrNum($receiverId))
            ->distinct()->countAllResults();
    }

    //get read_conversations
    public function getReadConversations($userId)
    {
        $queryUnreadConversations = $this->getUserUnreadConversationIdsQuery($userId);
        $queryConversations = $this->getUserConversationIdsQuery($userId);
        return $this->builder->where("conversations.id IN ($queryConversations)", NULL, FALSE)->where("conversations.id NOT IN ($queryUnreadConversations)", NULL, FALSE)
            ->orderBy('conversations.created_at DESC')->distinct()->get()->getResult();
    }

    //get user latest conversation
    public function getUserLatestConversation($userId)
    {
        return $this->builder->join('conversation_messages', 'conversation_messages.conversation_id = conversations.id')->select('conversations.*, conversation_messages.is_read as is_read')
            ->where('deleted_user_id != ', clrNum($userId))->groupStart()->where('conversations.sender_id', clrNum($userId))->orWhere('conversations.receiver_id', clrNum($userId))->groupEnd()
            ->orderBy('conversations.created_at DESC')->get()->getRow();
    }

    //is conversation deleted
    public function isConversationDeleted($conversationId)
    {
        if (!empty($this->builderMessages->where('conversation_id', clrNum($conversationId))->where('deleted_user_id = ', user()->id)->get()->getRow())) {
            return true;
        }
        return false;
    }

    //get conversation
    public function getConversation($id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //get message
    public function getMessage($id)
    {
        return $this->builderMessages->where('id', clrNum($id))->get()->getRow();
    }

    //get messages
    public function getMessages($conversationId)
    {
        return $this->builderMessages->where('conversation_id', clrNum($conversationId))->get()->getResult();
    }

    //set conversation messages as read
    public function setConversationMessagesAsRead($conversationId)
    {
        $messages = $this->getUnreadMessages($conversationId);
        if (!empty($messages)) {
            foreach ($messages as $message) {
                if ($message->receiver_id == user()->id) {
                    $this->builderMessages->where('id', $message->id)->update(['is_read' => 1]);
                }
            }
        }
    }

    //get unread messages
    public function getUnreadMessages($conversationId)
    {
        return $this->builderMessages->where('conversation_id', $conversationId)->where('receiver_id', user()->id)->where('is_read', 0)
            ->orderBy('id DESC')->get()->getResult();
    }

    //get conversation unread messages count
    public function getConversationUnreadMessagesCount($conversationId)
    {
        return $this->builderMessages->where('conversation_id', clrNum($conversationId))->where('receiver_id', user()->id)->where('is_read', 0)->countAllResults();
    }

    //get user unread conversation ids
    public function getUserUnreadConversationIdsQuery($userId)
    {
        $this->builderMessages->resetQuery();
        return $this->builderMessages->select('conversation_id')->where('receiver_id', clrNum($userId))->where('deleted_user_id !=', clrNum($userId))->where('is_read', 0)
            ->distinct()->getCompiledSelect();
    }

    //get user conversation ids
    public function getUserConversationIdsQuery($userId)
    {
        return $this->builderMessages->select('conversation_id')->groupStart()->where('sender_id', clrNum($userId))->orWhere('receiver_id', clrNum($userId))->groupEnd()
            ->where('deleted_user_id !=', clrNum($userId))->distinct()->getCompiledSelect();
    }

    //delete conversation
    public function deleteConversation($id)
    {
        $conversation = $this->getConversation($id);
        if (!empty($conversation)) {
            $messages = $this->getMessages($conversation->id);
            if (!empty($messages)) {
                foreach ($messages as $message) {
                    if ($message->sender_id == user()->id || $message->receiver_id == user()->id) {
                        if ($message->deleted_user_id == 0) {
                            $data = ['deleted_user_id' => user()->id];
                            $this->builderMessages->where('id', $message->id)->update($data);
                        } else {
                            $this->builderMessages->where('id', $message->id)->delete();
                        }
                    }
                }
            }
            //delete conversation if does not have messages
            $messages = $this->getMessages($conversation->id);
            if (empty($messages)) {
                $this->builder->where('id', $conversation->id)->delete();
            }
        }
    }
}
