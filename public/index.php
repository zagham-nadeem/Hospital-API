<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


require __DIR__ . '/../vendor/autoload.php';

require_once '../includes/dboperation.php';
$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);



//login Api
$app->post('/login', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $username = $requestData->username;
    $password = $requestData->password;
    $role = $requestData->role;
    $token = $requestData->token;
    $db = new DbOperation();
    $responseData = array();
    if ($db->userlogin($username, $password, $role)) {
        $responseData['error'] = false;
        $responseData['user'] = $db->getUserByEmail($username, $password, $role);
        $responseData['update'] = $db->updatetoken($token, $username, $password, $role);
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'Invalid email or password';
    }
    $response->getBody()->write(json_encode($responseData));
});



//signup

$app->post('/signup', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $username = $requestData->username;
    $password = $requestData->password;
    $role = $requestData->role;
    $token = $requestData->token;
    $first_name = $requestData->first_name;
    $last_name = $requestData->last_name;
    $address = $requestData->address;
    $cnic = $requestData->cnic;
    $phoneno = $requestData->phoneno;
    $img = $requestData->img;
    $db = new DbOperation();
    $result = $db->registerUser($username, $password, $role, $token);
    $responseData = array();

    if ($result == USER_CREATED) {
        $responseData['error'] = false;
        $responseData['message'] = 'Registered successfully';
        $result = $db->getUserByEmail1($username, $password, $role);
        $id1 = $result;
        $res = $db->create_profile($id1, $first_name, $last_name, $address, $cnic, $phoneno, $img);

    } elseif ($result == USER_CREATION_FAILED) {
        $responseData['error'] = true;
        $responseData['message'] = 'Repassword  not Matched';
    } elseif ($result == USER_EXIST) {
        $responseData['error'] = true;
        $responseData['message'] = 'This email already exist, please login';
    }
    $response->getBody()->write(json_encode($responseData));
});

// login social 



$app->post('/login_social', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $u_id = $requestData->u_id;
    $db = new DbOperation();
    $responseData = array();
    $result = $db->create_profile_social($u_id);
    $responseData = array();
    if ($result == USER_CREATED) {
        $responseData['error'] = false;
        $responseData['user'] = 'user Created Successfull';
    } else {
        $responseData['error'] = false;
        $responseData['e'] = $db->getprofilesocial($u_id);
    }
    $response->getBody()->write(json_encode($responseData));
});


$app->post('/profileupdate', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $pr_id = $requestData->pr_id;
    $first_name = $requestData->first_name;
    $last_name = $requestData->last_name;
    $address = $requestData->address;
    $cnic = $requestData->cnic;
    $phoneno = $requestData->phoneno;
    $img = $requestData->img;
    $db = new DbOperation();
    $result = $db->profileupdate($pr_id, $first_name, $last_name, $address, $cnic, $phoneno, $img);
    $responseData = array();

    if ($result == PROFILE_UPDATED) {
        $responseData['error'] = false;
        $responseData['message'] = 'your profile updated';
        $responseData['profileData'] = $db->getprofilebyid1($pr_id);
    } elseif ($result == PROFILE_NOT_UPDATED) {
        $responseData['error'] = true;
        $responseData['message'] = 'profile not updated';
    }
    $response->getBody()->write(json_encode($responseData));
});




//doctor data


$app->post('/add_doctor', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $name = $requestData->doctor->name;
    $email = $requestData->doctor->email;
    $age = $requestData->doctor->age;
    $gender = $requestData->doctor->gender;
    $fees = $requestData->doctor->fees;
    $d_no = $requestData->doctor->d_no;
    $speciality = $requestData->doctor->speciality;
    $username = $requestData->doctor->username;
    $password = $requestData->doctor->password;
    $days = $requestData->days;
    $db = new DbOperation();
    $responseData = array();
    $result = $db->doctordetail($name, $email, $age, $gender, $fees, $d_no, $speciality, $username, $password);
    $responseData = array();
    if ($result == PROFILE_NOT_CREATED) {
        $responseData['error'] = true;
        $responseData['Message'] = "medicine is  not added";
    } else {
        $d_id = $result;
        foreach ($days as $item) {
            $day = $item->day;
            $from_time = $item->from_time;
            $to_time = $item->to_time;
            $db = new DbOperation();
            $result = $db->add_doctordetail($d_id, $day, $from_time, $to_time);
            if ($result == ORDER_PLACED) {
                $responseData['error'] = false;
                $responseData['doctorlogin'] = $db->add_stafflogin($username, $password);
                $responseData['Message'] = "medicine added sucessfully";
            } else {
                $responseData['error'] = true;
                $responseData['message'] = 'medicine is not inserted';
            }
        }
    }
    $response->getBody()->write(json_encode($responseData));
});



