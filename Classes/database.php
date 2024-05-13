<?php
class database
{
 function opencon(){
return new PDO ('mysql:host=localhost;dbname=loginmethod','root', '');
}
function check($username, $password, $firstname, $lastname,$birthday,$sex){
$con=$this->opencon();
$query = "SELECT * from users WHERE Username='".$username."'&& Pass_word='".$password."'";
return  $con->query($query)->fetch();


    }
    function signup($username, $password, $firstname, $lastname, $birthday, $sex){
        $con = $this->opencon();
        $query = $con->prepare("SELECT Username FROM users WHERE Username = ?");
        $query->execute([$username]);
        $existingUser = $query->fetch();
 
        if ($existingUser){
            return false;
        }
        return $con->prepare("INSERT INTO users (Username, Pass_word, firstname, lastname, birthday, sex) VALUES(?, ?, ?, ?, ?, ?)")
        ->execute([$username, $password, $firstname, $lastname, $birthday, $sex]);
     }
        function signupUser($username, $password, $firstname, $lastname, $birthday, $sex){
        $con = $this->opencon();



        $query = $con->prepare("SELECT Username FROM users WHERE Username = ?");
        $query->execute([$username]);
        $existingUser = $query->fetch();
 
        if ($existingUser){
            return false;
        }
         $con->prepare("INSERT INTO users (Username, Pass_word, firstname, lastname, birthday, sex) VALUES(?, ?, ?, ?, ?, ?)")
        ->execute([$username, $password, $firstname, $lastname, $birthday, $sex]);
        return $con->lastInsertId();
    }


       function insertAddress($user_id, $street, $barangay, $city, $province) {
        $con = $this->opencon();

        return $con->prepare("INSERT INTO  user_address (UserID,user_add_street, user_add_barangay,user_add_city, user_add_province) 
        VALUES (?, ?, ?, ?, ?)")->execute([$user_id, $street, $barangay, $city, $province]);


}
}     
   
        
    