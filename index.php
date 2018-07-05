<?php
require_once "xml.php";

$activeNav = "home";  // Used for navigation & title
$panelHeaderTitle = "<span class=\"fa fa-list\" aria-hidden=\"true\"></span> Browse All Memos by ID"; // Changes if a search is applied

$xml = xml::openXML("memos.xml");  // Load XML file
$root = $xml->documentElement;  // Points to 'archive' element
$memos = $root->childNodes->item(0); // Points to 'memos' element

$rows = "";
$memoArray = array();
$count = 0;

foreach($memos->childNodes as $memo) {
    $countChild = $memo->childNodes->length;    // Number of child nodes in memo, used for obtaining recipient(s)

    $id         = $memo->getAttribute('id');
    $title      = $memo->childNodes->item(0)->nodeValue;
    $author     = $memo->childNodes->item(1)->nodeValue;
    $date       = $memo->childNodes->item($countChild-3)->nodeValue;
    $body       = $memo->childNodes->item($countChild-2)->nodeValue;
    $url        = $memo->childNodes->item($countChild-1)->nodeValue;
    $recipients = "";   // Populated by loop

    for($i=2; $i<$countChild-3; $i++) { // Iterate through recipients and save to variable
        if($i > 2) {
            $recipients .= ", ";
        }
        $recipients .= $memo->childNodes->item($i)->nodeValue;
    }
    $memoArray[$id] = array($title,$author,$recipients,$date,$body,$url); // Save details in 2D array w/ memo ID as key
}
ksort($memoArray); // Sort array by key asc (memo ID)

foreach($memoArray as $id => $content) {  // Add data to table rows
    $rows .= table::addRow($id,$content[0],$content[1],$content[2],$content[3],$content[4],$content[5]);
}


if($_SERVER['REQUEST_METHOD'] == "POST")
{
    // Author Search
    if(!empty($_POST['btnSearchAuthor'])) {
        $search = strip_tags($_POST['txtSearchAuthor']);
        // Validation
        if(empty($search))
            $error = "Search field is empty. Try typing something in first.";
        if(strlen($search) > 50)
            $error = "Search character limit exceeded. Max: 50.";

        if(!isset($error)) {
            $panelHeaderTitle = "<span class=\"fa fa-search\" aria-hidden=\"true\"></span> Search results for \"".ucfirst($_POST['txtSearchAuthor'])."\"";
            $searchRows = xml::searchXML($memos,$search,false,false);
        }
    }

    // Date Search
    if(!empty($_POST['btnSearchDate'])) {
        if(!filter_var($_POST['txtDateDay'],FILTER_VALIDATE_INT) || !filter_var($_POST['txtDateMonth'],FILTER_VALIDATE_INT) || !filter_var($_POST['txtDateYear'],FILTER_VALIDATE_INT))
            $error = "Only number values allowed in Date fields.";
        else if(checkdate($_POST['txtDateMonth'],$_POST['txtDateDay'],$_POST['txtDateYear']) == false)  // Checks if date is valid
            $error = "Invalid date given.";

        $search = strip_tags($_POST['txtDateYear'])."-".strip_tags($_POST['txtDateMonth'])."-".strip_tags($_POST['txtDateDay']); // Saves search in xml format yyyy-mm-dd

        if(!isset($error)) {
            $panelHeaderTitle = "<span class=\"fa fa-search\" aria-hidden=\"true\"></span> Search results for \"".$search."\"";
            $searchRows = xml::searchXML($memos,$search,true,false);
        }
    }

    // Recipient Search
    if(!empty($_POST['btnSearchRecipient'])) {
        $search = strip_tags($_POST['txtSearchRecipient']);

        if(strpos($search,",") !== false) {
            $search = explode(",",$search);
            foreach ($search as $value) {
                if(empty($value))
                    $error = "Search field is empty. Try typing something in first.";
                if(strlen($value) > 50)
                    $error = "Search character limit exceeded. Max: 50.";
            }

            if(!isset($error)) {
                $panelHeaderTitle = "<span class=\"fa fa-search\" aria-hidden=\"true\"></span> Search results for \"".ucfirst($_POST['txtSearchRecipient'])."\"";
                $searchRows = xml::searchXML($memos,$search,false,true);
            }
        }
        else {
            if(empty($search))
                $error = "Search field is empty. Try typing something in first.";
            if(strlen($search) > 50)
                $error = "Search character limit exceeded. Max: 500.";

            if(!isset($error)) {
                $panelHeaderTitle = "<span class=\"fa fa-search\" aria-hidden=\"true\"></span> Search results for \"".ucfirst($_POST['txtSearchRecipient'])."\"";
                $searchRows = xml::searchXML($memos,$search,false,true);
            }
        }
    }
}