$app->get('/getprofile/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $db = new DbOperation();
    $result = $db->getprofilebyid($id);
    $response->getBody()->write(json_encode($result));
});





//get

$app->get('/getdoctor', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->getdoctor();
    $response->getBody()->write(json_encode($result));
});


$app->get('/getdoctordetails/{d_id}', function (Request $request, Response $response) {
    $d_id = $request->getAttribute('d_id');
    $db = new DbOperation();
    $result = $db->getdoctordetails($d_id);
    $response->getBody()->write(json_encode($result));
});

$app->post('/updatedoctorlogin', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $username = $requestData->username;
    $password = $requestData->password;
    $oldusername = $requestData->oldusername;
    $db = new DbOperation();
    $responseData = array();
    if ($db->updatedoctorlogin($username, $password, $oldusername)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data Updated sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not inserted';
    }
    $response->getBody()->write(json_encode($responseData));
});


//delete




$app->post('/deletedoctor', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $id = $requestData->id;
    $db = new DbOperation();
    $responseData = array();
    if ($db->deletedoctor($id)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data deleted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not deleted';
    }
    $response->getBody()->write(json_encode($responseData));
});





//update




$app->post('/updatedoctordetail', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $id = $requestData->id;
    $name = $requestData->name;
    $email = $requestData->email;
    $age = $requestData->age;
    $gender = $requestData->gender;
    $fees = $requestData->fees;
    $d_no = $requestData->d_no;
    $db = new DbOperation();
    $responseData = array();
    if ($db->updatedoctordetail($id, $name, $email, $age, $gender, $fees, $d_no)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data Updated sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not inserted';
    }
    $response->getBody()->write(json_encode($responseData));
});








//Add Staff

$app->post('/add_staff', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $name = $requestData->name;
    $no = $requestData->no;
    $age = $requestData->age;
    $gender = $requestData->gender;
    $catagory = $requestData->catagory;
    $schedule = $requestData->schedule;
    $salary = $requestData->salary;
    $db = new DbOperation();
    $responseData = array();
    if ($db->addStaff($name, $no, $gender, $age, $catagory, $schedule, $salary)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data inserted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not inserted';
    }
    $response->getBody()->write(json_encode($responseData));
});
// Update Staff
$app->post('/updatestaffdetail', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $id = $requestData->id;
    $name = $requestData->name;
    $no = $requestData->no;
    $age = $requestData->age;
    $gender = $requestData->gender;
    $catagory = $requestData->catagory;
    $schedule = $requestData->schedule;
    $salary = $requestData->salary;
    $db = new DbOperation();
    $responseData = array();
    if ($db->updateStaff($id, $name, $no, $gender, $age, $catagory, $schedule, $salary)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data updated sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not updated';
    }
    $response->getBody()->write(json_encode($responseData));
});


//get

$app->get('/getnursedetail', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->getnursedetail();
    $response->getBody()->write(json_encode($result));
});



//delete


$app->post('/deletenurse', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $id = $requestData->id;
    $db = new DbOperation();
    $responseData = array();
    if ($db->deletenurse($id)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data deleted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not deleted';
    }
    $response->getBody()->write(json_encode($responseData));
});



//update






//staff data


$app->post('/staffdetail', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $name = $requestData->name;
    $no = $requestData->no;
    $age = $requestData->age;
    $gender = $requestData->gender;
    $catagory = $requestData->catagory;
    $schedule = $requestData->schedule;
    $salary = $requestData->salary;
    $db = new DbOperation();
    $responseData = array();
    if ($db->staffdetail($name, $no, $age, $gender, $catagory, $schedule, $salary)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data inserted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not inserted';
    }
    $response->getBody()->write(json_encode($responseData));
});



//get


