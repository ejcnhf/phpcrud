<?php
class database
{
 function opencon(){
return new PDO ('mysql:host=localhost;dbname=loginmethod','root', '');
}
//function check($username, $password){
//$con=$this->opencon();
//$query = "SELECT * from users WHERE Username='".$username."'&& Pass_word='".$password."'";
//return  $con->query($query)->fetch();

function check($username, $password) {
    // Open database connection
    $con = $this->opencon();

    // Prepare the SQL query
    $stmt = $con->prepare("SELECT * FROM users WHERE Username = ?");
    $stmt->execute([$username]);

    // Fetch the user data as an associative array
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If a user is found, verify the password
    if ($user && password_verify($password, $user['Pass_word'])) {
        return $user;
    }

    // If no user is found or password is incorrect, return false
    return false;
}









    
   function signup($username, $password, $firstname, $lastname, $birthday, $sex){
        $con = $this->opencon();
       $query = $con->prepare("SELECT Username FROM users WHERE Username = ?");
        $query->execute([$username]);
        $existingUser = $query->fetch();//
 


        if ($existingUser){
            return false;
        }
        return $con->prepare("INSERT INTO users (Username, Pass_word, firstname, lastname, birthday, sex) VALUES(?, ?, ?, ?, ?, ?)")
        ->execute([$username, $password, $firstname, $lastname, $birthday, $sex]);
     }
    //     function signupUser($username, $password, $firstname, $lastname, $birthday, $sex){
    //     $con = $this->opencon();

    //     $query = $con->prepare("SELECT Username FROM users WHERE Username = ?");
    //     $query->execute([$username]);
    //     $existingUser = $query->fetch();
 
    //     if ($existingUser){
    //         return false;
    //     }
    //      $con->prepare("INSERT INTO users (Username, Pass_word, firstname, lastname, birthday, sex) VALUES(?, ?, ?, ?, ?, ?)")
    //     ->execute([$username, $password, $firstname, $lastname, $birthday, $sex]);
    //     return $con->lastInsertId();
    // }

   function signupUser($firstname, $lastname, $birthday, $sex, $email, $username, $password, $profile_picture_path)
   {
       $con = $this->opencon();
        //Save user data along with profile picture path to the database
       $con->prepare("INSERT INTO users (firstname, lastname, birthday, sex, user_email, Username, Pass_word, user_profile_picture) VALUES (?,?,?,?,?,?,?,?)")
       ->execute([ $firstname, $lastname, $birthday, $sex, $email, $username, $password, $profile_picture_path]);
       return $con->lastInsertId();
       }
    



       function insertAddress($UserID, $street, $barangay, $city, $province) {
        $con = $this->opencon();

        return $con->prepare("INSERT INTO  user_address (UserID, user_add_street, user_add_barangay,user_add_city, user_add_province) 
        VALUES (?, ?, ?, ?, ?)")->execute([$UserID, $street, $barangay, $city, $province]);


       }
       function view(){
        $con = $this->opencon();
        return $con->query("SELECT users.UserID, users.firstname, users.lastname, users.birthday, users.sex, users.Username, users.Pass_word, users.user_profile_picture, CONCAT(user_address.user_add_street,' ', user_address.user_add_barangay,' ', user_address.user_add_city,' ', user_address.user_add_province) as address FROM users INNER JOIN user_address ON users.UserID = user_address.UserID")->fetchAll();
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
                  users.Username, users.Pass_word, users.user_profile_picture, user_address.user_add_street,
                   user_address.user_add_barangay, user_address.user_add_city, user_address.user_add_province 
                   FROM users INNER JOIN user_address ON users.UserID = user_address.UserID WHERE users.UserID = ? ");
                  $query->execute([$id]);
                  return $query->fetch();

              } catch (PDOException $e) {
                return [];
              }
          }
          
          function updateUser($user_id, $firstname, $lastname, $birthday,$sex, $username, $password) {
              try {
                  $con = $this->opencon();
                  $con->beginTransaction();
                  $query = $con->prepare("UPDATE users SET firstname=?, lastname=?,birthday=?, sex=?,Username=?, Pass_word=? WHERE UserID=?");
                  $query->execute([$firstname, $lastname,$birthday,$sex,$username, $password, $user_id]);
                  // Update successful
                  $con->commit();
                  return true;
              } catch (PDOException $e) {
                  // Handle the exception (e.g., log error, return false, etc.)
                   $con->rollBack();
                  return false; // Update failed
              }
          }
          
          function updateUserAddress($user_id, $street, $barangay, $city, $province) {
              try {
                  $con = $this->opencon();
                  $con->beginTransaction();
                  $query = $con->prepare("UPDATE user_address SET user_add_street=?, user_add_barangay=?, user_add_city=?, user_add_province=? WHERE UserID=?");
                  $query->execute([$street, $barangay, $city, $province, $user_id]);
                  $con->commit();
                  return true; // Update successful
              } catch (PDOException $e) {
                  // Handle the exception (e.g., log error, return false, etc.)
                  $con->rollBack();
                  return false; // Update failed
              }
               
          }
          function validateCurrentPassword($userId, $currentPassword) {
            // Open database connection
            $con = $this->opencon();
        
            // Prepare the SQL query
            $query = $con->prepare("SELECT Pass_word FROM users WHERE UserID = ?");
            $query->execute([$userId]);
        
            // Fetch the user data as an associative array
            $user = $query->fetch(PDO::FETCH_ASSOC);
        
            // If a user is found, verify the password
            if ($user && password_verify($currentPassword, $user['Pass_word'])) {
                return true;
            }
        
            // If no user is found or password is incorrect, return false
            return false;
        }

        }
         