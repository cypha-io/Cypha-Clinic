<?php 
include './config/connection.php';

$message = '';

if(isset($_POST['save_user'])) {
  $displayName = $_POST['display_name'];
  $userName = $_POST['user_name'];
  $password = $_POST['password'];

  $encryptedPassword = md5($password);

//$targetDir = "user_images/";
$baseName = basename($_FILES["profile_picture"]["name"]);

//time is a php function which gives unix time value.
//unix time value is all seconds from 1970

//abc.x.y.z.png

// $extArr = explode(".", $baseName);
// $extension = end($extArr);

//$targetFile =  time(). '.'.$extension;

$targetFile =  time().$baseName;
// 12312312312312312
//abc.jpg
//abc.jpg
//244574700_322087779604661_8207402889226768946_n

  $status = move_uploaded_file($_FILES["profile_picture"]["tmp_name"], 
    'user_images/'.$targetFile);

  if($status) {
    try {
      $con->beginTransaction();

          $query = "INSERT INTO `users`(`display_name`,
`user_name`, `password`, `profile_picture`) 
VALUES('$displayName', '$userName', '$encryptedPassword', '$targetFile');";

    $stmtUser = $con->prepare($query);
    $stmtUser->execute();

    $con->commit();

    $message = 'user registered successfully';    

    } catch(PDOException $ex) {
      $con->rollback();
      echo $ex->getTraceAsString();
      echo $ex->getMessage();
      exit;
    }

  } else {
    $message = 'a problem occured in image uploading.';
  }

header("location:congratulation.php?goto_page=users.php&message=$message");
exit;
}
  //https://www.w3schools.com/php/php_file_upload.asp
/**
we will save the user picture in a separate folder.
and in database we will store the picture name only.

ON THE OTHER HAND
mysql supports blob data for storing pictures, 
but we are not going to use it. why?
find reason?
*/

$queryUsers = "select `id`, `display_name`, `user_name`, 
`profile_picture` from `users` 
order by `display_name` asc;";
$stmtUsers = '';

try {
    $stmtUsers = $con->prepare($queryUsers);
    $stmtUsers->execute();

} catch(PDOException $ex) {
      echo $ex->getTraceAsString();
      echo $ex->getMessage();
      exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <?php include './config/site_css_links.php';?>

 
 <?php include './config/data_tables_css.php';?>
 <title>Users - Clinic's Patient Management System in PHP</title>

 <style>
  .user-img{
    width:3em;
    width:3em;
    object-fit:cover;
    object-position:center center;
  }
 </style>
</head>
<body class="hold-transition sidebar-mini dark-mode layout-fixed layout-navbar-fixed">
  <!-- Site wrapper -->
  <div class="wrapper">
    <!-- Navbar -->
    <?php include './config/header.php';
include './config/sidebar.php';?>  
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Users</h1>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>
      <!-- Main content -->
      <section class="content">
        <!-- Default box -->
        <div class="card card-outline card-primary rounded-0 shadow">
          <div class="card-header">
            <h3 class="card-title">Add User</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <form method="post" enctype="multipart/form-data">
             <div class="row">

              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                <label>Display Name</label>
                <input type="text" id="display_name" name="display_name" required="required"
                class="form-control form-control-sm rounded-0" />
              </div>

              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                <label>Username</label>
                <input type="text" id="user_name" name="user_name" required="required"
                class="form-control form-control-sm rounded-0" />
              </div>

              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                <label>Password</label>
                <input type="password" id="password" 
                name="password" required="required"
                class="form-control form-control-sm rounded-0" />
              </div>

              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                <label>Picture</label>
                <input type="file" id="profile_picture" 
                name="profile_picture" required="required"
                class="form-control form-control-sm rounded-0" />
              </div>

              <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                <label>&nbsp;</label>
                <button type="submit" id="save_medicine" 
                name="save_user" class="btn btn-primary btn-sm btn-flat btn-block">Save</button>
              </div>
            </div>
          </form>
        </div>

      </div>
      <!-- /.card -->
    </section>
    <section class="content">
      <!-- Default box -->
      
      <div class="card card-outline card-primary rounded-0 shadow">
        <div class="card-header">
          <h3 class="card-title">All Users</h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
            
          </div>
        </div>
        <div class="card-body">
         <div class="row table-responsive">

          <table id="all_users" 
          class="table table-striped dataTable table-bordered dtr-inline" 
          role="grid" aria-describedby="all_users_info">
          <colgroup>
            <col width="5%">
            <col width="10%">
            <col width="50%">
            <col width="25%">
            <col width="10%">
          </colgroup>
          <thead>
            <tr>
             <th class="p-1 text-center">S.No</th>
             <th class="p-1 text-center">Picture</th>
             <th class="p-1 text-center">Display Name</th>
             <th class="p-1 text-center">Username</th>
             <th class="p-1 text-center">Action</th>
           </tr>
         </thead>

         <tbody>
          <?php 
          $serial = 0;
          while($row = $stmtUsers->fetch(PDO::FETCH_ASSOC)) {
           $serial++;
           ?>
           <tr>
             <td class="px-2 py-1 align-middle text-center"><?php echo $serial;?></td>
             <td class="px-2 py-1 align-middle text-center">
               <img class = "img-thumbnail rounded-circle p-0 border user-img" src="user_images/<?php echo $row['profile_picture'];?>">
             </td>
             
             <td class="px-2 py-1 align-middle"><?php echo $row['display_name'];?></td>
             <td class="px-2 py-1 align-middle"><?php echo $row['user_name'];?></td>

             <td class="px-2 py-1 align-middle text-center">
                <a href="update_user.php?user_id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm btn-flat">
                  <i class="fa fa-edit"></i> 
                </a>
              </td>
         </tr>
       <?php } ?>
     </tbody>
   </table>
 </div>
</div>

<!-- /.card-footer-->
</div>

<!-- /.card -->

</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?php 
include './config/footer.php';

$message = '';
if(isset($_GET['message'])) {
  $message = $_GET['message'];
}
?>  
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<?php include './config/site_js_links.php'; ?>
<?php include './config/data_tables_js.php'; ?>


<script>
  showMenuSelected("#mnu_users", "");

  var message = '<?php echo $message;?>';

  if(message !== '') {
    showCustomMessage(message);
  }

  
  $(document).ready(function() {

    $("#user_name").blur(function() {
      var userName = $(this).val().trim();
      $(this).val(userName);

      if(userName !== '') {
        $.ajax({
          url: "ajax/check_user_name.php",
          type: 'GET', 
          data: {
            'user_name': userName
          },
          cache:false,
          async:false,
          success: function (count, status, xhr) {
            if(count > 0) {
              showCustomMessage("This user name exists. Please choose another username");
              $("#save_user").attr("disabled", "disabled");

            } else {
              $("#save_user").removeAttr("disabled");
            }
          },
          error: function (jqXhr, textStatus, errorMessage) {
            showCustomMessage(errorMessage);
          }
        });
      }

    });    
  });
</script>
</body>
</html>