$app->get('/getstaffdetail', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->getstaffdetail();
    $response->getBody()->write(json_encode($result));
});



//delete


$app->post('/deletestaff', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $id = $requestData->id;
    $db = new DbOperation();
    $responseData = array();
    if ($db->deletestaff($id)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data deleted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not deleted';
    }
    $response->getBody()->write(json_encode($responseData));
});

// Staff roaster



$app->post('/add_roaster', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $roaster = $requestData->roaster;
    $month = $requestData->days->month;
    $year = $requestData->days->year;
    foreach ($roaster as $item) {

        $id = $item->id;
        $shift = $item->shift;
        $db = new DbOperation();
        $responseData = array();
        $result = $db->add_roaster($id, $shift, $month, $year);
        if ($result == PROFILE_CREATED) {
            $responseData['error'] = false;
            $responseData['message'] = 'data inserted sucessfully';
        } else {
            $responseData['error'] = true;
            $responseData['message'] = 'data is not inserted';
        }
    }
    $response->getBody()->write(json_encode($responseData));
});


//get roaster
$app->post('/get_roaster', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $month = $requestData->month;
    $year = $requestData->year;
    $db = new DbOperation();
    $responseData = array();
    $result = $db->get_roaster($month, $year);

    $response->getBody()->write(json_encode($result));
});

// update rosater

$app->post('/update_roaster', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $id = $requestData->id;
    $month = $requestData->month;
    $year = $requestData->year;
    $shift = $requestData->shift;
    $db = new DbOperation();
    $responseData = array();
    if ($db->update_roaster($id, $month, $year, $shift)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data Updated sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not inserted';
    }
    $response->getBody()->write(json_encode($responseData));
});


// Mark_attendence_staff

$app->post('/mark_attendence', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $attendences = $requestData->attendences;

    foreach ($attendences as $item) {
        $id = $item->id;
        $replacement_id = $item->replacement_id;
        $replacement_name = $item->replacement_name;
        $attendence = $item->attendence;
        $db = new DbOperation();
        $responseData = array();
        if ($db->mark_attendence($id, $replacement_id, $replacement_name, $attendence)) {
            $responseData['error'] = false;
            $responseData['message'] = 'data inserted sucessfully';
        } else {
            $responseData['error'] = true;
            $responseData['message'] = 'data is not inserted';
        }
    }
    $response->getBody()->write(json_encode($responseData));
});

//update attendence

$app->post('/update_attendence', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $id = $requestData->id;
    $replacement_id = $requestData->replacement_id;
    $replacement_name = $requestData->replacement_name;
    $attendence = $requestData->attendence;
    $date = $requestData->date;
    $db = new DbOperation();
    $responseData = array();
    if ($db->update_attendence($id, $replacement_id, $replacement_name, $attendence, $date)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data Updated sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not inserted';
    }
    $response->getBody()->write(json_encode($responseData));
});

//
$app->get('/get_attendence', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->get_attendence();
    $response->getBody()->write(json_encode($result));
});

// attendence detail

$app->get('/attendence_detail/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $db = new DbOperation();
    $result = $db->attendence_detail($id);
    $response->getBody()->write(json_encode($result));
});

// attendence detail datewise
$app->post('/get_attendence_date', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $id = $requestData->id;
    $start = $requestData->start;
    $end = $requestData->end;
    $db = new DbOperation();
    $result = $db->get_attendence_date($id, $start, $end);
    $response->getBody()->write(json_encode($result));
});



//Room Api

$app->post('/addroom', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $r_id = $requestData->r_id;
    $room_no = $requestData->r_type;
    $charges = $requestData->charges;
    $db = new DbOperation();
    $responseData = array();
    if ($db->addroom($r_id, $room_no, $charges)) {
        $responseData['error'] = false;
        $responseData['message'] = 'room is added';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'room is not added';
    }

    $response->getBody()->write(json_encode($responseData));
});

// Update Room
$app->post('/updateroom', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $rd_id = $requestData->rd_id;
    $r_id = $requestData->rd_id;
    $room_no = $requestData->r_type;
    $charges = $requestData->charges;
    $status = $requestData->status;
    $db = new DbOperation();
    $responseData = array();
    if ($db->updateRoom($rd_id,$r_id, $room_no, $charges,$status)) {
        $responseData['error'] = false;
        $responseData['message'] = 'room is updated';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'room is not updated';
    }

    $response->getBody()->write(json_encode($responseData));
});

