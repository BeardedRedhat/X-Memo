<?php

class table
{
    // Add rows onto table
    public static function addRow($id,$title,$author,$recipients,$date,$body,$url) {
        $row = "";
        $row .= "<tr>";
        $row .=     "<td>$id</td>";
        $row .=     "<td>$title</td>";
        $row .=     "<td>$author</td>";
        $row .=     "<td>$recipients</td>";
        $row .=     "<td>$date</td>";
        $row .=     "<td>$body<br /><br />For further information, please visit:<br /><a href='$url' target='_blank'>$url</a></td>";
        $row .= "</tr>";
        return $row;
    }
}