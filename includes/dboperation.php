<?php

use phpDocumentor\Reflection\DocBlock\Description;

class DbOperation
{
    private $con;
    function __construct()
    {
        require_once dirname(__FILE__) . '/dbconnect.php';
        $db = new DbConnect();
        $this->con = $db->connect();
    }

    //login API

    function userlogin($username, $password, $other)
    {
        $stmt = $this->con->prepare("SELECT id FROM login WHERE username = ? AND password = ? AND role = ?");
        $stmt->bind_param("sss", $username, $password, $other);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
    function getUserByEmail($username, $password, $role)
    {
        $stmt = $this->con->prepare("SELECT id , username,password,role from login WHERE username = ? AND password = ? AND role = ?");
        $stmt->bind_param("sss", $username, $password, $role);
        $stmt->execute();
        $stmt->bind_result($id, $username, $password, $role);
        $stmt->fetch();
        $User = array();
        $User['id'] = $id;
        $User['username'] = $username;
        $User['password'] = $password;
        $User['role'] = $role;
        return $User;
    }

    function updatetoken($token, $username, $password, $role)
    {
        $stmt = $this->con->prepare("UPDATE `login` SET `token`= ? WHERE username = ? AND password = ? AND role = ?");
        $stmt->bind_param("ssss", $token, $username, $password, $role);
        if ($stmt->execute()) {
            return USER_CREATED;
        } else {
            return USER_CREATION_FAILED;
        }
    }


    function getUserByEmail1($username, $password, $role)
    {
        $stmt = $this->con->prepare("SELECT id from login WHERE username = ? AND password = ? AND role = ?");
        $stmt->bind_param("sss", $username, $password, $role);
        $stmt->execute();
        $stmt->bind_result($id);
        $stmt->fetch();
        return $id;
    }



    function registerUser($username, $password, $role, $token)
    {

        if (!$this->isUserExist($email)) {
            $stmt = $this->con->prepare("INSERT INTO `login`(`username`, `password`, `role` , token) VALUES (?, ?, ? , ?)");
            $stmt->bind_param("ssss", $username, $password, $role, $token);
            if ($stmt->execute()) {
                return USER_CREATED;
            } else {
                return USER_CREATION_FAILED;
            }
        }
        return USER_EXIST;
    }
    function isUserExist($username)
    {
        $stmt = $this->con->prepare("SELECT id FROM login WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    function create_profile($id1, $first_name, $last_name, $address, $cnic, $phoneno, $img)
    {
        date_default_timezone_set("America/Los_Angeles");
        $time = date("ymd");
        $id = '_' . $time . '_' . microtime(true);
        $upload_path = "../images/$id.jpg";
        $upload = substr($upload_path, 3);
        $stmt = $this->con->prepare("INSERT INTO `profile`(`id`, `first_name`, `last_name`, `address`, `cnic`, `phoneno`, `img`) VALUES(?, ?, ? , ? , ? , ? , ?)");
        $stmt->bind_param("isssiis", $id1, $first_name, $last_name, $address, $cnic, $phoneno, $upload);
        if ($stmt->execute()) {
            file_put_contents($upload_path, base64_decode($img));
            return USER_CREATED;
        } else {
            return USER_CREATION_FAILED;
        }
    }


    function create_profile_social($u_id)
    {

        if (!$this->isUserExistsocial($u_id)) {
            $stmt = $this->con->prepare("INSERT INTO `profile`(`id`) VALUES(?)");
            $stmt->bind_param("s", $u_id);
            if ($stmt->execute()) {

                return USER_CREATED;
            } else {
                return USER_CREATION_FAILED;
            }

        }
    }

    function getprofilesocial($u_id)
    {
        $stmt = $this->con->prepare("SELECT `pr_id`, `id`, `first_name`, `last_name`, `address`, `cnic`, `phoneno`, `img` FROM `profile` WHERE `id` = ? ;");
        $stmt->bind_param("s", $u_id);
        $stmt->execute();
        $stmt->bind_result($pr_id, $id, $first_name, $last_name, $address, $cnic, $phoneno, $img);
        $stmt->fetch();
        if ($img == null)
            $imgurl = 'https://' . 'Learn2earnn.com' . '/hospital/' . $img;
        else
            $imgurl = 'https://' . 'Learn2earnn.com' . '/hospital/' . $img;
        $profile = array();
        $profile['pr_id'] = $pr_id;
        $profile['id'] = $id;
        $profile['first_name'] = $first_name;
        $profile['last_name'] = $last_name;
        $profile['address'] = $address;
        $profile['cnic'] = $cnic;
        $profile['phoneno'] = $phoneno;
        $profile['profile_image'] = $imgurl;
        return $profile;




    }

    function isUserExistsocial($u_id)
    {
        $stmt = $this->con->prepare("SELECT id FROM profile WHERE id = ?");
        $stmt->bind_param("s", $u_id);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }



    function getprofilebyid($id)
    {
        $stmt = $this->con->prepare("SELECT `pr_id`, `id`, `first_name`, `last_name`, `address`, `cnic`, `phoneno`, `img` FROM `profile` WHERE `id` = ? ;");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($pr_id, $id, $first_name, $last_name, $address, $cnic, $phoneno, $img);
        $stmt->fetch();
        if ($img == null)
            $imgurl = 'https://' . 'Learn2earnn.com' . '/hospital/' . $img;
        else
            $imgurl = 'https://' . 'Learn2earnn.com' . '/hospital/' . $img;
        $profile = array();
        $profile['pr_id'] = $pr_id;
        $profile['id'] = $id;
        $profile['first_name'] = $first_name;
        $profile['last_name'] = $last_name;
        $profile['address'] = $address;
        $profile['cnic'] = $cnic;
        $profile['phoneno'] = $phoneno;
        $profile['profile_image'] = $imgurl;
        return $profile;
    }

    function getprofilebyid1($pr_id)
    {
        $stmt = $this->con->prepare("SELECT `pr_id`, `id`, `first_name`, `last_name`, `address`, `cnic`, `phoneno`, `img` FROM `profile` WHERE `pr_id` = ? ;");
        $stmt->bind_param("i", $pr_id);
        $stmt->execute();
        $stmt->bind_result($pr_id, $id, $first_name, $last_name, $address, $cnic, $phoneno, $img);
        $stmt->fetch();
        if ($img == null)
            $imgurl = 'https://' . 'Learn2earnn.com' . '/hospital/' . $img;
        else
            $imgurl = 'https://' . 'Learn2earnn.com' . '/hospital/' . $img;
        $profile = array();
        $profile['pr_id'] = $pr_id;
        $profile['id'] = $id;
        $profile['first_name'] = $first_name;
        $profile['last_name'] = $last_name;
        $profile['address'] = $address;
        $profile['cnic'] = $cnic;
        $profile['phoneno'] = $phoneno;
        $profile['profile_image'] = $imgurl;
        return $profile;
    }

    function profileupdate($pr_id, $first_name, $last_name, $address, $cnic, $phoneno, $img)
    {
        if (isset($img)) {
            date_default_timezone_set("Asia/Karachi");
            $time = date("ymd");
            $id = $pr_id . '_' . $time . '_' . microtime(true);
            $upload_path = "../images/$id.jpg";
            $upload = substr($upload_path, 3);
        } else
            $upload_path = null;
        $stmt = $this->con->prepare("UPDATE `profile` SET `first_name`=?,`last_name`=?,`address`=? ,`cnic`=?,`phoneno`=?,`img`= ? WHERE `pr_id`= ?");
        $stmt->bind_param("sssiisi", $first_name, $last_name, $address, $cnic, $phoneno, $upload, $pr_id);
        if ($stmt->execute()) {
            file_put_contents($upload_path, base64_decode($img));
            return PROFILE_UPDATED;
        }
        return PROFILE_NOT_UPDATED;
    }

    //doctor details

    function doctordetail($dp_id, $name, $email, $age, $gender, $fees, $d_no, $speciality, $username, $password)
    {
        $stmt = $this->con->prepare("INSERT INTO `doctor`(`dp_id`, `name`, `email`, `age`, `gender`, `fees`, `d_no`, `speciality`, `username`, `password`) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ississssss", $dp_id, $name, $email, $age, $gender, $fees, $d_no, $speciality, $username, $password);
        if ($stmt->execute()) {
            $stmt = $this->con->prepare("SELECT MAX(d_id) as `d_id` FROM doctor");
            $stmt->execute();
            $stmt->bind_result($d_id);
            $stmt->fetch();
            return $d_id;
        }
        return PROFILE_NOT_CREATED;
    }

    function add_doctordetail($d_id, $day, $from_time, $to_time)
    {
        $stmt = $this->con->prepare("INSERT INTO `doctor_detail`(`d_id`, `day`, `from_time`, `to_time`) VALUES (?,?,?,?)");
        $stmt->bind_param("isss", $d_id, $day, $from_time, $to_time);
        if ($stmt->execute()) {
            return ORDER_PLACED;
        } else {
            return ORDER_NOT_PLACED;
        }
    }

    function add_stafflogin($username, $password)
    {   
        $role = "doctor";
        $stmt = $this->con->prepare("INSERT INTO `login`(`username`, `password`, `role`) VALUES (?,?,?)");
        $stmt->bind_param("sss", $username, $password, $role);
        if ($stmt->execute()) {
            return ORDER_PLACED;
        } else {
            return ORDER_NOT_PLACED;
        }
    }
    //get

    function getdoctor()
    {
        $stmt = $this->con->prepare("SELECT doctor.d_id, doctor.dp_id, doctor.name, doctor.email, doctor.age, doctor.gender, doctor.fees, doctor.d_no, doctor.speciality, doctor.username, doctor.password, department.dp_name, department.dp_description FROM `doctor` JOIN department ON doctor.dp_id = department.dp_id");
        $stmt->execute();
        $stmt->bind_result($d_id, $dp_id, $name, $email, $age, $gender, $fees, $d_no, $speciality, $username, $password, $dp_name, $dp_description);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['d_id'] = $d_id;
            $test['dp_id'] = $dp_id;
            $test['name'] = $name;
            $test['email'] = $email;
            $test['age'] = $age;
            $test['gender'] = $gender;
            $test['fees'] = $fees;
            $test['d_no'] = $d_no;
            $test['speciality'] = $speciality;
            $test['username'] = $username;
            $test['password'] = $password;
            $test['dp_name'] = $dp_name;
            $test['dp_description'] = $dp_description;
            array_push($cat, $test);
        }
        return $cat;
    }


    function getdoctordetails($d_id)
    {
        $stmt = $this->con->prepare("SELECT `d_id`,`day`,`from_time`,`to_time` FROM `doctor_detail` WHERE `d_id` = $d_id");
        $stmt->execute();
        $stmt->bind_result($d_id, $day, $from_time, $to_time);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['d_id'] = $d_id;
            $test['day'] = $day;
            $test['from_time'] = $from_time;
            $test['to_time'] = $to_time;

            array_push($cat, $test);
        }
        return $cat;
    }

    
    function updatedoctorlogin($username, $password, $oldusername)
    {
        $stmt = $this->con->prepare("UPDATE `login` SET `username`=?,`password`=? WHERE username=?");
        $stmt->bind_param("sss", $username, $password, $oldusername);
        if ($stmt->execute()) {
            $stmt = $this->con->prepare("UPDATE `doctor` SET `username`=?,`password`=? WHERE username=?");
            $stmt->bind_param("sss", $username, $password, $oldusername);
            if ($stmt->execute()) { 
                return PROFILE_CREATED;
            }
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }



    //delete

    function deletedoctor($id)
    {
        $stmt = $this->con->prepare("DELETE FROM `doctor` Where d_id = ? ");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return PROFILE_DELETED;
        }
        return PROFILE_NOT_DELETED;
    }




    //update

    function updatedoctordetail($id, $name, $email, $age, $gender, $fees, $d_no)
    {
        $stmt = $this->con->prepare("UPDATE `doctor` SET `name`= ?,`email`=?,`age`=?,`gender`=?,`fees`=?,`d_no`=? WHERE `d_id` =?");
        $stmt->bind_param("ssisssi", $name, $email, $age, $gender, $fees, $d_no, $id);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }







    //Add Staff 

    function addStaff($name, $no, $gender, $age, $catagory, $shedule, $salary)
    {
        $stmt = $this->con->prepare("INSERT INTO `staff`( `name`, `no`, `age`, `gender`, `catagory`, `schedule`, `salary`) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("ssisssi", $name, $no, $gender, $age, $catagory, $shedule, $salary);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }
    // Update Staff
    function updateStaff($id, $name, $no, $gender, $age, $catagory, $shedule, $salary)
    {
        $stmt = $this->con->prepare("UPDATE `staff` SET `name`= ?,`no`= ?,`age`= ?,`gender`= ?,`catagory`= ?,`schedule`= ?,`salary`= ? WHERE `id`=?");
        $stmt->bind_param("ssisssii", $name, $no, $age, $gender,  $catagory, $shedule, $salary,$id);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }

    //get

    function getnursedetail()
    {
        $stmt = $this->con->prepare("SELECT * FROM `Nurse`");
        $stmt->execute();
        $stmt->bind_result($id, $n_name, $n_age, $n_gender, $n_no, $n_email);
        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['id'] = $id;
            $test['n_name'] = $n_name;
            $test['n_age'] = $n_age;
            $test['n_gender'] = $n_gender;
            $test['n_no'] = $n_no;
            $test['n_email'] = $n_email;
            array_push($cat, $test);
        }
        return $cat;
    }



    //delete

    function deletenurse($id)
    {
        $stmt = $this->con->prepare("DELETE FROM `Nurse` Where id = ? ");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return PROFILE_DELETED;
        }
        return PROFILE_NOT_DELETED;
    }





    //staff details


    function staffdetail($name, $no, $age, $gender, $catagory, $schedule, $salary)
    {
        $stmt = $this->con->prepare("INSERT INTO `staff`(`name`, `no`, `age`, `gender`, `catagory`, `schedule`, `salary`) VALUES  (?,?,?,?,?,?,?)");
        $stmt->bind_param("ssisssi", $name, $no, $age, $gender, $catagory, $schedule, $salary);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }

    //get


    function getstaffdetail()
    {
        $stmt = $this->con->prepare("SELECT * FROM `staff`");
        $stmt->execute();
        $stmt->bind_result($id, $name, $no, $age, $gender, $catagory, $schedule, $salary);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['id'] = $id;
            $test['name'] = $name;
            $test['no'] = $no;
            $test['age'] = $age;
            $test['gender'] = $gender;
            $test['catagory'] = $catagory;
            $test['schedule'] = $schedule;
            $test['salary'] = $salary;
            array_push($cat, $test);
        }
        return $cat;
    }