$app->get('/getroomcat', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->getroomcat();
    $response->getBody()->write(json_encode($result));
});


$app->get('/getroomdetail/{r_id}', function (Request $request, Response $response) {
    $r_id = $request->getAttribute('r_id');
    $db = new DbOperation();
    $result = $db->getroomdetail($r_id);
    $response->getBody()->write(json_encode($result));
});
$app->get('/getroomdetailbyrid/{r_id}', function (Request $request, Response $response) {
    $r_id = $request->getAttribute('r_id');
    $db = new DbOperation();
    $result = $db->getroomdetailbyrid($r_id);
    $response->getBody()->write(json_encode($result));
});

// Get Room
$app->get('/getroomdetails', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->getroomdetails();
    $response->getBody()->write(json_encode($result));
});

//pending rooms

$app->get('/getpendingrooms/{r_id}', function (Request $request, Response $response) {
    $r_id = $request->getAttribute('r_id');
    $db = new DbOperation();
    $result = $db->getpendingrooms($r_id);
    $response->getBody()->write(json_encode($result));
});

//appointment


$app->post('/appointment', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $p_name = $requestData->p_name;
    $p_no = $requestData->p_no;
    $p_age = $requestData->p_age;
    $p_gender = $requestData->p_gender;
    $u_id = $requestData->u_id;
    $doc_id = $requestData->doc_id;
    $fees = $requestData->fees;
    $type = $requestData->type;
    $db = new DbOperation();
    $responseData = array();
    if ($db->appointment($p_name, $p_no, $p_age, $u_id, $doc_id, $p_gender, $fees, $type)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data inserted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not inserted';
    }
    $response->getBody()->write(json_encode($responseData));
});


//get

$app->get('/getappointment', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->getappointment();
    $response->getBody()->write(json_encode($result));
});

$app->get('/getappointmentbyid/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $db = new DbOperation();
    $result = $db->getappointmentbyid($id);
    $response->getBody()->write(json_encode($result));
});



$app->get('/getappointmentbyd_id/{d_id}', function (Request $request, Response $response) {
    $d_id = $request->getAttribute('d_id');
    $db = new DbOperation();
    $result = $db->getappointmentbyd_id($d_id);
    $response->getBody()->write(json_encode($result));
});




$app->post('/updateappointstatus', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $id = $requestData->id;
    $db = new DbOperation();
    $responseData = array();
    if ($db->updateappointstatus($id)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data inserted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not inserted';
    }
    $response->getBody()->write(json_encode($responseData));
});


$app->post('/deleteappointmentuser', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $u_id = $requestData->u_id;
    $id = $requestData->id;
    $db = new DbOperation();
    $responseData = array();
    if ($db->deleteappointmentuser($u_id, $id)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data inserted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not inserted';
    }
    $response->getBody()->write(json_encode($responseData));
});




$app->post('/updateappointment', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $id = $requestData->id;
    $p_name = $requestData->p_name;
    $p_no = $requestData->p_no;
    $p_age = $requestData->p_age;
    $p_gender = $requestData->p_gender;
    $doc_id = $requestData->doc_id;
    $time = $requestData->time;
    $fees = $requestData->fees;
    $db = new DbOperation();
    $responseData = array();
    if ($db->updateappointment($id, $p_name, $p_no, $p_age, $doc_id, $p_gender, $time, $fees)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data inserted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not inserted';
    }
    $response->getBody()->write(json_encode($responseData));
});

//delete


$app->post('/deleteappointment', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $id = $requestData->id;
    $db = new DbOperation();
    $responseData = array();
    if ($db->deleteappointment($id)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data deleted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not deleted';
    }
    $response->getBody()->write(json_encode($responseData));
});



$app->post('/addoperatecategory', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $operate_name = $requestData->operate_name;
    $operate_amount = $requestData->operate_amount;
    $db = new DbOperation();
    $responseData = array();
    if ($db->addoperatecategory($operate_name, $operate_amount)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data inserted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not inserted';
    }
    $response->getBody()->write(json_encode($responseData));
});

