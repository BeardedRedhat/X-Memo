<?php
$activeNav = "create";
require_once "xml.php";

if($_SERVER['REQUEST_METHOD'] == "POST")
{
    // Create new memo
    if(!empty($_POST['btnCreateNew'])) {
        $memoName   = strip_tags($_POST['txtMemoName']);
        $fullName   = strip_tags($_POST['txtMemoSender']);
        $dateDay    = strip_tags($_POST['txtDateDay']);  // Dates saved separately for validation
        $dateMonth  = strip_tags($_POST['txtDateMonth']);
        $dateYear   = strip_tags($_POST['txtDateYear']);
        $body       = strip_tags($_POST['txtBody']);
        $url        = !empty($_POST['txtUrl']) ? strip_tags($_POST['txtUrl']) : "-";
        $recipients = array();

        for($i=1; $i<11; $i++) {  // Add recipient(s) to array
            if(!empty($_POST['txtRecipient'.$i])) {
                $recipients[] = strip_tags($_POST['txtRecipient'.$i]);
            }
        }

        // Form validation
        if(strlen($memoName) > 50)
            $error = "Memo Name character limit exceeded. Max: 50";
        if(strlen($fullName) > 50)
            $error = "Your name character limit exceeded. Max: 50";
        if(filter_var($fullName,FILTER_VALIDATE_INT) == true)
            $error = "No numeric values allowed in name";
        if(checkdate($dateMonth,$dateDay,$dateYear) == false)
            $error = "Invalid date given";
        else
            $date = strip_tags($_POST['txtDateYear']) . "-" . strip_tags($_POST['txtDateMonth']) . "-" . strip_tags($_POST['txtDateDay']);
        if(strlen($body) > 1000)
            $error = "Message body character limit exceeded. Max: 1000";
        if(strpos($url,"-") == false) {
            if(strpos($url,"www.")) {
                $error = "Invalid URL given.";
            }
        }

        if(!isset($error)) {
            $xml = xml::openXML("memos.xml");  // Load XML file
            $root = $xml->documentElement;  // Points to 'archive' element
            $memos = $root->childNodes->item(0);    // Points to 'memos' element
            $memoCount = $memos->childNodes->length;
            $firstMemo = $memos->childNodes->item(0);   // Points to first memo

            // Create memo node and set ID attribute
            $newMemoNode = $xml->createElement("memo");
            $newMemoNode->setAttribute("id",$memoCount+1);

            $values = array("title" => $memoName, "author" => $fullName, "recipient" => $recipients,
                "date" => $date, "body" => $body, "url" => $url);

            foreach($values as $title => $value) {
                xml::createNode($xml,$title,$value,$newMemoNode);
            }

            $success = "New memo added.";
            $memos->insertBefore($newMemoNode,$firstMemo);
            $xml->save("memos.xml");
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

    div.clearfix { margin-bottom:1em }

    .dateInp > span { font-size:18px; }
    .dateInp > input {
        width:28% !important;
        display:inline-block;
    }

    .dateInp > label { display:block; text-align:left; }

    .divTxtArea { position:relative; }
    .divTxtArea textarea { width:100%; }
    .divTxtArea span {
        position:absolute;
        right:3px;
        bottom:6px;
        width:45px;
        text-align:center;
        z-index:1;
    }

    .str { color:red }
</style>


<div class="container-fluid">
    <div class="row-fluid">
        <div class="col-xs-12 title" align="center">
            Create a New Memo<hr />
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="row-fluid">
        <div class="col-lg-8 col-md-8 col-sm-10 col-xs-12 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
            <?php if(isset($success)) { ?>
                <div class="row-fluid">
                    <div class="alert alert-success alert-success" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Success!</strong> <?=$success?>
                    </div>
                </div>

                <div class="clearfix"></div>
            <?php } ?>

            <form method="post" name="frmCreateNew" onsubmit="return validateForm()">
                <div class="panel panel-info">
                    <div class="panel-heading"><span class="fa fa-plus" aria-hidden="true"></span> New Memo</div>
                    <div class="panel-body">

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
                                <label for="txtMemoName">Title <span class="str">*</span></label>
                                <input type="text" class="form-control required" name="txtMemoName" title="Memo Title" placeholder="Memo Title" />
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <div class="row-fluid">
                            <div class="col-lg-6 col-xs-12">
                                <label for="txtMemoSender">Your Full Name <span class="str">*</span></label>
                                <input type="text" class="form-control" name="txtMemoSender" title="Full Name" placeholder="Full name" />
                            </div>
                            <div class="col-lg-6 col-xs-12">
                                <div class="dateInp">
                                    <label for="txtDateDay">Date <span class="str">*</span></label>
                                    <input type="text" class="form-control" name="txtDateDay" title="Day" placeholder="DD" maxlength="2" /><span> &nbsp;/&nbsp;</span>
                                    <input type="text" class="form-control" name="txtDateMonth" title="Month" placeholder="MM" maxlength="2" /><span> &nbsp;/&nbsp;</span>
                                    <input type="text" class="form-control" name="txtDateYear" title="Year" placeholder="YYYY" maxlength="4" />
                                </div>
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <div class="row-fluid">
                            <div class="col-lg-6 col-xs-12">
                                <div class="input-block">
                                    <label class="control-label" for="txtRecipients1">Recipient(s) <span class="str">*</span></label>
                                    <input type="text" class="form-control" name="txtRecipient1" title="Recipient 1" placeholder="Recipient 1">
                                    <input type="text" class="form-control" name="txtRecipient2" title="Recipient 2" placeholder="Recipient 2">
                                    <input type="text" class="form-control" name="txtRecipient3" title="Recipient 3" placeholder="Recipient 3">
                                    <input type="text" class="form-control" name="txtRecipient4" title="Recipient 4" placeholder="Recipient 4">
                                    <input type="text" class="form-control" name="txtRecipient5" title="Recipient 5" placeholder="Recipient 5">
                                </div>
                            </div>
                            <div class="col-lg-6 col-xs-12">
                                <div class="input-block">
                                    <label class="control-label" for="txtRecipients1">&nbsp;</label>
                                    <input type="text" class="form-control" name="txtRecipient6" title="Recipient 6" placeholder="Recipient 6">
                                    <input type="text" class="form-control" name="txtRecipient7" title="Recipient 7" placeholder="Recipient 7">
                                    <input type="text" class="form-control" name="txtRecipient8" title="Recipient 8" placeholder="Recipient 8">
                                    <input type="text" class="form-control" name="txtRecipient9" title="Recipient 9" placeholder="Recipient 9">
                                    <input type="text" class="form-control" name="txtRecipient10" title="Recipient 10" placeholder="Recipient 10">
                                </div>
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <div class="row-fluid">
                            <div class="col-xs-12">
                                <label for="txtBody">Message <span class="str">*</span></label>
                                <div class="divTxtArea">
                                    <textarea class="form-control" name="txtBody" id="txtBody" title="Memo Body" rows="5" data-length="1000" placeholder="Message body" onkeyup="countChar(this,1000)"></textarea>
                                    <span id="count"></span>
                                </div>
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <div class="row-fluid">
                            <div class="col-xs-12">
                                <label for="txtUrl">Optional link for further information</label>
                                <input type="text" class="form-control" name="txtUrl" title="Optional link for further information" placeholder="e.g. www.google.co.uk" />
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <div class="row-fluid" align="center">
                            <input type="submit" name="btnCreateNew" class="form-contol btn btn-primary" value="Create Memo" />
                        </div>
                    </div><!--panel-body-->
                </div><!--panel-->
            </form>
        </div><!--col-lg-8-->
    </div><!--row-->
</div><!--container-->

<script>
    function countChar(charLen) {
        var dataValue = $('#txtBody').data('length');
        var txtareaLen = charLen.value.length;

        if(txtareaLen >= dataValue) {
            charLen.value = charLen.value.substring(0,dataValue);
        } else {
            $('#count').text(dataValue - txtareaLen);
        }
    }

    function validateForm() {
        var title     = document.forms['frmCreateNew']['txtMemoName'].value;
        var name      = document.forms['frmCreateNew']['txtMemoSender'].value;
        var dateDay   = document.forms['frmCreateNew']['txtDateDay'].value;
        var dateMonth = document.forms['frmCreateNew']['txtDateMonth'].value;
        var dateYear  = document.forms['frmCreateNew']['txtDateYear'].value;
        var recipient = document.forms['frmCreateNew']['txtRecipient1'].value;
        var body      = document.forms['frmCreateNew']['txtBody'].value;

        if(title.trim() == "" || name.trim() == "" || dateDay.trim() == "" || dateMonth.trim() == ""
            || dateYear.trim() == "" || body.trim() == "" || recipient.trim() == "") {
            alert("Please fill in required fields.");
            return false;
        }
    }
</script>

</body>
</htm