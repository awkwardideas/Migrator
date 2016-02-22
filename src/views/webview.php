<?php
/**
 * User: dachusa
 * Date: 2/15/2016
 * Time: 12:44 PM
 */
$myLaravel = new MyLaravelMigrate();
?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha256-7s5uDGW3AHqw6xtJmNNtr+OBRJUlgkNJEo78P4b0yRw= sha512-nNo+yCHEyn0smMxSswnf/OnX6/KwJuZTlNZBjauKhTK0c+zT+q5JOCx0UFhXQ6rJR9jg6Es8gPuD2uZcYDLqSw==" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha256-KXn5puMvxCw+dAYznun+drMdG1IFl3agK0p/pqT9KAo= sha512-2e8qq0ETcfWRI4HJBzQiA3UoyFk6tbNyG+qSaIBZLyW9Xf3sWZHN/lxe9fTh1U45DpPf07yj94KsUHHWe4Yk1A==" crossorigin="anonymous"></script>
    <script type="text/javascript">
        jQuery(function(){
            $(".saveToFile").click(
                function(){
                    saveTextAsFile(this);
                }
            );

            $(".saveAll").click(function(){
                $(".saveToFile").each(function(){
                    $(this).click();
                });
            });
        });

        function toggle_visibility(tableData){
            if (tableData.classList.contains('active'))
                tableData.classList.remove('active');
            else tableData.classList.add('active');
        }

        function saveTextAsFile(clicked){
            var textToWrite = $("#table-"+$(clicked).attr("data-table") + " textarea").val();
            var textFileAsBlob = new Blob([textToWrite], {type:'text/plain'});
            var d = new Date();
            var fileNameToSaveAs = d.getFullYear() + "_" + (d.getMonth()+1) + "_" + d.getDate() + "_" + d.getHours() + d.getMinutes() + d.getSeconds() + "_create_"+$(clicked).attr("data-table")+"_table.php";
            var downloadLink = document.createElement("a");
            downloadLink.download = fileNameToSaveAs;
            downloadLink.innerHTML = "create_"+$(clicked).attr("data-table")+"_table";
            window.URL = window.URL || window.webkitURL;
            downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
            downloadLink.onclick = destroyClickedElement;
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);downloadLink.click();
        }
        function destroyClickedElement(event){
            document.body.removeChild(event.target);
        }
    </script>
</head>
<body>
<div class="container">
    <div class="row jumbotron">
        <div class="col-md-7">
            <h1>My Laravel Migrate</h1>
        </div>
        <div class="col-md-5">
            <form action="" method="post">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group"><label for="host">Host:</label> <input type="text" class="form-control" id="host" name="host" placeholder="ex. localhost" value="<?php echo $myLaravel->GetHost();?>" /></div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group"><label for="database">Database:</label> <input type="text" class="form-control" id="database" name="database" placeholder="test" value="<?php echo $myLaravel->GetDatabase();?>" /></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group"><label for="username">Username:</label> <input type="text" class="form-control" id="username" name="username" placeholder="root" value="<?php echo $myLaravel->GetUsername();?>" /></div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group"><label for="password">Password:</label> <input type="text" class="form-control" id="password" name="password" placeholder="pass123" value="<?php echo $myLaravel->GetPassword();?>" /></div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success pull-right" title="Connect"><span class="glyphicon glyphicon-play" aria-hidden="true"></span></button>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <button class='btn btn-info saveAll pull-right'><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span></button>
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#migrations" aria-controls="migrations" role="tab" data-toggle="tab">Migrations</a></li>
            <li role="presentation"><a href="#models" aria-controls="models" role="tab" data-toggle="tab">Models</a></li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="migrations"><?php echo $myLaravel->GetMigrations(); ?></div>
            <div role="tabpanel" class="tab-pane" id="models"><?php echo $myLaravel->GetModels(); ?></div>
        </div>
    </div>
</div>
</body>
</html>