    //delete

    function deletestaff($id)
    {
        $stmt = $this->con->prepare("DELETE FROM `staff` Where id = ? ");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return PROFILE_DELETED;
        }
        return PROFILE_NOT_DELETED;
    }


    //STAFF ROSATER



    function add_roaster($id, $shift, $month, $year)
    {

        $stmt = $this->con->prepare("INSERT INTO `staff_roaster`(`id`, `shift`, `month`, `year`) VALUES (?,?,?,?)");
        $stmt->bind_param("isss", $id, $shift, $month, $year);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }

    //get roaster

    function get_roaster($month, $year)
    {
        $stmt = $this->con->prepare("SELECT staff_roaster.* , staff.name , staff.catagory FROM `staff_roaster` join staff on staff_roaster.id = staff.id  WHERE staff_roaster.month = ? AND staff_roaster.year = ?");
        $stmt->bind_param("ss", $month, $year);
        $stmt->execute();
        $stmt->bind_result($id, $shift, $month, $year, $other, $name, $catagory);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['id'] = $id;
            $test['shift'] = $shift;
            $test['month'] = $month;
            $test['year'] = $year;
            $test['other'] = $other;
            $test['name'] = $name;
            $test['category'] = $catagory;
            array_push($cat, $test);
        }
        return $cat;
    }

    // update roaster

    function update_roaster($id, $month, $year, $shift)
    {
        $stmt = $this->con->prepare("UPDATE `staff_roaster` SET `shift`= ? WHERE `id` = ? AND `month` = ? AND `year` = ?");
        $stmt->bind_param("siss", $shift, $id, $month, $year);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }

    // mark attendence


    function mark_attendence($id, $replacement_id, $replacement_name, $attendence)
    {
        date_default_timezone_set("Asia/Karachi");
        $time = date("ymd");
        $stmt = $this->con->prepare("INSERT INTO `staff_attendence`(`id`, `replacement_id`, replacement_name , `attendence`, `date`) VALUES (?,?,?,?,?)");
        $stmt->bind_param("iisss", $id, $replacement_id, $replacement_name, $attendence, $time);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }

    //update attendence

    function update_attendence($id, $replacement_id, $replacement_name, $attendence, $date)
    {
        $stmt = $this->con->prepare("UPDATE `staff_attendence` SET `replacement_id`= ? ,`replacement_name`=?,`attendence`=? WHERE `id` = ? AND date = ?");
        $stmt->bind_param("issis", $replacement_id, $replacement_name, $attendence, $id, $date);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }

    //
    function get_attendence()
    {
        date_default_timezone_set("Asia/Karachi");
        $time = date("ymd");
        $stmt = $this->con->prepare("SELECT `staff_attendence`.`id`, `staff_attendence`.`replacement_id`, `staff_attendence`.`replacement_name`, `staff_attendence`.`attendence`, `staff_attendence`.`date`, staff.name FROM `staff_attendence` join staff on `staff_attendence`.id = staff.id WHERE `date` = ?");
        $stmt->bind_param("s", $time);
        $stmt->execute();
        $stmt->bind_result($id, $replacement_id, $replacement_name, $attendence, $date, $name);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['id'] = $id;
            $test['replacement_id'] = $replacement_id;
            $test['replacement_name'] = $replacement_name;
            $test['attendence'] = $attendence;
            $test['date'] = $date;
            $test['name'] = $name;


            array_push($cat, $test);
        }
        return $cat;
    }

    // attendence_detail

    function attendence_detail($id)
    {

        $stmt = $this->con->prepare("SELECT `staff_attendence`.`id`, `staff_attendence`.`replacement_id`, `staff_attendence`.`replacement_name`, `staff_attendence`.`attendence`, `staff_attendence`.`date`, staff.name FROM `staff_attendence` join staff on `staff_attendence`.id = staff.id WHERE staff_attendence.`id` = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($id, $replacement_id, $replacement_name, $attendence, $date, $name);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['id'] = $id;
            $test['replacement_id'] = $replacement_id;
            $test['replacement_name'] = $replacement_name;
            $test['attendence'] = $attendence;
            $test['date'] = $date;
            $test['name'] = $name;


            array_push($cat, $test);
        }
        return $cat;
    }

    // attendence details datewise

    function get_attendence_date($id, $start, $end)
    {

        $stmt = $this->con->prepare("SELECT `staff_attendence`.`id`, `staff_attendence`.`replacement_id`, `staff_attendence`.`replacement_name`, `staff_attendence`.`attendence`, `staff_attendence`.`date`, staff.name FROM `staff_attendence` join staff on `staff_attendence`.id = staff.id WHERE staff_attendence.`id` = ? AND staff_attendence.date BETWEEN ? and ?");
        $stmt->bind_param("iss", $id, $start, $end);
        $stmt->execute();
        $stmt->bind_result($id, $replacement_id, $replacement_name, $attendence, $date, $name);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['id'] = $id;
            $test['replacement_id'] = $replacement_id;
            $test['replacement_name'] = $replacement_name;
            $test['attendence'] = $attendence;
            $test['date'] = $date;
            $test['name'] = $name;


            array_push($cat, $test);
        }
        return $cat;
    }

    //room Api's
    function addroom($room_no, $charges)
    {
        $status = 'pending';
        $stmt = $this->con->prepare("INSERT INTO `room_detail`(`room_no`, `charges`, `status`) VALUES (?,?,?)");
        $stmt->bind_param("sis", $room_no, $charges, $status);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }

