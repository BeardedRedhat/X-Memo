<?php
require "table.php";

class xml
{
    // Opens & instantiates the XML file
    public static function openXML($file) {
        if(empty($file)) throw new Exception("No file detected");
        $file = "memos.xml";
        if($fp  = fopen($file, "rb")) {
            $str = fread($fp, filesize($file));
            $xml = new DOMDocument();
            $xml->formatOutput = true;
            $xml->preserveWhiteSpace = false;
            if($xml->loadXML($str))
                return $xml;
            else
                Throw new Exception("Oops! Something went wrong..");
        } else {
            Throw new Exception("Error - XML not detected");
        }
    }

    // Creates a new child node within parent node
    public static function createNode($xml, $name, $text, $parentNode) {
        if(empty($name) || empty($text)) {
            Throw new exception("No parameters detected createNode()");
        }
        if(is_array($text)) {   // Check if text is array
            for($i=0; $i<count($text);$i++) {   // Loop through array and append node(s)
                $node = $xml->createElement($name);
                $textNode = $xml->createTextNode("$text[$i]");
                $node->appendChild($textNode);
                $parentNode->appendChild($node);
            }
        } else {
            $node = $xml->createElement($name);
            $textNode = $xml->createTextNode("$text");
            $node->appendChild($textNode);
            $parentNode->appendChild($node);
        }
    }

    // Searches XML document by author, date or recipient
    public static function searchXML($parentNode, $searchValue, bool $isDate, bool $isRecipient) {
        $searchRows  = "";   // Any returned rows from searches will be saved here
        $searchCount = 0;

        foreach($parentNode->childNodes as $memo) {
            $countChild = $memo->childNodes->length;    // Number of child nodes in memo, used for obtaining recipient(s)
            // Save child nodes into variables
            $id         = $memo->getAttribute('id');
            $title      = $memo->childNodes->item(0)->nodeValue;
            $author     = $memo->childNodes->item(1)->nodeValue;
            $date       = $memo->childNodes->item($countChild-3)->nodeValue;
            $body       = $memo->childNodes->item($countChild-2)->nodeValue;
            $url        = $memo->childNodes->item($countChild-1)->nodeValue;
            $recipients = "";     // Populated by loop

            for($i=2; $i<$countChild-3; $i++) { // Iterate through recipients and save to variable
                if($i > 2) {
                    $recipients .= ", ";
                }
                $recipients .= $memo->childNodes->item($i)->nodeValue;
            }

            if($isDate == true) {  // If search criteria is a date
                if($searchValue == $date) {
                    $searchCount++;
                    $searchRows .= table::addRow($id,$title,$author,$recipients,$date,$body,$url);
                }
            }
            else if($isRecipient == true) {  // If search criteria is a recipient
                $recipients_arr = explode(', ',$recipients); // Seperate recipients into array

                if(is_array($searchValue)) {
                    for($i=0; $i<count($recipients_arr); $i++) {
                        for($x=0; $x<count($searchValue); $x++) {
                            similar_text($recipients_arr[$i],$searchValue[$x],$pcentMatch);
                            if((strtolower($recipients_arr[$i]) == strtolower($searchValue[$x])) || $pcentMatch >= 90) {
                                $searchCount++;
                                $searchRows .= table::addRow($id,$title,$author,$recipients,$date,$body,$url);
                            }
                        }
                    }
                }
                else {
                    for($i=0; $i<count($recipients_arr); $i++) {
                        similar_text(strtolower($recipients_arr[$i]),$searchValue,$pcentMatch);
                        if(strtolower($recipients_arr[$i]) == strtolower($searchValue) || $pcentMatch >= 90) {
                            $searchCount++;
                            $searchRows .= table::addRow($id,$title,$author,$recipients_arr[$i],$date,$body,$url);
                        }
                    }
                }
            }
            else { // If search criteria is an author
                similar_text(strtolower($author),strtolower($searchValue),$pcentMatch); // Checks the similarity between 2 strings
                if(strtolower($author) == strtolower($searchValue) || $pcentMatch > 90) {
                    $searchCount++;
                    $searchRows .= table::addRow($id,$title,$author,$recipients,$date,$body,$url);
                }
            }
        }
        if($searchCount !== 0) {
            return $searchRows;
        } else {
            return "<tr><td colspan='6'>No Results Found</td></tr>";
        }
    }
}