$app->get('/getoperatecategory', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->getoperatecategory();
    $response->getBody()->write(json_encode($result));
});


$app->post('/deleteoperatecategory', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $op_id = $requestData->op_id;
    $db = new DbOperation();
    $responseData = array();
    if ($db->deleteoperatecategory($op_id)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data deleted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not deleted';
    }
    $response->getBody()->write(json_encode($responseData));
});

$app->post('/updateoperatecategory', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $operate_name = $requestData->operate_name;
    $operate_amount = $requestData->operate_amount;
    $op_id = $requestData->op_id;
    $db = new DbOperation();
    $responseData = array();
    if ($db->updateoperatecategory($operate_name, $operate_amount, $op_id)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data Updated sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not Updated';
    }
    $response->getBody()->write(json_encode($responseData));
});


//operate data

$app->post('/operate', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $type = $requestData->type;
    $pid = $requestData->pid;
    $p_name = $requestData->p_name;
    $p_age = $requestData->p_age;
    $p_no = $requestData->p_no;
    $d_id = $requestData->d_id;
    $r_id = $requestData->r_id;
    $room_no = $requestData->room_no;
    $charges = $requestData->charges;
    $d_fees = $requestData->d_fees;
    $p_gender = $requestData->p_gender;
    $p_blood = $requestData->p_blood;
    $p_address = $requestData->p_address;
    $cnic = $requestData->cnic;
    $fh_name = $requestData->fh_name;
    $description = $requestData->description;
    $advance = $requestData->advance;
    $remaining_amount = $requestData->remaining_amount;
    $is_new = $requestData->is_new;
    $db = new DbOperation();
    if($is_new == true) {
        $result = $db->addPatientOperate($p_name, $p_age, $p_gender, $p_blood, $p_address, $cnic, $p_no, $fh_name);
        if ($result == PROFILE_NOT_CREATED) {
        $responseData['error'] = true;
        $responseData['Message'] = "patient is  not added";
    } else {
        $p_id = $result; 
        $responseData = array();
    if ($db->operate($type,$p_id, $p_name, $p_age, $p_no, $d_id, $r_id, $room_no, $charges, $d_fees, $p_gender, $description, $advance, $remaining_amount)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data Inserted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not Inserted';
        
    }
    }
    }
    else {
        if($db->operate($type,$pid, $p_name, $p_age, $p_no, $d_id, $r_id, $room_no, $charges, $d_fees, $p_gender, $description, $advance, $remaining_amount)) {
            $responseData['error'] = false;
        $responseData['message'] = 'data Inserted sucessfully';
        }
    }
    $response->getBody()->write(json_encode($responseData));
});


//get



$app->get('/getoperation', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->getoperation();
    $response->getBody()->write(json_encode($result));
});





//delete


$app->post('/deleteoperate', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $id = $requestData->id;
    $rd_id = $requestData->rd_id;
    $db = new DbOperation();
    $responseData = array();
    if ($db->deleteoperate($id, $rd_id)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data deleted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not deleted';
    }
    $response->getBody()->write(json_encode($responseData));
});





//surgery data


$app->post('/surgery', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $p_name = $requestData->p_name;
    $p_age = $requestData->p_age;
    $p_gender = $requestData->p_gender;
    $p_no = $requestData->p_no;
    $d_id = $requestData->d_id;
    $time = $requestData->time;
    $d_fees = $requestData->d_fees;
    $db = new DbOperation();
    $responseData = array();
    if ($db->surgery($p_name, $p_age, $p_no, $p_gender, $d_id, $time, $d_fees)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data inserted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not inserted';
    }
    $response->getBody()->write(json_encode($responseData));
});



//get



$app->get('/getsurgery', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->getsurgery();
    $response->getBody()->write(json_encode($result));
});




//delete


$app->post('/deletesurgery', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $id = $requestData->id;
    $db = new DbOperation();
    $responseData = array();
    if ($db->deletesurgery($id)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data deleted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not deleted';
    }
    $response->getBody()->write(json_encode($responseData));
});



//join table//




$app->get('/getdata2', function (Request $rqst, Response $rspn) {
    $db = new DbOperation();
    $result = $db->getit2();
    $rspn->getBody()->write(json_encode($result));
});


$app->get('/getsummary', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->getsummary();
    $response->getBody()->write(json_encode($result));
});