// Update

function updateRoom($rd_id,$r_id, $room_no, $charges,$status)
{
    
    $stmt = $this->con->prepare("INSERT INTO `room_detail`(`r_id`, `room_no`, `charges`, `status`) VALUES (?,?,?,?)");
    $stmt->bind_param("isis", $r_id, $room_no, $charges, $status,$rd_id);
    if ($stmt->execute()) {
        return PROFILE_CREATED;
    }
    return PROFILE_NOT_CREATED;
}

    function getroomcat()
    {
        $stmt = $this->con->prepare("SELECT * FROM `room`");
        $stmt->execute();
        $stmt->bind_result($r_id, $r_type);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['r_id'] = $r_id;
            $test['r_type'] = $r_type;
            array_push($cat, $test);
        }
        return $cat;
    }


    function getroomdetail($r_id)
    {
        $stmt = $this->con->prepare("SELECT * FROM `room_detail` WHERE r_id = ?");
        $stmt->bind_param("i", $r_id);
        $stmt->execute();
        $stmt->bind_result($rd_id,$r_id, $room_no, $charges, $status);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['rd_id'] = $rd_id;
            $test['r_id'] = $r_id;
            $test['room_no'] = $room_no;
            $test['charges'] = $charges;
            $test['status'] = $status;

            array_push($cat, $test);
        }
        return $cat;
    }
    function getroomdetailbyrid($r_id)
    {
        $stmt = $this->con->prepare("SELECT * FROM `room_detail` WHERE r_id = ? AND status = 'pending'");
        $stmt->bind_param("i", $r_id);
        $stmt->execute();
        $stmt->bind_result($rd_id,$r_id, $room_no, $charges, $status);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['rd_id'] = $rd_id;
            $test['r_id'] = $r_id;
            $test['room_no'] = $room_no;
            $test['charges'] = $charges;
            $test['status'] = $status;

            array_push($cat, $test);
        }
        return $cat;
    }
    // Room Cat
    function getroomdetails()
    {
        $stmt = $this->con->prepare("SELECT * FROM `room_detail`");
        $stmt->execute();
        $stmt->bind_result($rd_id,$r_id, $room_no, $charges, $status);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['rd_id'] = $rd_id;
            $test['r_id'] = $r_id;
            $test['room_no'] = $room_no;
            $test['charges'] = $charges;
            $test['status'] = $status;

            array_push($cat, $test);
        }
        return $cat;
    }

    // Pending Rooms
    function getpendingrooms($r_id)
    {
        $stmt = $this->con->prepare("SELECT * FROM `room_detail` WHERE r_id = ? && status = 'pending'");
        $stmt->bind_param("i", $r_id);
        $stmt->execute();
        $stmt->bind_result($rd_id,$r_id, $room_no, $charges, $status);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['rd_id'] = $rd_id;
            $test['r_id'] = $r_id;
            $test['room_no'] = $room_no;
            $test['charges'] = $charges;
            $test['status'] = $status;

            array_push($cat, $test);
        }
        return $cat;
    }
    //doctor appointment

    function appointment($p_name, $p_no, $p_age, $u_id, $doc_id, $p_gender, $fees, $type, $appointment_no)
    {
        $status = 'pending';
        date_default_timezone_set("Asia/Karachi");
        $time = date("ymd");

        $stmt = $this->con->prepare("INSERT INTO `appointment`(`p_name`, `p_no`, `p_age`, u_id ,  `doc_id` , `p_gender`, `time`, `fees`, `status`, `type`, `appointment_no` ) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssiiisssssi", $p_name, $p_no, $p_age, $u_id, $doc_id, $p_gender, $time, $fees, $status, $type, $appointment_no);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }

    //get


    function getappointment()
    {
        date_default_timezone_set("Asia/Karachi");
        $time = date("ymd");

        $stmt = $this->con->prepare("SELECT appointment.id as id , appointment.p_name as p_name , appointment.p_no as p_no , appointment.p_age as p_age , appointment.u_id as u_id , appointment.doc_id as doc_id , appointment.p_gender as p_gender , appointment.time as time , appointment.fees as fee , appointment.status as status , appointment.type as type , appointment.appointment_no as appointment_no , login.username as username , doctor.name as doctor_name FROM appointment JOIN login on appointment.u_id = login.id JOIN doctor on appointment.doc_id = doctor.d_id where time = ?");
        $stmt->bind_param("s", $time);
        $stmt->execute();
        $stmt->bind_result($id, $p_name, $p_no, $p_age, $u_id, $doc_id, $p_gender, $time, $fees, $status, $type, $appointment_no, $username, $doctor_name);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['id'] = $id;
            $test['p_name'] = $p_name;
            $test['p_no'] = $p_no;
            $test['p_age'] = $p_age;
            $test['u_id'] = $u_id;
            $test['doc_id'] = $doc_id;
            $test['p_gender'] = $p_gender;
            $test['time'] = $time;
            $test['fees'] = $fees;
            $test['status'] = $status;
            $test['type'] = $type;
            $test['appointment_no'] = $appointment_no;
            $test['username'] = $username;
            $test['doctor_name'] = $doctor_name;
            array_push($cat, $test);
        }
        return $cat;
    }



    function getappointmentbyid($id)
    {

        $stmt = $this->con->prepare("SELECT appointment.id as id , appointment.p_name as p_name , appointment.p_no as p_no , appointment.p_age as p_age , appointment.u_id as u_id , appointment.doc_id as doc_id , appointment.p_gender as p_gender , appointment.time as time , appointment.fees as fee , appointment.status as status , appointment.type as type , appointment.appointment_no as appointment_no , login.username as username , doctor.name as doctor_name FROM appointment JOIN login on appointment.u_id = login.id JOIN doctor on appointment.doc_id = doctor.d_id where u_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($id, $p_name, $p_no, $p_age, $u_id, $doc_id, $p_gender, $time, $fees, $status, $type, $appointment_no, $username, $doctor_name);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['id'] = $id;
            $test['p_name'] = $p_name;
            $test['p_no'] = $p_no;
            $test['p_age'] = $p_age;
            $test['u_id'] = $u_id;
            $test['doc_id'] = $doc_id;
            $test['p_gender'] = $p_gender;
            $test['time'] = $time;
            $test['fees'] = $fees;
            $test['status'] = $status;
            $test['type'] = $type;
            $test['appointment_no'] = $appointment_no;
            $test['username'] = $username;
            $test['doctor_name'] = $doctor_name;
            array_push($cat, $test);
        }
        return $cat;
    }

    function getappointmentbyd_id($d_id)
    {

        $stmt = $this->con->prepare("SELECT appointment.id as id , appointment.p_name as p_name , appointment.p_no as p_no , appointment.p_age as p_age , appointment.u_id as u_id , appointment.doc_id as doc_id , appointment.p_gender as p_gender , appointment.time as time , appointment.fees as fee , appointment.status as status , appointment.type as type , appointment.appointment_no as appointment_no , login.username as username , doctor.name as doctor_name FROM appointment JOIN login on appointment.u_id = login.id JOIN doctor on appointment.doc_id = doctor.d_id where appointment.doc_id = ?");
        $stmt->bind_param("i", $d_id);
        $stmt->execute();
        $stmt->bind_result($id, $p_name, $p_no, $p_age, $u_id, $doc_id, $p_gender, $time, $fees, $status, $type, $appointment_no, $username, $doctor_name);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['id'] = $id;
            $test['p_name'] = $p_name;
            $test['p_no'] = $p_no;
            $test['p_age'] = $p_age;
            $test['u_id'] = $u_id;
            $test['doc_id'] = $doc_id;
            $test['p_gender'] = $p_gender;
            $test['time'] = $time;
            $test['fees'] = $fees;
            $test['status'] = $status;
            $test['type'] = $type;
            $test['appointment_no'] = $appointment_no;
            $test['username'] = $username;
            $test['doctor_name'] = $doctor_name;
            array_push($cat, $test);
        }
        return $cat;
    }



    function updateappointment($id, $p_name, $p_no, $p_age, $doc_id, $p_gender, $time, $fees)
    {
        $stmt = $this->con->prepare("UPDATE `appointment` SET `p_name`= ? ,`p_no`= ?,`p_age`= ?,`doc_id`= ? ,`p_gender`= ? ,`time`= ? ,`fees`= ? WHERE id = ?");
        $stmt->bind_param("siiissii", $p_name, $p_no, $p_age, $doc_id, $p_gender, $time, $fees, $id);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }

    function updateappointstatus($id)
    {
        $status = 'confirm';
        $stmt = $this->con->prepare("UPDATE `appointment` SET `status`= ?  WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }

    //delete

    function deleteappointmentuser($u_id, $id)
    {
        $stmt = $this->con->prepare("DELETE FROM `appointment` Where id = ? AND u_id = ? AND status = 'confirm'");
        $stmt->bind_param("ii", $id, u_id);
        if ($stmt->execute()) {
            return PROFILE_DELETED;
        }
        return PROFILE_NOT_DELETED;
    }


    function deleteappointment($id)
    {
        $stmt = $this->con->prepare("DELETE FROM `appointment` Where id = ? ");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return PROFILE_DELETED;
        }
        return PROFILE_NOT_DELETED;
    }


    function addoperatecategory($operate_name, $operate_amount)
    {
        $stmt = $this->con->prepare("INSERT INTO `operate_category`(`operate_name`, `operate_amount`) VALUES (?,?)");
        $stmt->bind_param("si", $operate_name, $operate_amount);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }

    function getoperatecategory()
    {
        $stmt = $this->con->prepare("SELECT `op_id`, `operate_name`, `operate_amount` FROM `operate_category`");
        $stmt->execute();
        $stmt->bind_result($op_id, $operate_name, $operate_amount);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['op_id'] = $op_id;
            $test['operate_name'] = $operate_name;
            $test['operate_amount'] = $operate_amount;

            array_push($cat, $test);
        }
        return $cat;
    }

    
    function deleteoperatecategory($op_id)
    {
        $stmt = $this->con->prepare("DELETE FROM `operate_category` WHERE `op_id` = ?");
        $stmt->bind_param("i", $op_id);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }

    function updateoperatecategory($operate_name, $operate_amount, $op_id)
    {
        $stmt = $this->con->prepare("UPDATE `operate_category` SET `operate_name`=?,`operate_amount`=? WHERE `op_id`=?");
        $stmt->bind_param("sii", $operate_name, $operate_amount, $op_id);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }



    //operate data

    function operate($type,$pid, $p_name, $p_age, $p_no, $d_id, $r_id, $room_no, $charges, $d_fees, $p_gender, $description, $advance, $remaining_amount)
    {
        date_default_timezone_set("Asia/Karachi");
        $time = date("ymd");
        $opstatus = 'pending';
        $stmt = $this->con->prepare("INSERT INTO `operate`( `type`, `pid`, `p_name`, `p_age`, `p_no`, `d_id`, `r_id`, `room_no`, `charges`, `d_fees`, `date`, `p_gender`, `status`, `description`, `advance`, `remaining_amount`) VALUES(?,?,?,?,?,?,?,?,?,?,?,? ,? , ? ,?,?)");
        $stmt->bind_param("sisisiiiisssssii", $type,$pid, $p_name, $p_age, $p_no, $d_id, $r_id, $room_no, $charges, $d_fees, $time, $p_gender, $opstatus, $description, $advance, $remaining_amount);
        if ($stmt->execute()) {
            $status = 'confirm';
            $stmt = $this->con->prepare("UPDATE `room_detail` SET `status`= ? WHERE `rd_id` = ? AND `room_no` = ?");
            $stmt->bind_param("sii", $status, $r_id, $room_no);
            if ($stmt->execute()) {
                return PROFILE_CREATED;
            }
            return PROFILE_NOT_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }
    //operate data

    function addPatientOperate($name, $age, $gender, $blood, $address, $cnic, $phone, $fh_name)
    {
        $stmt = $this->con->prepare("INSERT INTO `patients`(`name`, `age`, `gender`, `blood`, `address`, `cnic`, `phone`, `fh_name`) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sissssss", $name, $age, $gender, $blood, $address, $cnic, $phone, $fh_name);
        if ($stmt->execute()) {
            $stmt = $this->con->prepare("SELECT MAX(pid) as `pid` FROM patients");
            $stmt->execute();
            $stmt->bind_result($pid);
            $stmt->fetch();
            return $pid;
        }
        return PROFILE_NOT_CREATED;
    }

    //get

    function getoperation()
    {
        $stmt = $this->con->prepare("SELECT operate.id as id , operate.p_name as p_name , operate.p_age as p_age,operate.p_no as p_no , operate.d_fees as d_fee , operate.date as time , operate.p_gender as p_gender, operate.description as opdescription, operate.remaining_amount as remaining_amount , room_detail.r_id as r_id , room_detail.room_no , doctor.name as drname FROM `operate` join room_detail on operate.r_id =room_detail.r_id AND operate.room_no = room_detail.room_no join doctor on operate.d_id = doctor.d_id");
        $stmt->execute();
        $stmt->bind_result($id, $p_name, $p_age, $p_no, $d_fees, $time, $p_gender, $opdescription, $remaining_amount, $r_id, $room_no, $drname);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['id'] = $id;
            $test['p_name'] = $p_name;
            $test['p_age'] = $p_age;
            $test['p_no'] = $p_no;
            $test['d_fees'] = $d_fees;
            $test['time'] = $time;
            $test['p_gender'] = $p_gender;
            $test['opdescription'] = $opdescription;
            $test['remaining_amount'] = $remaining_amount;
            $test['r_id'] = $r_id;
            $test['room_no'] = $room_no;
            $test['drname'] = $drname;

            array_push($cat, $test);
        }
        return $cat;
    }


    //delete operate

    function deleteoperate($id, $rd_id)
    {
        $stmt = $this->con->prepare("DELETE FROM `operate` Where id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $status = "pending";
            $stmt = $this->con->prepare("UPDATE `room_detail` SET `status`=? WHERE `rd_id`=?");
            $stmt->bind_param("si", $status, $rd_id);
            if ($stmt->execute()) {
                return PROFILE_CREATED;
            } else {
                return PROFILE_NOT_CREATED;
            }
        }
        return PROFILE_NOT_CREATED;
    }



    //surgery data

    function surgery($p_name, $p_age, $p_no, $p_gender, $d_id, $time, $d_fees)
    {
        $stmt = $this->con->prepare("INSERT INTO `surgery`(`p_name`, `p_age`, `p_gender`, `p_no`, `d_id`, `time`, `d_fees`) VALUES   (?,?,?,?,?,?,?)");
        $stmt->bind_param("sississ", $p_name, $p_age, $p_no, $p_gender, $d_id, $time, $d_fees);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }

    //get

    function getsurgery()
    {
        $stmt = $this->con->prepare("SELECT * FROM `surgery`");
        $stmt->execute();
        $stmt->bind_result($id, $p_name, $p_age, $p_no, $p_gender, $d_id, $time, $d_fees);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['id'] = $id;
            $test['p_name'] = $p_name;
            $test['p_age'] = $p_age;
            $test['p_no'] = $p_no;
            $test['p_gender'] = $p_gender;
            $test['d_id'] = $d_id;
            $test['time'] = $time;
            $test['d_fees'] = $d_fees;
            array_push($cat, $test);
        }
        return $cat;
    }



    //delete

    function deletesurgery($id)
    {
        $stmt = $this->con->prepare("DELETE FROM `surgery` Where id = ? ");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return PROFILE_DELETED;
        }
        return PROFILE_NOT_DELETED;
    }






    //update

    function updt1($u_id, $name, $username, $password)
    {
        $stmt = $this->con->prepare("UPDATE `test2` SET `name`= ?, `username`= ? ,`password`= ? WHERE u_id =?");
        $stmt->bind_param("sssi", $name, $username, $password, $u_id);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }

    // join //





    function getsummary_date($start, $end)
    {
        $stmt = $this->con->prepare("SELECT COUNT(`status`) as total FROM `appointment` WHERE `status` = 'confirm' AND `time` between ? and ?");
        $stmt->bind_param("ss", $start, $end);
        $stmt->execute();
        $stmt->bind_result($total);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['total'] = $total;
            $test['title'] = 'Appointment';
            array_push($cat, $test);
        }
        //$stmt = $this->con->prepare ("SELECT COUNT(`status`) as total FROM `operate` WHERE `status` = 'confirmed' AND `time` between ? and ?");
