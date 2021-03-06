<?php

class AdminPage extends Controller{

    private $msg = "";

    public function index(){
        $logs = array(); //user_id, username, archive_name, action_type, created_at
        $admin = $this->model("Admin");

        if(isset($_POST['maxFileSize']) && !empty($_POST['maxFileSize'])){
            $admin->setMaxFileSize($_POST['maxFileSize']);
        }
        if(isset($_POST['maxArchiveSize']) && !empty($_POST['maxArchiveSize'])){
            $admin->setMaxArchiveSize($_POST['maxArchiveSize']);
        }
        if(isset($_POST['maxFiles']) && !empty($_POST['maxFiles'])){
            $admin->setMaxFiles($_POST['maxFiles']);
        }
        if(isset($_POST['name_type']) && !empty($_POST['name_type'])){
            $admin->setName($_POST['name_type']);
        }


        $logs = $admin->getLogs();
        if(count($logs)){
            if(isset($_POST['download_xml'])){
                $this->downloadXML($logs, 'logs.xml');
            } elseif(isset($_POST['download_csv'])){
                $this->downloadCSV($logs, 'logs.csv');
            } elseif(isset($_POST['download_html'])){
                $this->downloadHTML($logs, 'logs.html');
            }
        }
        $this->view('home/AdminPage', ['msg' => $this->msg,'MaxFileSize' => $admin->getMaxFileSize(),'MaxArchiveSize' =>$admin->getMaxArchiveSize(),'MaxFiles' => $admin->getMaxFiles(),'name' => $admin->getName()]);

    }

    private function downloadXML($logs_array, $file_name){
        $xml_file = $file_name;
        $dom = new DOMDocument('1.0', 'utf-8'); 
        $root = $dom->createElement('Archives'); 
        for($i=0; $i<count($logs_array); $i++){
            $user_id = $logs_array[$i]['user_id'];  
            $username = $logs_array[$i]['username'];
            $archive_name = htmlspecialchars($logs_array[$i]['archive_name']);
            $action_type = $logs_array[$i]['action_type']; 
            $created_at = $logs_array[$i]['created_at'];

            $entry = $dom->createElement('user_id');
            $entry->setAttribute('user_id', $user_id);

            $xml_username = $dom->createElement('username', $username); 
            $entry->appendChild($xml_username);

            $xml_archive_name = $dom->createElement('archive_name', $archive_name); 
            $entry->appendChild($xml_archive_name);

            $xml_action_type = $dom->createElement('action_type', $action_type); 
            $entry->appendChild($xml_action_type); 

            $xml_created_at = $dom->createElement('created_at', $created_at); 
            $entry->appendChild($xml_created_at); 
            $root->appendChild($entry);
        }
        $dom->appendChild($root); 
        $dom->save($xml_file);
        if(file_exists($xml_file)){
            header('Content-Description: File Transfer');
            header('Content-Type: application/xml');
            header('Content-Disposition: attachment; filename='.basename($xml_file));
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: '.filesize($xml_file));
            readfile($xml_file);
            unlink($xml_file);
            exit();
        } else {
            $this->msg = "Error downloading";
        } 
    }

    private function downloadCSV($logs_array, $file_name){
        $csv_file = fopen($file_name, 'w');
        fputcsv($csv_file, array('user_id', 'username', 'archive_name', 'action_type', 'created_at'));
        foreach ($logs_array as $log){
            fputcsv($csv_file, $log);
        }
        fclose($csv_file);
        if(file_exists($file_name)){
            header('Content-Description: File Transfer');
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename='.basename($file_name));
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: '.filesize($file_name));
            readfile($file_name);
            unlink($file_name);
            exit();
        } else {
            $this->msg = "Error downloading";
        } 
    }
    private function downloadHTML($logs_array, $file_name){
        $html_file = fopen($file_name, 'w');
        $text = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
            table, th, td {
                border: 1px solid black;
            }
            </style>
        </head>
        <body>
        <table style=\"width:100%\">
            <tr>
                <th>user_id</th>
                <th>username</th> 
                <th>archive_name</th>
                <th>action_type</th>
                <th>created_at</th>
            </tr>";
        fwrite($html_file, $text);
        for($i=0; $i<count($logs_array); $i++){
            $text = "
            <tr>
                <td>".$logs_array[$i]['user_id']."</td>
                <td>".$logs_array[$i]['username']."</td>
                <td>".$logs_array[$i]['archive_name']."</td>
                <td>".$logs_array[$i]['action_type']."</td>
                <td>".$logs_array[$i]['created_at']."</td>
            </tr>";
            fwrite($html_file, $text);
        }
        $text = "
        </table>
        </body>
        </html>";
        fclose($html_file);
        if(file_exists($file_name)){
            header('Content-Description: File Transfer');
            header('Content-Type: application/html');
            header('Content-Disposition: attachment; filename='.basename($file_name));
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: '.filesize($file_name));
            readfile($file_name);
            unlink($file_name);
            exit();
        } else {
            $this->msg = "Error downloading";
        } 
    }
}

?>