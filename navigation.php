<style>
    .navbar.navbar-default {
        background-color: #283c75 !important;
        border:none;
        border-radius:0 !important;
        width:100%;
        box-shadow: 0 4px 6px 0 rgba(0, 0, 0, 0.2), 0 6px 10px 0 rgba(0, 0, 0, 0.5);
        -webkit-box-shadow: 0 4px 6px 0 rgba(0, 0, 0, 0.2), 0 6px 10px 0 rgba(0, 0, 0, 0.5);
        -moz-box-shadow: 0 4px 6px 0 rgba(0, 0, 0, 0.2), 0 6px 10px 0 rgba(0, 0, 0, 0.5);
    }

    .nav.navbar-nav li > a { color:white; }

    ul li.active > a {
        background-color: #293d73 !important;
        border-bottom:3px solid #1995d5;
    }
</style>

<nav class="navbar navbar-default">
    <div class="pull-left">
        <img src="img/profilePic.png" class="img-circle" style="height:35px; width:35px; margin:8px 15px 8px 15px;" />
    </div>
    <div class="container" style="margin:0;">
        <ul class="nav navbar-nav">
            <li class="<?=$activeNav == 'home' ? 'active' : ''?>">
                <a href="index.php" style="color:white">
                    <span class="fa fa-home" aria-hidden="true"></span> Home
                </a>
            </li>
            <li class="<?=$activeNav == 'create' ? 'active' : ''?>">
                <a href="createNewMemo.php" style="color:white">
                    <span class="fa fa-pencil" aria-hidden="true"></span> Create New Memo
                </a>
            </li>
        </ul>
    </div>
</nav>
