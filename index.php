<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,follow">
<title>南區學員管理系統</title>
<?php if(isset($_GET["loginFail"])) { ?>
<script language="javascript">
alert("登入失敗，請重新登入");
window.location.href = "./index.php";
</script>
<?php } ?>
<link rel="icon" href="./_res/img/icons.ico" type="image/x-icon">
<link rel="shortcut icon" href="./_res/img/icons.ico">
<link href="./_res/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="./_res/bootstrap/css/normalize.css" rel="stylesheet" type="text/css"/>
<script src="./_res/js/jquery-2.1.1.min.js" type="text/javascript" ></script>
<script src="./_res/bootstrap/js/bootstrap.min.js" type="text/javascript" ></script>
<script src="./_res/js/index.js?{F017A570-2E23-4359-BF3C-E9E58038C23E}" type="text/javascript"></script>

<style type="text/css">
html, body{height:100%; margin:0;padding:0;font-family:Meiryo,"微軟正黑體","Microsoft JhengHei";}
.container-fluid{height:90%;display:table;width:100%;padding:0;}
.row-fluid{height:100%; display:table-cell; vertical-align: middle;}
.centering{float:none;margin:0 auto;}
.righting{float:right;margin:0 auto;}
</style>


</head>
<body>

<div class="container-fluid">
<div class="row-fluid">
    <div class="col-sm-3"></div>
    <div class="col-sm-3"><div class="centering text-center"><img src="./_res/img/sign-in-logo.png" class="img-responsive"></div></div>
    <div class="col-sm-3">
        <form style="font-size:14px;" method="post" action="login.php">
            <p  style="height:42px;">請輸入帳號及密碼登入法會報名系統！</p>
            <hr>
            <?php
                $in="rc";
                if(isset($_GET["in"])){$in=$_GET["in"];};
                $select[0]="";$select[1]="";$select[2]="";$select[3]="";$select[4]="";
                if($in=="rc"){$select[0]=" selected";}
                if($in=="cm"){$select[1]=" selected";}
                if($in=="stu"){$select[2]=" selected";}
                if($in=="bks"){$select[3]=" selected";}
                if($in=="cls"){$select[4]=" selected";}
                echo "<select class='form-control input-default' id='func' name='func'>";
                echo "<option value='ceremony'".$select[1].">法會報名</option>";
                echo "</select>";
            ?>
            <div class="form-group">
            <br>
            <input type="account" class="form-control" id="username" name="username" placeholder="帳號">
            <br>
            <input type="password" class="form-control" id="password" name="password" placeholder="密碼">
            </div>
            <hr>
            <div class="righting text-center">
            <button type="submit" class="btn btn-primary">登  入</button>
            </div>
        </form>
    </div>
    <div class="col-sm-3"></div>
</div>
</div>

</body>
</html>
