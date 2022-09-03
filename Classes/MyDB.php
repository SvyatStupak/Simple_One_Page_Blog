<?php

namespace Classes;

class MyDB
{
    protected $db;

    public function __construct()
    {
        include_once 'config.php';
        $this->db = new \mysqli(HOST, USER, PASSWORD, DB);
        if ($this->db->connect_error) {
            throw new \Exception('Connection error: ' . $this->db->connect_error);
        }
    }

    public function __destruct()
    {
        if ($this->db) {
            $this->db->close();
        }
    }

    public function getStat()
    {
        $allEstimate = $this->getGrade();
        $positiveRate = $this->getGrade(3, '>');
        $negativeRate = $this->getGrade(3, '<');

        $stat = array(
            'allEstimate'  => $allEstimate,
            'positiveRate' => $positiveRate,
            'negativeRate' => $negativeRate,
        );
        
        return $stat;
    }

    public function getGrade($stars = '', $operand = '')
    {
        try {
            $query = "SELECT COUNT(comment_id) as count_post FROM comments_rating WHERE rating $operand $stars";
            $result = $this->db->query($query);
            $data = $result->fetch_row();
            return $data[0];
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function storeComments()
    {
        try {
            if (!empty($_POST["name"]) && !empty($_POST["comment"])) 
            {
                $insertComments = "INSERT INTO comment (parent_id, comment, sender) 
                                VALUES ('" . $_POST["commentId"] . "', '" . $_POST["comment"] . "', '" . $_POST["name"] . "')";
                $result = $this->db->query($insertComments);

                if (!$result) {
                    throw new \Exception("ERROR: " . $this->db->errno . ' | ' . $this->db->error);
                }
            
                $message = '<label class="text-success">Comment posted Successfully.</label>';
                $status = array(
                    'error'  => 0,
                    'message' => $message
                );
            } else {
                $message = '<label class="text-danger">Error: Comment not posted.</label>';
                $status = array(
                    'error'  => 1,
                    'message' => $message
                );
            }
            return $status;
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }

    public function storeRate()
    {
        try {
            if (isset($_POST['commentID']) AND isset($_POST['rating'])) 
            {
                $commentID = $_POST["commentID"];
                $rating = $_POST["rating"];

                $sql = "INSERT INTO comments_rating (comment_id,rating) VALUES ('$commentID','$rating')";
                $result = $this->db->query($sql);
            if (!$result) {
                throw new \Exception("ERROR: " . $this->db->errno . ' | ' . $this->db->error);
            }
                $message = '<label class="text-success">Estimate added Successfully.</label>';
                $status = array(
                    'error'  => 0,
                    'message' => $message
                );
            } else {
                $message = '<label class="text-danger">Error: Estimate not added.</label>';
                $status = array(
                    'error'  => 1,
                    'message' => $message
                );
            }
            return $status;
        } catch (\Exception $e) {
            $e->getMessage();
        }

    }

    public function showComments()
    {
        try {
            $commentQuery = "SELECT id, parent_id, comment, sender, date FROM comment WHERE parent_id = '0' ORDER BY id DESC";
            $commentsResult = $this->db->query($commentQuery);
            $commentHTML = '';
            while ($comment = mysqli_fetch_assoc($commentsResult)) {
                $commentHTML .= '
		<div class="panel panel-primary">
            <div class="panel-heading">By <b>' . $comment["sender"] . '</b> on <i>' . $comment["date"] . '</i></div>
            <div class="panel-body">' . $comment["comment"] . '</div>
            <div class="panel-footer" align="right">
                <button  type="button" class="btn btn-primary reply" id="' . $comment["id"] . '">Reply</button>
            </div>  
		</div>

        <form  method="post" class="rateForm">

            <label>Rate user comment ' . $comment["sender"] . '</label>
            <div class="rateyo" id="rating" data-rateyo-rating="4" data-rateyo-num-stars="5" data-rateyo-score="3"></div>
            
            <span class=\'result\'>4</span>
            <input type="hidden" name="rating">
            <input type="hidden" name="commentID" id="commentID" value="' . $comment["id"] . '"/>
            
            <div><button type="submit" name="addRate" id="addRate" class="addRate btn btn-primary">Estimate</button></div>
    
        </form>
        <br>';

                $commentHTML .= $this->getCommentReply($comment["id"]);
            }
            return $commentHTML;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    protected function getCommentReply($parentId = 0, $marginLeft = 0)
    {
        try {
            $commentHTML = '';
            $commentQuery = "SELECT id, parent_id, comment, sender, date FROM comment WHERE parent_id = '" . $parentId . "'";
            $commentsResult = $this->db->query($commentQuery);
            $commentsCount = mysqli_num_rows($commentsResult);
            if ($parentId == 0) {
                $marginLeft = 0;
            } else {
                $marginLeft = $marginLeft + 48;
            }
            if ($commentsCount > 0) {
                while ($comment = mysqli_fetch_assoc($commentsResult)) {
                    $commentHTML .= '
				<div class="panel panel-primary" style="margin-left:' . $marginLeft . 'px">
				<div class="panel-heading">By <b>' . $comment["sender"] . '</b> on <i>' . $comment["date"] . '</i></div>
				<div class="panel-body">' . $comment["comment"] . '</div>
				<div class="panel-footer" align="right"><button type="button" class="btn btn-primary reply" id="' . $comment["id"] . '">Reply</button></div>
				</div>';
                    $commentHTML .= $this->getCommentReply($comment["id"], $marginLeft);
                }
            }
            return $commentHTML;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
