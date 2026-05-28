<?php $current_page = basename($_SERVER['PHP_SELF']); ?>

<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <?php 
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start(); 
                    }
                        
                    $user_img = (!empty($_SESSION['user_image'])) ? $_SESSION['user_image'] : 'profile_small.png';
                    $user_name = (!empty($_SESSION['user_name'])) ? $_SESSION['user_name'] : 'User';
                    ?>
                    
                    <img alt="image" class="rounded-circle" 
                        src="img/<?php echo $user_img; ?>" 
                        style="width: 48px; height: 48px; object-fit: cover;">

                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="block m-t-xs font-bold">
                            <?php echo $user_name; ?> <b class="caret"></b>
                        </span>
                    </a>
                    
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                        <li class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </li>

            <li class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                <a href="index.php"><i class="fa fa-th-large"></i> <span class="nav-label">Dashboard</span></a>
            </li>
            <li class="<?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
                <a href="profile.php"><i class="fa fa-user"></i> <span class="nav-label">Profile</span></a>
            </li>

            <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] !== 'user'): ?>
            <li class="<?php echo ($current_page == 'users.php') ? 'active' : ''; ?>">
                <a href="users.php"><i class="fa fa-users"></i> <span class="nav-label">Users</span></a>
            </li>
            <li class="<?php echo ($current_page == 'checkins.php') ? 'active' : ''; ?>">
                <a href="checkins.php"><i class="fa fa-calendar"></i> <span class="nav-label">Checkins</span></a>
            </li>
            <?php endif; ?>

        </ul>
    </div>
</nav>