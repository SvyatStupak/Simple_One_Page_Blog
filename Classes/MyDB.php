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

    public function store()
    {
        if (!empty($_POST["name"]) && !empty($_POST["comment"])) {


            try {
                $insertComments = "INSERT INTO comment (parent_id, comment, sender) 
                                VALUES ('" . $_POST["commentId"] . "', '" . $_POST["comment"] . "', '" . $_POST["name"] . "')";
                $result = $this->db->query($insertComments);

                if (!$result) {
                    throw new \Exception("ERROR: " . $this->db->errno . ' | ' . $this->db->error);
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
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
		<div class="panel-footer" align="right"><button type="button" class="btn btn-primary reply" id="' . $comment["id"] . '">Reply</button></div>
		</div> ';
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
				</div>
				';
                    $commentHTML .= $this->getCommentReply($comment["id"], $marginLeft);
                }
            }
            return $commentHTML;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    // public function getAll()
    // {
    //     try {
    //         $query = "SELECT * FROM users";
    //         $result = $this->db->query($query);
    //         if (!$result) {
    //             throw new Exception("ERROR: " . $this->db->errno . '|' . $this->db->error);
    //         }

    //         $resultArr = [];
    //         while ($row = mysqli_fetch_assoc($result)) {
    //             $resultArr[] = $row;
    //         }
    //         return $resultArr;
    //     } catch (\Exception $e) {
    //         echo $e->getMessage();
    //     }
    // }

    // public function getNameColumns()
    // {
    //     try {
    //         $query = "SHOW COLUMNS FROM users";
    //         $result = $this->db->query($query);
    //         if (!$result) {
    //             throw new Exception("ERROR: " . $this->db->errno . '|' . $this->db->error);
    //         }

    //         $resultNameColumns = [];
    //         while ($row = mysqli_fetch_array($result)) {
    //             $resultNameColumns[] = $row['Field'];
    //         }
    //         return $resultNameColumns;
    //     } catch (\Exception $e) {
    //         echo $e->getMessage();
    //     }
    // }

    // public function store($csv, $fieldsInBD)
    // {
    //     if (PASS_FIRST) array_shift($csv);
    //     $columns = '';
    //     foreach ($fieldsInBD as $key => $files) {
    //         $columns .= '`' . $files . '`' . ',';
    //     }
    //     $columns = trim($columns, ',');

    //     if ($columns) {
    //         $str = '';
    //         foreach ($csv as $item) {
    //             $r = '';
    //             foreach ($item as $value) {
    //                 $r .= "'" . $this->db->real_escape_string($value) . "',";
    //             }
    //             $r = trim($r, ',');

    //             if (strlen($r) != 0) {
    //                 $str .= '(' . $r . '),';
    //             }
    //         }
    //         $str = trim($str, ',');



    //         try {
    //             $query = "REPLACE INTO `users` (" . $columns . ") VALUES " . $str;
    //             $result = $this->db->query($query);
    //             if (!$result) {
    //                 throw new Exception("ERROR: " . $this->db->errno . ' | ' . $this->db->error);
    //             }
    //             echo "<script type=\"text/javascript\">
    //                     alert(\"CSV File has been successfully Imported.\");
    //                     window.location = \"index.php\"
    //                   </script>";
    //         } catch (\Exception $e) {
    //             echo $e->getMessage();
    //         }
    //     }
    // }

    // public function export()
    // {
    //     header('Content-Type: text/csv; charset=utf-8');
    //     header('Content-Disposition: attachment; filename=data.csv');
    //     $output = fopen("php://output", "w");
    //     $nameColumns = $this->getNameColumns();
    //     fputcsv($output, $nameColumns);
    //     $query = "SELECT * from `users`";
    //     $result = $this->db->query($query);
    //     while ($row = mysqli_fetch_assoc($result)) {
    //         fputcsv($output, $row);
    //     }
    //     fclose($output);
    //     exit();
    // }

    // public function delete()
    // {
    //     $query = "DELETE FROM `users`";
    //     $result = $this->db->query($query);
    //     if (!$result) {
    //         throw new Exception("ERROR: " . $this->db->errno . '|' . $this->db->error);
    //     }
    //     echo "<script type=\"text/javascript\">
    //                     alert(\"Data from the database has been deleted.\");
    //                     window.location = \"index.php\"
    //                   </script>";
    // }
}
