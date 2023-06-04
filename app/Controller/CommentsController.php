<?php
class CommentsController extends AppController
{
    public function editcomment($comment_id = null)
    {
        if (!$comment_id) {
            throw new NotFoundException(__('Invalid post'));
        }
        $comment = $this->Comment->findById($comment_id);
        if (!$comment) {
            throw new NotFoundException(__('Invalid post'));
        }
        if (!empty($this->request->data)) {
            if ($this->Comment->save($this->request->data)) {
                $this->Flash->success(__('Your Comment has been updated.'));
                return $this->redirect(array('controller' => 'posts', 'action' => 'view', $this->request->data['Comment']['post_id']));
            }
            $this->Flash->error(__('Unable to update your post.'));
        } else {
            $this->request->data = $comment;
        }
    }
    public function deletecomment($comment_id = null,$post_id = null)
    {
        if (!$comment_id || !$post_id) {
            throw new NotFoundException(__('Invalid comment'));
        }
        $this->Comment->id = $comment_id;
        if ($this->Comment->delete()) {
            $this->Flash->success(__('Your Comment has been deleted.'));
            return $this->redirect(array('controller' => 'posts', 'action' => 'view', $post_id));
        }
    }
}