$app->post('/getsummary_date', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $start = $requestData->start;
    $end = $requestData->end;
    $db = new DbOperation();
    $result = $db->getsummary_date($start, $end);
    $response->getBody()->write(json_encode($result));
});


$app->post('/get_summary_detail', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $ide = $requestData->ide;
    $db = new DbOperation();
    $result = $db->get_summary_detail($ide);
    $response->getBody()->write(json_encode($result));
});

$app->post('/get_summary_detail_date', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $ide = $requestData->ide;
    $id = $requestData->id;
    $start = $requestData->start;
    $end = $requestData->end;
    $db = new DbOperation();
    $result = $db->get_summary_detail_date($ide, $id, $start, $end);
    $response->getBody()->write(json_encode($result));
});

$app->post('/add_expense', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $name = $requestData->name;
    $db = new DbOperation();
    $responseData = array();
    if ($db->addexpense($name)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data deleted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not deleted';
    }
    $response->getBody()->write(json_encode($responseData));
});

$app->post('/delete_expense', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $e_id = $requestData->e_id;
    $db = new DbOperation();
    $responseData = array();
    if ($db->deleteexpense($e_id)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data deleted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not deleted';
    }
    $response->getBody()->write(json_encode($responseData));
});

$app->get('/get_expense', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->get_expense();
    $response->getBody()->write(json_encode($result));
});


// expense details Apis


$app->post('/add_expense_detail', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $e_id = $requestData->e_id;
    $e_description = $requestData->description;
    $e_amount = $requestData->amount;
    $e_date = $requestData->date;
    $db = new DbOperation();
    $responseData = array();
    if ($db->add_expense_detail($e_id, $e_description, $e_amount, $e_date)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data deleted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not deleted';
    }
    $response->getBody()->write(json_encode($responseData));
});


$app->post('/delete_expense_detail', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $id = $requestData->id;
    $db = new DbOperation();
    $responseData = array();
    if ($db->delete_expense_detail($id)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data deleted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not deleted';
    }
    $response->getBody()->write(json_encode($responseData));
});
$app->get('/get_expense_detail/{e_id}', function (Request $request, Response $response) {
    $e_id = $request->getAttribute('e_id');
    $db = new DbOperation();
    $result = $db->get_expense_detail($e_id);
    $response->getBody()->write(json_encode($result));
});

// Bank Apis

$app->post('/insertbank', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $name = $requestData->name;
    $title = $requestData->title;
    $acct_no = $requestData->no;
    $db = new DbOperation();
    $responseData = array();
    if ($db->insertbank($name, $title, $acct_no)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data inserted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not inserted';
    }
    $response->getBody()->write(json_encode($responseData));
});

$app->get('/getbank', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->getbank();
    $response->getBody()->write(json_encode($result));
});


$app->get('/getbankdetail/{b_id}', function (Request $request, Response $response) {
    $b_id = $request->getAttribute('b_id');
    $db = new DbOperation();
    $result = $db->getbankdetail($b_id);
    $response->getBody()->write(json_encode($result));
});

$app->post('/insertbankdetail', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $b_id = $requestData->b_id;
    $customer_id = $requestData->customer_id;
    $name = $requestData->name;
    $title = $requestData->title;
    $no = $requestData->no;
    $ammount = $requestData->ammount;
    $credit = $requestData->credit;
    $net_balance = $requestData->net_balance;
    $db = new DbOperation();
    $responseData = array();
    if ($db->insertbankdetail($b_id, $customer_id, $name, $title, $no, $ammount, $credit, $net_balance, $cnet_balance)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data inserted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not inserted';
    }
    $response->getBody()->write(json_encode($responseData));
});

$app->post('/delete_bank__detail', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $id = $requestData->id;
    $db = new DbOperation();
    $responseData = array();
    if ($db->delete_bank_detail($id)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data deleted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not deleted';
    }
    $response->getBody()->write(json_encode($responseData));
});

//bank graph


$app->get('/bank_graph', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->bank_graph();
    $response->getBody()->write(json_encode($result));
});

// graphn apis


$app->get('/appointment_graph', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->appointment_graph();
    $response->getBody()->write(json_encode($result));
});

