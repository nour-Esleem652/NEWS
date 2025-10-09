<?php  

// <select name="category"></select>
include "connetionOnDatabase.php";

if($connetion->error == false){
    if(isset($_POST["save"])){     
        $category_name = $_POST["category_name"];
}
    }


    if(($id,$data["password"])){
                  // من خلالها نصل الى صفحة الداش بورد
                $_SESSION["authUser"]=$data;
                header("Location:dashboardUi.php");
                    // echo "login done";
            }else{
                echo "login faile";  
            }
?>