//$stmt->bind_param("ss", $start , $end);
//$stmt->execute();
//$stmt->bind_result($total);

        //       while ($stmt->fetch()) {
        //         $test = array();
        //       $test['operate'] = $total ;
        //     $test['title'] = 'appointment' ;
// array_push($cat, $test);
//}

        $stmt = $this->con->prepare("SELECT COUNT(`attendence`) as attendence_total FROM staff_attendence WHERE `attendence` = 'Present' AND date between ? and ?");
        $stmt->bind_param("ss", $start, $end);
        $stmt->execute();
        $stmt->bind_result($total);

        while ($stmt->fetch()) {
            $test = array();
            $test['total'] = $total;
            $test['title'] = 'Present';
            array_push($cat, $test);
        }

        $stmt = $this->con->prepare("SELECT COUNT(`attendence`) as attendence_total FROM staff_attendence WHERE `attendence` = 'Absent' AND `date` between ? and ?");
        $stmt->bind_param("ss", $start, $end);
        $stmt->execute();
        $stmt->bind_result($total);

        while ($stmt->fetch()) {
            $test = array();
            $test['total'] = $total;
            $test['title'] = 'Absent';
            array_push($cat, $test);
        }


        return $cat;
    }


    function get_summary_detail($ide)
    {
        if ($ide == 1) {
            date_default_timezone_set("Asia/Karachi");
            $time = date("ymd");
            $stmt = $this->con->prepare("SELECT * FROM `appointment` where `status` = 'confirm' AND time = ?");
            $stmt->bind_param("s", $time);
            $stmt->execute();
            $stmt->bind_result($id, $p_name, $p_no, $p_age, $u_id, $doc_id, $p_gender, $time, $fees, $status, $type);

            $cat = array();
            while ($stmt->fetch()) {
                $test = array();
                $test['id'] = $id;
                $test['p_name'] = $p_name;
                $test['p_no'] = $p_no;
                $test['p_age'] = $p_age;
                $test['u_id'] = $u_id;
                $test['doc_id'] = $doc_id;
                $test['p_gender'] = $p_gender;
                $test['time'] = $time;
                $test['fees'] = $fees;
                $test['status'] = $status;
                $test['type'] = $type;
                array_push($cat, $test);
            }
            return $cat;
        }

        if ($ide == 2) {
            date_default_timezone_set("Asia/Karachi");
            $time = date("ymd");
            $stmt = $this->con->prepare("SELECT `staff_attendence`.`id`, `staff_attendence`.`replacement_id`, `staff_attendence`.`replacement_name`, `staff_attendence`.`attendence`, `staff_attendence`.`date`, staff.name FROM `staff_attendence` join staff on `staff_attendence`.id = staff.id WHERE staff_attendence.attendence = 'Present' AND staff_attendence.date = ?");
            $stmt->bind_param("s", $time);
            $stmt->execute();
            $stmt->bind_result($id, $replacement_id, $replacement_name, $attendence, $date, $name);

            $cat = array();
            while ($stmt->fetch()) {
                $test = array();
                $test['ide'] = 2;
                $test['id'] = $id;
                $test['replacement_id'] = $replacement_id;
                $test['replacement_name'] = $replacement_name;
                $test['attendence'] = $attendence;
                $test['date'] = $date;
                $test['name'] = $name;


                array_push($cat, $test);
            }
            return $cat;
        }

        if ($ide == 3) {
            date_default_timezone_set("Asia/Karachi");
            $time = date("ymd");
            $stmt = $this->con->prepare("SELECT `staff_attendence`.`id`, `staff_attendence`.`replacement_id`, `staff_attendence`.`replacement_name`, `staff_attendence`.`attendence`, `staff_attendence`.`date`, staff.name FROM `staff_attendence` join staff on `staff_attendence`.id = staff.id WHERE staff_attendence.attendence = 'Absent' AND staff_attendence.date = ?");
            $stmt->bind_param("s", $time);
            $stmt->execute();
            $stmt->bind_result($id, $replacement_id, $replacement_name, $attendence, $date, $name);

            $cat = array();
            while ($stmt->fetch()) {
                $test = array();
                $test['ide'] = 2;
                $test['id'] = $id;
                $test['replacement_id'] = $replacement_id;
                $test['replacement_name'] = $replacement_name;
                $test['attendence'] = $attendence;
                $test['date'] = $date;
                $test['name'] = $name;


                array_push($cat, $test);
            }
            return $cat;
        }


    }

    function get_summary_detail_date($ide, $id, $start, $end)
    {
        if ($ide == 1) {
            date_default_timezone_set("Asia/Karachi");
            $time = date("ymd");
            $stmt = $this->con->prepare("SELECT * FROM `appointment` where `status` = 'confirm' AND time between ? and ?");
            $stmt->bind_param("ss", $start, $end);
            $stmt->execute();
            $stmt->bind_result($id, $p_name, $p_no, $p_age, $u_id, $doc_id, $p_gender, $time, $fees, $status, $type);

            $cat = array();
            while ($stmt->fetch()) {
                $test = array();
                $test['id'] = $id;
                $test['p_name'] = $p_name;
                $test['p_no'] = $p_no;
                $test['p_age'] = $p_age;
                $test['u_id'] = $u_id;
                $test['doc_id'] = $doc_id;
                $test['p_gender'] = $p_gender;
                $test['time'] = $time;
                $test['fees'] = $fees;
                $test['status'] = $status;
                $test['type'] = $type;
                array_push($cat, $test);
            }
            return $cat;
        }

        if ($ide == 2) {
            date_default_timezone_set("Asia/Karachi");
            $time = date("ymd");
            $stmt = $this->con->prepare("SELECT `staff_attendence`.`id`, `staff_attendence`.`replacement_id`, `staff_attendence`.`replacement_name`, `staff_attendence`.`attendence`, `staff_attendence`.`date`, staff.name FROM `staff_attendence` join staff on `staff_attendence`.id = staff.id WHERE staff_attendence.`date` BETWEEN ? AND ?");
            $stmt->bind_param("ss", $start, $end);
            $stmt->execute();
            $stmt->bind_result($id, $replacement_id, $replacement_name, $attendence, $date, $name);

            $cat = array();
            while ($stmt->fetch()) {
                $test = array();
                $test['ide'] = 1;
                $test['id'] = $id;
                $test['replacement_id'] = $replacement_id;
                $test['replacement_name'] = $replacement_name;
                $test['attendence'] = $attendence;
                $test['date'] = $date;
                $test['name'] = $name;


                array_push($cat, $test);
            }
            return $cat;
        }

        if ($ide == 3) {
            $stmt = $this->con->prepare("SELECT operate.id as id , operate.p_name as p_name , operate.p_age as p_age,operate.p_no as p_no , operate.d_fees as d_fee , operate.time as time , operate.p_gender as p_gender , operate.status as opstatus , operate.description as opdescription, room.r_number as room_no , room.r_type, room.r_charges as charges , room_detail.bed_no as bed_no , room_detail.status as status , doctor.name as drname FROM `operate` join room on operate.r_id =room.r_id JOIN room_detail on room.r_id = room_detail.r_id join doctor on operate.d_id = doctor.d_id AND operate.date BETWEEN ? AND ?");
            $stmt->bind_param("ss", $start, $end);
            $stmt->execute();
            $stmt->bind_result($id, $p_name, $p_age, $p_no, $d_fees, $time, $p_gender, $opstatus, $opdescriptionr, $room_no, $r_type, $charges, $bed_no, $status, $drname);

            $cat = array();
            while ($stmt->fetch()) {
                $test = array();
                $test['ide'] = 3;
                $test['id'] = $id;
                $test['p_name'] = $p_name;
                $test['p_age'] = $p_age;
                $test['p_no'] = $p_no;
                $test['d_fees'] = $d_fees;
                $test['time'] = $time;
                $test['p_gender'] = $p_gender;
                $test['opstatus'] = $opstatus;
                $test['opdescription'] = $opdescriptionr;
                $test['room_no'] = $room_no;
                $test['r_type'] = $r_type;
                $test['charges'] = $charges;
                $test['bed_no'] = $bed_no;
                $test['status'] = $status;
                $test['drname'] = $drname;

                array_push($cat, $test);
            }
            return $cat;
        }


    }

    // Expense Api.s

    function addexpense($name)
    {
        $stmt = $this->con->prepare("INSERT INTO `expense`(`name`) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }
    function deleteexpense($e_id)
    {
        $stmt = $this->con->prepare("DELETE FROM `expense` WHERE `e_id` = ?");
        $stmt->bind_param("i", $e_id);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }

    function get_expense()
    {
        $stmt = $this->con->prepare("SELECT `e_id`, `name` FROM `expense` ");
        $stmt->execute();
        $stmt->bind_result($e_id, $name);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['e_id'] = $e_id;
            $test['name'] = $name;
            array_push($cat, $test);
        }
        return $cat;
    }



    //expense_detail
    //get_expense_detail

    function add_expense_detail($e_id, $e_description, $e_amount, $e_date)
    {
        $stmt = $this->con->prepare("INSERT INTO `expense_detail`( `e_id`, `e_description`, `e_amount`, `e_date`) VALUES (? ,? ,? ,?)");
        $stmt->bind_param("isis", $e_id, $e_description, $e_amount, $e_date);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }


    function delete_expense_detail($id)
    {
        $status = 'pending';
        $stmt = $this->con->prepare("DELETE FROM `expense_detail` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }
    function get_expense_detail($e_id)
    {
        $stmt = $this->con->prepare("SELECT `id`, `e_id`, `e_description`, `e_amount`, `e_date` FROM `expense_detail` WHERE `e_id` = ?");
        $stmt->bind_param("i", $e_id);
        $stmt->execute();
        $stmt->bind_result($id, $e_id, $e_description, $e_amount, $e_date);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['id'] = $id;
            $test['e_id'] = $e_id;
            $test['e_description'] = $e_description;
            $test['e_amount'] = $e_amount;
            $test['e_date'] = $e_date;

            array_push($cat, $test);
        }
        return $cat;
    }

    // bank Functions

    function insertbank($name, $title, $no)
    {
        $stmt = $this->con->prepare("INSERT INTO `bank`(`name`, `title`, `acct_no`) VALUES  (?,?,?)");
        $stmt->bind_param("ssi", $name, $title, $no);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }

    function insertbankdetail($b_id, $customer_id, $name, $title, $no, $ammount, $credit, $net_balance, $cnet_balance)
    {
        date_default_timezone_set("Asia/Karachi");
        $time = date("ymd");
        $net_balance = $net_balance - $credit;
        $net_balance = $net_balance + $ammount;
        $cnet_balance = $cnet_balance - $credit;
        $cnet_balance = $cnet_balance + $ammount;
        $stmt = $this->con->prepare("INSERT INTO `bank_detail`( `b_id`, `customer_id` , `account_title`, `bank_name`, `account_no`, `receive_ammount`, `credit`, `net_balance`, `date`) VALUES  (?,?,?,?,?,?,?,?,? )");
        $stmt->bind_param("iissiiiis", $b_id, $customer_id, $title, $name, $no, $ammount, $credit, $net_balance, $time);
        if ($stmt->execute()) {
            $stmt = $this->con->prepare("UPDATE `bank` SET `net_balance`= ? WHERE b_id = ?");
            $stmt->bind_param("ii", $net_balance, $b_id);
            if ($stmt->execute()) {
                return PROFILE_CREATED;
            } else {
                return PROFILE_NOT_CREATED;
            }
        }
        return PROFILE_NOT_CREATED;
    }

    function insertbankdetailpur($b_id, $pi_id, $s_id, $name, $title, $no, $ammount, $credit, $net_balance, $cnet_balance)
    {
        date_default_timezone_set("Asia/Karachi");
        $time = date("ymd");
        $net_balance = $net_balance - $credit;
        $net_balance = $net_balance + $ammount;
        $snet_balance = $cnet_balance - $credit;
        $snet_balance = $cnet_balance + $ammount;
        $stmt = $this->con->prepare("INSERT INTO `bank_detail`( `b_id`, `invoice_id`, `customer_id` , `account_title`, `bank_name`, `account_no`, `receive_ammount`, `credit`, `net_balance`, `date`) VALUES  (?,?,?,?,?,?,?,?,?,? )");
        $stmt->bind_param("iiissiiiis", $b_id, $pi_id, $s_id, $title, $name, $no, $ammount, $credit, $net_balance, $time);
        if ($stmt->execute()) {
            $stmt = $this->con->prepare("UPDATE `bank` SET `net_balance`= ? WHERE b_id = ?");
            $stmt->bind_param("ii", $net_balance, $b_id);
            if ($stmt->execute()) {
                return PROFILE_CREATED;
            } else {
                return PROFILE_NOT_CREATED;
            }
        }
        return PROFILE_NOT_CREATED;
    }



    function getbank()
    {
        $stmt = $this->con->prepare("SELECT * FROM bank");
        $stmt->execute();
        $stmt->bind_result($b_id, $name, $title, $acct_no, $net_balance);
        $cat = array();
        while ($stmt->fetch()) {
            $data = array();
            $data['b_id'] = $b_id;
            $data['name'] = $name;
            $data['title'] = $title;
            $data['acct_no'] = $acct_no;
            $data['net_balance'] = $net_balance;
            array_push($cat, $data);
        }
        return $cat;
    }

    function getbankdetail($b_id)
    {
        $stmt = $this->con->prepare("SELECT bank_detail.`id`, bank_detail.`b_id`,bank_detail.invoice_id, bank_detail.`customer_id`, bank_detail.`account_title`, bank_detail.`bank_name`, bank_detail.`account_no`, bank_detail.`receive_ammount`, bank_detail.`credit` , bank_detail.`net_balance`  ,   bank_detail.`date` FROM `bank_detail` WHERE bank_detail.`b_id` = ?");
        $stmt->bind_param("i", $b_id);
        $stmt->execute();
        $stmt->bind_result($id, $b_id, $invoice_id, $customer_id, $account_title, $bank_name, $account_no, $receive_ammount, $credit, $net_balance, $date);
        $cat = array();
        while ($stmt->fetch()) {
            $data = array();
            $data['id'] = $id;
            $data['b_id'] = $b_id;
            $data['invoice_id'] = $invoice_id;
            $data['customer_id'] = $customer_id;
            $data['account_title'] = $account_title;
            $data['bank_name'] = $bank_name;
            $data['account_no'] = $account_no;
            $data['receive_ammount'] = $receive_ammount;
            $data['credit'] = $credit;
            $data['net_balance'] = $net_balance;
            $data['date'] = $date;
            array_push($cat, $data);
        }
        return $cat;
    }

    function delete_bank_detail($id)
    {
        $stmt = $this->con->prepare("DELETE FROM `bank_detail` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }


    //bank summary
    function bank_graph()
    {
        $cat3 = array();
        $stmt = $this->con->prepare("select date_format(date,'%M') as month ,date_format(date,'%Y') as year ,SUM(`credit`) as total_credit from bank_detail group by year(date),month(date) order by year(date),month(date)");
        $stmt->execute();
        $stmt->bind_result($month, $year, $total_credit);
        $cat = array();
        while ($stmt->fetch()) {
            $data = array();
            $data['Month'] = $month;
            $data['Year'] = $year;
            $data['total_credit'] = $total_credit;
            array_push($cat, $data);
        }
        array_push($cat3, $cat);
        $stmt = $this->con->prepare("select date_format(date,'%M') as month ,date_format(date,'%Y') as year ,SUM(`receive_ammount`) as total_debit from bank_detail group by year(date),month(date) order by year(date),month(date)");
        $stmt->execute();
        $stmt->bind_result($month, $year, $total_debit);
        $cat2 = array();
        while ($stmt->fetch()) {
            $data = array();
            $data['Month'] = $month;
            $data['Year'] = $year;
            $data['total_debit'] = $total_debit;
            array_push($cat2, $data);
        }
        array_push($cat3, $cat2);

        return $cat3;
    }



    // graph apis

    function appointment_graph()
    {
        $cat3 = array();
        $stmt = $this->con->prepare("select date_format(time,'%M') as month ,date_format(time,'%Y') as year ,COUNT(p_name) as total from appointment WHERE status = 'confirm' group by year(time),month(time) order by year(time),month(time)");
        $stmt->execute();
        $stmt->bind_result($month, $year, $total);
        $cat = array();
        while ($stmt->fetch()) {
            $data = array();
            $data['Month'] = $month;
            $data['Year'] = $year;
            $data['total_confirm'] = $total;
            array_push($cat, $data);
        }
        array_push($cat3, $cat);
        $stmt = $this->con->prepare("select date_format(time,'%M') as month ,date_format(time,'%Y') as year ,COUNT(p_name) as total from appointment WHERE status = 'pending' group by year(time),month(time) order by year(time),month(time)");
        $stmt->execute();
        $stmt->bind_result($month, $year, $total);
        $cat2 = array();
        while ($stmt->fetch()) {
            $data = array();
            $data['Month'] = $month;
            $data['Year'] = $year;
            $data['total_pending'] = $total;
            array_push($cat2, $data);
        }
        array_push($cat3, $cat2);

        return $cat3;
    }

    function expense_graph()
    {
        $cat3 = array();
        $stmt = $this->con->prepare("select date_format(e_date,'%M') as month ,date_format(e_date,'%Y') as year ,SUM(`e_amount`) as total_ammount from expense_detail group by year(e_date),month(e_date) order by year(e_date),month(e_date)");
        $stmt->execute();
        $stmt->bind_result($month, $year, $total_ammount);
        $cat = array();
        while ($stmt->fetch()) {
            $data = array();
            $data['Month'] = $month;
            $data['Year'] = $year;
            $data['total_ammount'] = $total_ammount;
            array_push($cat, $data);
        }

        return $cat;
    }


    //Client App

    //Signup Profile

    //  Add Patient
    function addPatient($name, $age, $gender, $blood, $address, $cnic, $phone, $fh_name)
    {
        $stmt = $this->con->prepare("INSERT INTO `patients`(`name`, `age`, `gender`, `blood`, `address`, `cnic`, `phone`, `fh_name`) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sissssss", $name, $age, $gender, $blood, $address, $cnic, $phone, $fh_name);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }
    //  Update Patient
    function updatePatient($pid,$name, $age, $gender, $blood, $address, $cnic, $phone, $fh_name)
    {
        $stmt = $this->con->prepare("UPDATE `patients` SET `name`= ?,`age`= ?,`gender`= ?,`blood`= ?,`address`= ?,`cnic`= ?,`phone`= ?,`fh_name`= ? WHERE `pid` = ?");
        $stmt->bind_param("sissssssi", $name, $age, $gender, $blood, $address, $cnic, $phone, $fh_name,$pid);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }
    //  Delete Patient
    function deletePatient($pid)
    {
        $stmt = $this->con->prepare("DELETE FROM `patients` WHERE `pid` = ?");
        $stmt->bind_param("i", $pid);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }

    //  Lab Test
    function addTest($t_name, $amount, $other)
    {
        $stmt = $this->con->prepare("INSERT INTO `lab_test`(`t_name`,`amount`, `other`) VALUES (?,?,?)");
        $stmt->bind_param("sis", $t_name, $amount, $other);
        if ($stmt->execute()) {
            $stmt = $this->con->prepare("SELECT MAX(t_id) as `t_id` FROM lab_test");
            $stmt->execute();
            $stmt->bind_result($t_id);
            $stmt->fetch();
            return $t_id;
        }
        return PROFILE_NOT_CREATED;
    }
    //  Lab Test Variables
    function addTestVariables($t_id, $tv_name, $normal_range, $unit)
    {
        $stmt = $this->con->prepare("INSERT INTO `test_variable`(`t_id`, `tv_name`,`normal_range`,`unit`) VALUES (?,?,?,?)");
        $stmt->bind_param("isss", $t_id, $tv_name, $normal_range, $unit);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }
    //  Lab Test Variables
    function addTestReport($t_id, $p_id, $p_name, $p_address, $p_no, $p_age, $blood, $p_gender, $date, $doc_ref, $status, $amount, $discount)
    {
        $stmt = $this->con->prepare("INSERT INTO `test_report`(`t_id`, `p_id`, `p_name`, `p_address`, `p_no`, `p_age`, `blood`, `p_gender`, `date`, `doc_ref`,`status`,`amount`,`discount`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("iisssisssisii", $t_id, $p_id, $p_name, $p_address, $p_no, $p_age, $blood, $p_gender, $date, $doc_ref, $status,$amount, $discount);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }
    //  Lab Test Result
    function addTestResult($tr_id, $v_name, $normal_range, $result, $unit)
    {
        $stmt = $this->con->prepare("INSERT INTO `test_result`(`tr_id`,`v_name`, `normal_range`, `result`, `unit`) VALUES (?,?,?,?,?)");
        $stmt->bind_param("issss", $tr_id, $v_name, $normal_range, $result, $unit);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }
    // Get

    function getTest()
    {
        $stmt = $this->con->prepare("SELECT * FROM `lab_test`");
        $stmt->execute();
        $stmt->bind_result($t_id, $t_name, $amount, $other);
        $cat = array();
        while ($stmt->fetch()) {
            $data = array();
            $data['t_id'] = $t_id;
            $data['t_name'] = $t_name;
            $data['amount'] = $amount;
            $data['other'] = $other;
            array_push($cat, $data);
        }
        return $cat;
    }
    function getTestVariable($t_id)
    {
        $stmt = $this->con->prepare("SELECT `lab_test`.t_name AS t_name,  `test_variable`.* FROM `test_variable` JOIN lab_test ON `lab_test`.t_id = `test_variable`.t_id WHERE `test_variable`.`t_id` = ?");
        $stmt->bind_param("i", $t_id);
        $stmt->execute();
        $stmt->bind_result($t_name, $tv_id, $t_id, $tv_name, $normal_range, $unit);
        $cat = array();
        while ($stmt->fetch()) {
            $data = array();
            $data['t_name'] = $t_name;
            $data['tv_id'] = $tv_id;
            $data['t_id'] = $t_id;
            $data['tv_name'] = $tv_name;
            $data['normal_range'] = $normal_range;
            $data['unit'] = $unit;
            array_push($cat, $data);
        }
        return $cat;
    }

    // Get Test Template


    function getReportTemplate()
    {
        $stmt = $this->con->prepare("SELECT lab_test.t_name,  test_report.* FROM `test_report` JOIN lab_test ON lab_test.t_id = test_report.t_id");
        $stmt->execute();
        $stmt->bind_result($t_name, $tr_id, $t_id, $p_id, $p_name, $p_address, $p_no, $p_age, $blood, $p_gender, $date, $doc_ref, $status,$amount, $discount);
        $cat = array();
        while ($stmt->fetch()) {
            $data = array();
            $data['t_name'] = $t_name;
            $data['tr_id'] = $tr_id;
            $data['t_id'] = $t_id;
            $data['p_id'] = $p_id;
            $data['p_name'] = $p_name;
            $data['p_address'] = $p_address;
            $data['p_no'] = $p_no;
            $data['p_age'] = $p_age;
            $data['blood'] = $blood;
            $data['p_gender'] = $p_gender;
            $data['date'] = $date;
            $data['doc_ref'] = $doc_ref;
            $data['status'] = $status;
            $data['amount'] = $amount;
            $data['discount'] = $discount;
            array_push($cat, $data);
        }
        return $cat;
    }
    // Get Lab Test Data
    function getTemplate()
    {
        $stmt = $this->con->prepare("SELECT * FROM `lab_test`");
        $stmt->execute();
        $stmt->bind_result($t_id,$t_name, $amount, $other);
        $cat = array();
        while ($stmt->fetch()) {
            $data = array();
            $data['t_id'] = $t_id;
            $data['t_name'] = $t_name;
            $data['amount'] = $amount;
            $data['other'] = $amount;
            array_push($cat, $data);
        }
        return $cat;
    }
    // Update
//  Lab Test Variables
    function updateTestReportStatus($tr_id, $status)
    {
        $stmt = $this->con->prepare("UPDATE `test_report` SET `status`= ? WHERE `tr_id` = ?");
        $stmt->bind_param("si", $status, $tr_id);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }

    //  Get Patients
function getPatients()
{
    $stmt = $this->con->prepare("SELECT * FROM `patients`;");
    $stmt->execute();
    $stmt->bind_result($pid, $name,$age,$gender,$blood,$address,$cnic,$phone,$fh_name);
    $cat = array();
    while ($stmt->fetch()) {
        $data = array();
        $data['pid'] = $pid;
        $data['name'] = $name;
        $data['age'] = $age;
        $data['gender'] = $gender;
        $data['blood'] = $blood;
        $data['address'] = $address;
        $data['cnic'] = $cnic;
        $data['phone'] = $phone;
        $data['fh_name'] = $fh_name;
        array_push($cat, $data);
    }
    return $cat;
}
    //  Get Patients by Pid
function getPatientsByPid($pid)
{
    $stmt = $this->con->prepare("SELECT * FROM `patients` WHERE `pid`=?;");
    $stmt->bind_param("s", $pid);
    $stmt->execute();
    $stmt->bind_result($pid, $name,$age,$gender,$blood,$address,$cnic,$phone,$fh_name);
    $stmt->fetch();
        $data = array();
        $data['pid'] = $pid;
        $data['name'] = $name;
        $data['age'] = $age;
        $data['gender'] = $gender;
        $data['blood'] = $blood;
        $data['address'] = $address;
        $data['cnic'] = $cnic;
        $data['phone'] = $phone;
        $data['fh_name'] = $fh_name;
    return $data;
}

// Get Test Result
function getTestResult($tr_id)
    {   
        $cat1 = array();
        $stmt = $this->con->prepare("SELECT * FROM `test_report` WHERE tr_id =?");
        $stmt->bind_param("i", $tr_id);
        $stmt->execute();
        $stmt->bind_result($tr_id, $t_id, $p_id, $p_name, $p_address, $p_no, $p_age, $blood, $p_gender, $date, $doc_ref, $status,$amount, $discount);
        $cat = array();
        while ($stmt->fetch()) {
            $data = array();
            $data['tr_id'] = $tr_id;
            $data['t_id'] = $t_id;
            $data['p_id'] = $p_id;
            $data['p_name'] = $p_name;
            $data['p_address'] = $p_address;
            $data['p_no'] = $p_no;
            $data['p_age'] = $p_age;
            $data['blood'] = $blood;
            $data['p_gender'] = $p_gender;
            $data['date'] = $date;
            $data['doc_ref'] = $doc_ref;
            $data['status'] = $status;
            $data['amount'] = $amount;
            $data['discount'] = $discount;
            array_push($cat, $data);
        }
        array_push($cat1, $cat);
        $stmt = $this->con->prepare("SELECT * FROM `test_result` WHERE tr_id = ?");
        $stmt->bind_param("i", $tr_id);
        $stmt->execute();
        $stmt->bind_result($r_id, $tr_id,$v_name, $noraml_range, $result, $unit);
        $cat2 = array();
        while ($stmt->fetch()) {
            $data = array();
            $data['r_id'] = $r_id;
            $data['tr_id'] = $tr_id;
            $data['v_name'] = $v_name;
            $data['noraml_range'] = $noraml_range;
            $data['result'] = $result;
            $data['unit'] = $unit;
            array_push($cat2, $data);
        }
        array_push($cat1,$cat2 );
        return $cat1;
    }
    // Dsicharge Patient 27-02-2023
    function discharge($op_id)
    {
        date_default_timezone_set("Asia/Karachi");
        $date = date("Y-m-d");
        $stmt = $this->con->prepare("INSERT INTO `discharge`(`op_id`, `date`) VALUES (?,?)");
        $stmt->bind_param("is", $op_id, $date);
        if ($stmt->execute()) {
            $stmt = $this->con->prepare("SELECT MAX(d_id) as `d_id` FROM discharge");
            $stmt->execute();
            $stmt->bind_result($d_id);
            $stmt->fetch();
            return $d_id;
        }
        return PROFILE_NOT_CREATED;
    }
    // Add Discharge Expense
    function addDischargeExpense($d_id, $description, $amount)
    {
        $stmt = $this->con->prepare("INSERT INTO `discharge_expense`(`d_id`, `description`, `amount`) VALUES (?,?,?)");
        $stmt->bind_param("isi", $d_id, $description, $amount);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }
    // get pending operates
    function getpendingoperation()
    {
        $status = "pending";
        $stmt = $this->con->prepare("SELECT operate.id as id , operate.p_name as p_name , operate.p_age as p_age,operate.p_no as p_no , operate.d_fees as d_fee , operate.date as time , operate.p_gender as p_gender, operate.description as opdescription, operate.advance as advance , operate.remaining_amount as remaining_amount , room_detail.rd_id  as rd_id , room_detail.r_id as r_id , room_detail.room_no , room_detail.charges as room_charges, doctor.name as drname FROM `operate` JOIN room_detail ON room_detail.rd_id = operate.r_id JOIN doctor ON doctor.d_id = operate.d_id WHERE operate.status = 'pending'");
        $stmt->execute();
        $stmt->bind_result($id, $p_name, $p_age, $p_no, $d_fees, $time, $p_gender, $opdescription, $advance, $remaining_amount, $rd_id, $r_id, $room_no, $room_charges, $drname);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['id'] = $id;
            $test['p_name'] = $p_name;
            $test['p_age'] = $p_age;
            $test['p_no'] = $p_no;
            $test['d_fees'] = $d_fees;
            $test['time'] = $time;
            $test['p_gender'] = $p_gender;
            $test['opdescription'] = $opdescription;
            $test['advance'] = $advance;
            $test['remaining_amount'] = $remaining_amount;
            $test['rd_id'] = $rd_id;
            $test['r_id'] = $r_id;
            $test['room_no'] = $room_no;
            $test['room_charges'] = $room_charges;
            $test['drname'] = $drname;

            array_push($cat, $test);
        }
        return $cat;
    }
     // Add Investigating 
    function addInvestigating($inv_name, $inv_price)
    {
        $stmt = $this->con->prepare("INSERT INTO `investigations`(`inv_name`, `inv_price`) VALUES (?,?)");
        $stmt->bind_param("si", $inv_name, $inv_price);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }
    // getInvestigations
    function getInvestigations()
    {
        $status = "pending";
        $stmt = $this->con->prepare("SELECT * FROM investigations");
        $stmt->execute();
        $stmt->bind_result($inv_id, $inv_name, $inv_price);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['inv_id'] = $inv_id;
            $test['inv_name'] = $inv_name;
            $test['inv_price'] = $inv_price;

            array_push($cat, $test);
        }
        return $cat;
    }
     // Update Opearte Status
    function updateOperateStatus($id)
    {
        $stmt = $this->con->prepare("UPDATE `operate` SET `status`= 'confirm' WHERE operate.id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return PROFILE_CREATED;
        }
        return PROFILE_NOT_CREATED;
    }
     // Update Rooms Status
     function updateRoomStatus($r_id)
     {
         $stmt = $this->con->prepare("UPDATE `room_detail` SET `status`= 'pending' WHERE room_detail.r_id = ?");
         $stmt->bind_param("i", $r_id);
         if ($stmt->execute()) {
             return PROFILE_CREATED;
         }
         return PROFILE_NOT_CREATED;
     }
     // Get Opearte by Category
    function getOpearteCategory($r_id)
    {
        $stmt = $this->con->prepare("SELECT * FROM `room_detail` WHERE `r_id` = ? AND status = 'pending'");
        $stmt->bind_param("i", $r_id);
        $stmt->execute();
        $stmt->bind_result($rd_id, $r_id, $room_no, $charges, $status);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['rd_id'] = $rd_id;
            $test['r_id'] = $r_id;
            $test['room_no'] = $room_no;
            $test['charges'] = $charges;
            $test['status'] = $status;
            array_push($cat, $test);
        }
        return $cat;
    }
    // Get Opearte Expense and Discharged Patient Data
    function getDischarged()
    {
        $stmt = $this->con->prepare("SELECT DISTINCT operate.id ,operate.p_name, operate.p_age ,operate.p_no , operate.d_fees , operate.date as time , operate.status , operate.p_gender , operate.description, operate.advance  , operate.remaining_amount , room_detail.rd_id , room_detail.r_id , room_detail.room_no , doctor.name ,(SELECT SUM(discharge_expense.amount)  FROM discharge_expense WHERE discharge_expense.d_id = discharge.d_id) as total_expense, discharge.date as discharge_date, discharge.d_id   FROM operate join room_detail on room_detail.rd_id = operate.r_id  join doctor on doctor.d_id = operate.d_id JOIN discharge ON discharge.op_id = operate.id JOIN discharge_expense ON discharge_expense.d_id = discharge.d_id WHERE operate.`status` = 'confirm'");
        $stmt->execute();
        $stmt->bind_result($id, $p_name, $p_age, $p_no, $d_fees, $time,$status, $p_gender, $opdescription, $advance, $remaining_amount, $rd_id, $r_id, $room_no, $drname, $total_expense, $discharge_date, $d_id);

        $cat = array();
        while ($stmt->fetch()) {
            $test = array();
            $test['id'] = $id;            
            $test['p_name'] = $p_name;
            $test['p_age'] = $p_age;
            $test['p_no'] = $p_no;
            $test['d_fees'] = $d_fees;
            $test['time'] = $time;
            $test['status'] = $status;
            $test['p_gender'] = $p_gender;
            $test['opdescription'] = $opdescription;
            $test['advance'] = $advance;
            $test['remaining_amount'] = $remaining_amount;
            $test['rd_id'] = $rd_id;
            $test['r_id'] = $r_id;
            $test['room_no'] = $room_no;
            $test['drname'] = $drname;
            $test['total_expense'] = $total_expense;
            $test['total_payable'] = $total_expense + $d_fees;
            $test['discharge_date'] = $discharge_date;
            $test['d_id'] = $d_id;

            array_push($cat, $test);
        }
        return $cat;
    }

// department

function adddepartment($dp_name, $dp_description)
{
    $stmt = $this->con->prepare("INSERT INTO `department`(`dp_name`, `dp_description`) VALUES (?,?)");
    $stmt->bind_param("ss", $dp_name, $dp_description);
    if ($stmt->execute()) {
        return PROFILE_CREATED;
    }
    return PROFILE_NOT_CREATED;
}

function getdepartment()
{
    $stmt = $this->con->prepare("SELECT `dp_id`, `dp_name`, `dp_description` FROM `department`");
    $stmt->execute();
    $stmt->bind_result($dp_id, $dp_name, $dp_description);

    $cat = array();
    while ($stmt->fetch()) {
        $test = array();
        $test['dp_id'] = $dp_id;
        $test['dp_name'] = $dp_name;
        $test['dp_description'] = $dp_description;

        array_push($cat, $test);
    }
    return $cat;
}


function updatedepartment($dp_id, $dp_name, $dp_description)
{
    $stmt = $this->con->prepare("UPDATE `department` SET `dp_name`=?,`dp_description`=? WHERE `dp_id`=?");
    $stmt->bind_param("ssi", $dp_name, $dp_description, $dp_id);
    if ($stmt->execute()) {
        return PROFILE_CREATED;
    }
    return PROFILE_NOT_CREATED;
}

function deletedepartment($dp_id)
{
    $stmt = $this->con->prepare("DELETE FROM `department` WHERE `dp_id`=?");
    $stmt->bind_param("i", $dp_id);
    if ($stmt->execute()) {
        return PROFILE_CREATED;
    }
    return PROFILE_NOT_CREATED;
    // get discharge_expenses by d_id
}
function getDischargedExpensebyDid($d_id)
{
    $stmt = $this->con->prepare("SELECT description, amount FROM discharge_expense WHERE d_id = ?");
    $stmt->bind_param("i", $d_id);
    $stmt->execute();
    $stmt->bind_result($description, $amount);

    $cat = array();
    while ($stmt->fetch()) {
        $test = array();
        $test['description'] = $description;            
        $test['amount'] = $amount;
        array_push($cat, $test);
    }
    return $cat;
}

 //  Add Transactions
 function transactions($type, $sub_type, $debit, $credit, $net_balance, $description)
 {   $date = date("ymd");
     $stmt = $this->con->prepare("INSERT INTO `transaction`(`type`, `sub_type`, `debit`, `credit`, `net_balance`, `description`, `date`) VALUES (?,?,?,?,?,?,?)");
     $stmt->bind_param("ssiiiss", $type, $sub_type, $debit, $credit, $net_balance, $description, $date);
     if ($stmt->execute()) {
         $stmt = $this->con->prepare("SELECT t_id from transaction where t_id = (select MAX(t_id) from transaction)");
         $stmt->execute();
        $stmt->bind_result($t_id);
        $stmt->fetch();
        return $t_id;
     }
     return PROFILE_NOT_CREATED;
 }

 function getNetbalance() {
        $stmt = $this->con->prepare("SELECT net_balance FROM transaction where t_id = (select MAX(t_id) from transaction)");
        $stmt->execute();
        $stmt->bind_result($netbalance);
        $stmt->fetch();
        return $netbalance;
 }
 //  Add Transactions
 function updateNetBalance($t_id, $net_balance)
 {   $date = date("ymd");
     $stmt = $this->con->prepare("UPDATE `transaction` SET `net_balance`=? WHERE t_id = ?");
     $stmt->bind_param("ii", $net_balance, $t_id);
     if ($stmt->execute()) {
         return PROFILE_CREATED;
     }
     return PROFILE_NOT_CREATED;
 }
 function getNetbalanceCreditDebit() {
    $stmt = $this->con->prepare("SELECT net_balance FROM transaction where t_id = (select MAX(t_id) from transaction)");
    $stmt->execute();
    $stmt->bind_result($netbalance);
    $data = array();
    while($stmt->fetch()) {
        $test = array();
        $test['netbalance'] = $netbalance;
        array_push($data, $test);
    }
    $stmt = $this->con->prepare("SELECT SUM(credit) as total_credit FROM transaction ");
    $stmt->execute();
    $stmt->bind_result($total_credit);
    while($stmt->fetch()) {
        $test = array();
        $test['total_credit'] = $total_credit;
        array_push($data, $test);
    }
    $stmt = $this->con->prepare("SELECT SUM(debit) as total_debit FROM transaction");
    $stmt->execute();
    $stmt->bind_result($total_debit);
    while($stmt->fetch()) {
        $test = array();
        $test['total_debit'] = $total_debit;
        array_push($data, $test);
    }
    return $data;
}
function getTransactions() {
    $stmt = $this->con->prepare("SELECT * FROM transaction");
    $stmt->execute();
    $stmt->bind_result($t_id, $type, $sub_type, $debit, $credit, $net_balance, $description, $date);
    $cat = array();
    while($stmt->fetch()) {
        $test = array();
        $test['t_id']= $t_id;
        $test['type']= $type;
        $test['sub_type']= $sub_type;
        $test['debit']= $debit;
        $test['credit']= $credit;
        $test['net_balance']= $net_balance;
        $test['description']= $description;
        $test['date']= $date;
        array_push($cat, $test);
    }
    return $cat;
}

// get Transaction by Type
function getTransactionsbyType($type, $sub_type) {
    $stmt = $this->con->prepare("SELECT * FROM transaction WHERE type=?");
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $stmt->bind_result($t_id, $type, $sub_type, $debit, $credit, $net_balance, $description, $date);
    $cat = array();
    while($stmt->fetch()) {
        $test = array();
        $test['t_id']= $t_id;
        $test['type']= $type;
        $test['sub_type']= $sub_type;
        $test['debit']= $debit;
        $test['credit']= $credit;
        $test['net_balance']= $net_balance;
        $test['description']= $description;
        $test['date']= $date;
        array_push($cat, $test);
    }
    return $cat;
}

function getcurrentappointmentNumber($doc_id)
{
    date_default_timezone_set("Asia/Karachi");
    $date = date("Y-m-d");
    $stmt = $this->con->prepare("SELECT COUNT(id) AS current_number FROM `appointment` WHERE doc_id=? AND time='$date'");
    $stmt->bind_param("i", $doc_id);
    $stmt->execute();
    $stmt->bind_result($current_number);

    $cat = array();
    while ($stmt->fetch()) {
        $test = array();
        $test['current_number'] = $current_number;

        array_push($cat, $test);
    }
    return $cat;
}

function getPendingroom()
{
    $stmt = $this->con->prepare("SELECT * FROM `room_detail` WHERE status = 'pending'");
    $stmt->execute();
    $stmt->bind_result($rd_id, $r_id, $room_no, $charges, $status);

    $cat = array();
    while ($stmt->fetch()) {
        $test = array();
        $test['rd_id'] = $rd_id;
        $test['r_id'] = $r_id;
        $test['room_no'] = $room_no;
        $test['charges'] = $charges;
        $test['status'] = $status;
        array_push($cat, $test);
    }
    return $cat;
}

}
?>