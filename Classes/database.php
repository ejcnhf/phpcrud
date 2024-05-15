<?php
class database
{
 function opencon(){
return new PDO ('mysql:host=localhost;dbname=loginmethod','root', '');
}
function check($username, $password){
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


       function insertAddress($UserID, $street, $barangay, $city, $province) {
        $con = $this->opencon();

        return $con->prepare("INSERT INTO  user_address (UserID, user_add_street, user_add_barangay,user_add_city, user_add_province) 
        VALUES (?, ?, ?, ?, ?)")->execute([$UserID, $street, $barangay, $city, $province]);


       }
       function view(){
        $con = $this->opencon();
        return $con->query("SELECT users.UserID, users.firstname, users.lastname, users.birthday, users.sex, users.Username, users.Pass_word,  CONCAT(user_address.user_add_street,' ', user_address.user_add_barangay,' ', user_address.user_add_city,' ', user_address.user_add_province) as address FROM users INNER JOIN user_address ON users.UserID = user_address.UserID")->fetchAll();
    }

    function delete($id)
    {
        try{
            $con = $this->opencon();
            $con->beginTransaction();
            $query = $con->prepare("DELETE FROM user_address
            WHERE UserID =?");
            $query->execute([$id]);
            $query = $con->prepare("DELETE FROM users WHERE UserID = ?");
            $query->execute([$id]);

            $con->commit();
            return true;
          }  catch (PDOException $e) {
            $con->rollBack();
            return false;
          } 
        }

          function viewdata($id){
              try{
                
                  $con = $this->opencon();
                  $query = $con->prepare("SELECT users.UserID, users.firstname, users.lastname, users.birthday, users.sex, 
                  users.Username, users.Pass_word,user_address.user_add_street,
                   user_address.user_add_barangay, user_address.user_add_city, user_address.user_add_province 
                   FROM users INNER JOIN user_address ON users.UserID = user_address.UserID; WHERE users.UserID = ? ");
                  $query->execute([$id]);
                  return $query->fetch();

              } catch (PDOException $e) {
                return [];
              }
          }
                function updateUser($user_id, $firstname, $lastname, $birthday, $sex, $Username, $Pass_word){
                 try{
                       $con = $this->opencon();
                       $query = $con->beginTransaction();
                       $query = $con->prepare("UPDATE users SET firstname=?, lastname=?, birthday=?, sex?, username=?, user_pass=?, WHERE UserID=? ");

                       $query->execute([$firstname, $lastname,$birthday, $sex, $Username, $Pass_word, $user_id]);
                       $con->commit();
                }    catch (PDOException $e){
                    $con->rollBack();
                    return false;  
                }
              } 
              function updateUserAddress($user_id, $street, $barangay, $city, $province,){
                try{
                      $con = $this->opencon();
                      $query = $con->beginTransaction();
                      $query = $con->prepare("UPDATE user_address=? SET user_street=?, user_barangay=?, user_province=?,WHERE UserID=? ");

                      $query->execute([$user_id, $street,$barangay, $city, $province]);
                      $con->commit();
               }    catch (PDOException $e){
                   $con->rollBack();
                   return false;  

}
}
}
    