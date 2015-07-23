<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?php echo $Config['page']['title']; ?> | Elements Community</title>
  
  <!-- core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animate.min.css" rel="stylesheet">
    <link href="css/prettyPhoto.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
    <link href="css/jquery-ui.min.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
</head><!--/head-->

<body class="homepage">

    <header id="header">
        <div class="top-bar">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6 col-xs-4">
                        <div class="top-number"><p></p></div>
                    </div>
                    <div class="col-sm-6 col-xs-8">
                      <div class="account">
                        <?php 
                        if(!empty($_SESSION['user'])) {
                          echo '<ul>';
                          echo '<li class="dropdown">';
                          echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $_SESSION['user']['username'] . ' <i class="fa fa-angle-down"></i></a>';
                          echo '<ul class="dropdown-menu">';
                          echo '<li><a href="logout.php">Logout</a></li>';
                          echo '</ul>';
                          echo '</li>';
                          echo '</ul>';
                        }
                        ?>
                     </div>
                    </div>
                </div>
            </div><!--/.container-->
        </div><!--/.top-bar-->

        <nav class="navbar navbar-inverse" role="banner">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.php">Elements Community</a>
            </div>
    
            <div class="collapse navbar-collapse navbar-right">
              <ul class="nav navbar-nav">
                  <li class="active"><a href="index.php">Home</a></li>
                  <?php if(!empty($_SESSION['user']['admin']) || !empty($_SESSION['user']['auth'])) { ?>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin</a>
                    <ul class="dropdown-menu">
                      <?php if(!empty($_SESSION['user']['admin'])) { ?>
                      <li><a href="manage-user-access.php">Manage Access</a></li>
                      <?php } if(!empty($_SESSION['user']['admin']) || !empty($_SESSION['user']['auth']['Council'])) { ?>
                      <li><a href="council-setup.php">Council Setup</a></li>
                      <?php } ?>
                    </ul>
                  </li>
                  <?php } ?>
                  <?php if(!empty($_SESSION['user'])) { ?><li><a href="council.php">Council Voting</a></li><?php } ?>
              </ul>
            </div>
          </div><!--/.container-->
        </nav><!--/nav-->
    
    </header><!--/header-->