$app->get('/expense_graph', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->expense_graph();
    $response->getBody()->write(json_encode($result));
});






//Client app APis
// Zagham
// Get Patient
$app->get('/getPatients', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->getPatients();
    $response->getBody()->write(json_encode($result));
});
// Get Patients by Pid
$app->get('/getPatientsByPid/{pid}', function (Request $request, Response $response) {
    $pid = $request->getAttribute('pid');
    $db = new DbOperation();
    $result = $db->getPatientsByPid($pid);
    $response->getBody()->write(json_encode($result));
});
// Add Patient
$app->post('/insertPatient', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $name = $requestData->name;
    $age = $requestData->age;
    $gender = $requestData->gender;
    $blood = $requestData->blood;
    $address = $requestData->address;
    $cnic = $requestData->cnic;
    $phone = $requestData->phone;
    $fh_name = $requestData->fh_name;
    $db = new DbOperation();
    $responseData = array();
    if ($db->addPatient($name, $age, $gender, $blood, $address, $cnic, $phone, $fh_name)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data inserted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not inserted';
    }
    $response->getBody()->write(json_encode($responseData));
});
// Update Patient
$app->post('/updatePatient', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $pid = $requestData->pid;
    $name = $requestData->name;
    $age = $requestData->age;
    $gender = $requestData->gender;
    $blood = $requestData->blood;
    $address = $requestData->address;
    $cnic = $requestData->cnic;
    $phone = $requestData->phone;
    $fh_name = $requestData->fh_name;
    $db = new DbOperation();
    $responseData = array();
    if ($db->updatePatient($pid,$name, $age, $gender, $blood, $address, $cnic, $phone, $fh_name)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data updated sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not updated';
    }
    $response->getBody()->write(json_encode($responseData));
});
// Delete Patient
$app->post('/deletePatient', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $pid = $requestData->pid;
    $db = new DbOperation();
    $responseData = array();
    if ($db->deletePatient($pid)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data deleted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not deleted';
    }
    $response->getBody()->write(json_encode($responseData));
});
// Lab Test
$app->post('/addtestreport', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $t_id = $requestData->t_id;
    $p_id = $requestData->p_id;
    $p_name = $requestData->p_name;
    $p_address = $requestData->p_address;
    $p_no = $requestData->p_no;
    $p_age = $requestData->p_age;
    $blood = $requestData->blood;
    $p_gender = $requestData->p_gender;
    $date = $requestData->date;
    $doc_ref = $requestData->doc_ref;
    $status = $requestData->status;
    $amount = $requestData->amount;
    $discount = $requestData->discount;
    $db = new DbOperation();
    $responseData = array();
    if ($db->addTestReport($t_id, $p_id, $p_name, $p_address, $p_no, $p_age, $blood, $p_gender, $date, $doc_ref, $status,$amount, $discount)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data inserted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not inserted';
    }
    $response->getBody()->write(json_encode($responseData));
});

// Add Test Cat with Variables
$app->post('/add_test', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $t_name = $requestData->test->t_name;
    $amount = $requestData->test->amount;
    $other = $requestData->test->other;
    $variables = $requestData->variables;
    $db = new DbOperation();
    $responseData = array();
    $result = $db->addTest($t_name, $amount, $other);
    $responseData = array();
    if ($result == PROFILE_NOT_CREATED) {
        $responseData['error'] = true;
        $responseData['Message'] = "test is  not added";
    } else {
        $t_id = $result;
        foreach ($variables as $item) {
            $tv_name = $item->tv_name;
            $normal_range = $item->normal_range;
            $unit = $item->unit;
            $db = new DbOperation();
            $result = $db->addTestVariables($t_id, $tv_name, $normal_range, $unit);
            if ($result == PROFILE_CREATED) {
                $responseData['error'] = false;
                $responseData['Message'] = "variables added sucessfully";
            } else {
                $responseData['error'] = true;
                $responseData['message'] = 'variables is not inserted';
            }
        }
    }
    $response->getBody()->write(json_encode($responseData));
});