require "header.php";
require "navigation.php"; ?>

<style>

    div.title {
        font-size:20px;
        text-align:center;
        padding-top:10px;
        width:100%;
    }

    .table.table-striped { margin-bottom: 0!important; }

    .table-striped tbody tr { vertical-align:middle; }
    .table-striped tbody tr td:nth-child(1) { width:5%  }
    .table-striped tbody tr td:nth-child(2) { width:15% }
    .table-striped tbody tr td:nth-child(3) { width:15% }
    .table-striped tbody tr td:nth-child(4) { width:15% }
    .table-striped tbody tr td:nth-child(5) { width:10% }
    .table-striped tbody tr td:nth-child(6) { width:40% }

    div.date-input {
        width:25%;
        display:inline-block;
    }
    div.date-sep {
        width:4%;
        display:inline-block;
        font-size:16px
    }

</style>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="col-xs-12 title" align="center">
            COM506 Assignment A: XML and its Applications<br /><br />
            X-Memo Archive<hr />
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="row-fluid">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <form method="post" enctype="multipart/form-data" name="frmSearchAuthor">
                <div class="panel panel-info">
                    <div class="panel-heading"><span class="fa fa-user" aria-hidden="true"></span> Search by Author</div>
                    <div class="panel-body" style="padding-bottom:67px">
                        <div class="input-group">
                            <input type="text" class="form-control" name="txtSearchAuthor" title="Search by Author" placeholder="Search author..." />
                            <span class="input-group-btn">
                                <input type="submit" class="btn btn-default" name="btnSearchAuthor" value="Search" />
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <form method="post" enctype="multipart/form-data" name="frmSearchDate">
                <div class="panel panel-info">
                    <div class="panel-heading"><span class="fa fa-calendar" aria-hidden="true"></span> Search by Date</div>
                    <div class="panel-body" align="center">
                        <div class="row-fluid">
                            <div class="dateInp">
                                <div class="date-input">
                                    <input type="text" class="form-control" name="txtDateDay" title="Day" placeholder="DD" />
                                </div>
                                <div class="date-sep">&nbsp;/&nbsp;</div>
                                <div class="date-input">
                                    <input type="text" class="form-control" name="txtDateMonth" title="Month" placeholder="MM" />
                                </div>
                                <div class="date-sep">&nbsp;/&nbsp;</div>
                                <div class="date-input">
                                    <input type="text" class="form-control" name="txtDateYear" title="Year" placeholder="YYYY" />
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row-fluid">
                            <br /><input type="submit" class="btn btn-default" value="Search" name="btnSearchDate" />
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <form method="post" enctype="multipart/form-data" name="frmSearchRecipient">
                <div class="panel panel-info">
                    <div class="panel-heading"><span class="fa fa-users" aria-hidden="true"></span> Search by Recipient</div>
                    <div class="panel-body">
                        <div class="row-fluid">
                            <div class="input-group">
                                <input type="text" class="form-control" name="txtSearchRecipient" title="Search by Recipient" placeholder="Search recipient..." />
                                <span class="input-group-btn">
                                <input type="submit" class="btn btn-default" name="btnSearchRecipient" value="Search">
                            </span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row-fluid" style="font-size:11px; color:#767676; padding-top:7px; margin-left:15px">
                            If you wish to search for more than one recipient, please seperate the names using a comma ','<br />
                            E.g. 'John Doe,Jane Doe'
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="clearfix"></div>

    <?php if(isset($error)) { ?>
        <div class="row-fluid">
            <div class="col-xs-12">
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Error!</strong> <?=$error?>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
    <?php } ?>

    <div class="row-fluid">
        <div class="col-xs-12">
            <div class="panel panel-info">
                <div class="panel-heading"><?=$panelHeaderTitle?></div>
                <div class="panel-body" style="max-height:600px; overflow-y:scroll; padding:0 !important;">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Recipient(s)</th>
                            <th>Date</th>
                            <th>Message</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(!empty($searchRows)) {
                            echo $searchRows;
                        } else {
                            echo $rows;
                        } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>