// Add Test Cat with Variables
$app->post('/add_testResult', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $tr_id = $requestData->test->tr_id;
    $status = $requestData->test->status;
    $test_result = $requestData->test_result;
    $db = new DbOperation();
    $responseData = array();
    $result = $db->updateTestReportStatus($tr_id, $status);
    $responseData = array();
    if ($result == PROFILE_NOT_CREATED) {
        $responseData['error'] = true;
        $responseData['Message'] = "test is  not added";
    } else {
        foreach ($test_result as $item) {
            $tv_name = $item->tv_name;
            $normal_range = $item->normal_range;
            $result = $item->result;
            $unit = $item->unit;
            $db = new DbOperation();
            $result = $db->addTestResult($tr_id, $tv_name, $normal_range, $result, $unit);
            if ($result == PROFILE_CREATED) {
                $responseData['error'] = false;
                $responseData['Message'] = "variables added sucessfully";
            } else {
                $responseData['error'] = true;
                $responseData['message'] = 'variables is not inserted';
            }
        }
    }
    $response->getBody()->write(json_encode($responseData));
});

// Get
// Get Tests
$app->get('/getTestReport', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->getReportTemplate();
    $response->getBody()->write(json_encode($result));
});
$app->get('/getTests', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->getTest();
    $response->getBody()->write(json_encode($result));
});
// Get Template
$app->get('/getTemplates', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->getTemplate();
    $response->getBody()->write(json_encode($result));
});

// Get Test Variables
$app->get('/getTestVariables/{t_id}', function (Request $request, Response $response) {
    $t_id = $request->getAttribute('t_id');
    $db = new DbOperation();
    $result = $db->getTestVariable($t_id);
    $response->getBody()->write(json_encode($result));
});
// Get Result
$app->get('/getTestResult/{tr_id}', function (Request $request, Response $response) {
    $tr_id = $request->getAttribute('tr_id');
    $db = new DbOperation();
    $result = $db->getTestResult($tr_id);
    $response->getBody()->write(json_encode($result));
});
// Discharge Patient 27-02-2023
$app->post('/discharge_patient', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $op_id = $requestData->discharge->op_id;
    $r_id = $requestData->discharge->r_id;
    $discharge_expense = $requestData->discharge_expense;
    $db = new DbOperation();
    $responseData = array();
    $result = $db->discharge($op_id);
    $responseData = array();
    if ($result == PROFILE_NOT_CREATED) {
        $responseData['error'] = true;
        $responseData['Message'] = "discharge is  not added";
    } else {
        $d_id = $result;
        $result = $db->updateOperateStatus($op_id);
        $result = $db->updateRoomStatus($r_id);
        foreach ($discharge_expense as $expense) {
            $description = $expense->description;
            $amount = $expense->amount;
            $db = new DbOperation();
            $result = $db->addDischargeExpense($d_id, $description, $amount);
            if ($result == PROFILE_CREATED) {
                $responseData['error'] = false;
                $responseData['Message'] = "variables added sucessfully";
            } else {
                $responseData['error'] = true;
                $responseData['message'] = 'variables is not inserted';
            }
        }
    }
    $response->getBody()->write(json_encode($responseData));
});
// Get Pending Operates
$app->get('/getpendingoperation', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->getpendingoperation();
    $response->getBody()->write(json_encode($result));
});
//  Add Investigating
$app->post('/addInvestigations', function (Request $request, Response $response) {
    $requestData = json_decode($request->getBody());
    $inv_name = $requestData->inv_name;
    $inv_price = $requestData->inv_price;
    $db = new DbOperation();
    $responseData = array();
    if ($db->addInvestigating($inv_name, $inv_price)) {
        $responseData['error'] = false;
        $responseData['message'] = 'data inserted sucessfully';
    } else {
        $responseData['error'] = true;
        $responseData['message'] = 'data is not inserted';
    }
    $response->getBody()->write(json_encode($responseData));
});
// Get Investigating
$app->get('/getInvestigations', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->getInvestigations();
    $response->getBody()->write(json_encode($result));
});
$app->get('/getRoomsbyCat/{r_id}', function (Request $request, Response $response) {
    $r_id = $request->getAttribute('r_id');
    $db = new DbOperation();
    $result = $db->getOpearteCategory($r_id);
    $response->getBody()->write(json_encode($result));
});
$app->get('/getDischarged', function (Request $request, Response $response) {
    $db = new DbOperation();
    $result = $db->getDischarged();
    $response->getBody()->write(json_encode($result));
});

$app->run();
?>