<?php
require("include/base.php");
needrights(7);
//this page is the page that will literally preform the search and return the content. The result is to be put in like live AJAX.
$search = preg_split('/\s*"([^"]+)"\s*|\s+/i', trim($_REQUEST['searchTerms']), null, 2);// will allow search for "haa haah" for its own term instead of like "haa and haah"
//for debug purposes uncomment the following line to test for a user named "Kelly"
//$search = array('lydia');
//now we need to clear out any bad entries, like just symbols, and we need to make sure that 
//now to prepare the SQL
$sql = "SELECT 'skllern_users'.'ID', 'skllern_users'.'firstname', 'skllern_users'.'lastname', 'skllern_users'.'classperiod', 'skllern_users'.'usertype' FROM 'skllern_users' WHERE ";
//ok so we now have  the base but we need more.
$anded = false;//This means have we used AND yet, or rather, is it needed?
$searchColumns = array('username', 'firstname','lastname','email');
foreach($search as $term){
    $term = sqlite_escape_string(preg_replace('/[^\s\w"]+/i', '',$term));
    if($anded){
        //insert the AND
        $sql .= ' AND ';
    }else{
        $anded = true;//so next time we have the AND inserted
    }
    $sql .= ' (';
    $ored = false;//meant for the columns, works for the same reason.
    foreach($searchColumns as $column){
        if($ored){
           $sql .= ' OR '; 
        }else{
            $ored = true;
        }
        $sql .= '\'skllern_users\'.\''.$column.'\' LIKE \'%'.$term.'%\' ';
    }
    $sql .= ' ) ';
    
}
//SQL is now prepared, time to do it and prepare the feedback.
$result = sqlite_query($sdb,$sql);
if(sqlite_num_rows($result) > 0){
while($row = sqlite_fetch_array($result)){
    echo '<div class="searchResult">';
        echo '<div class="searchField firstname">'.htmlentities($row["'skllern_users'.'firstname'"]).'</div>';
        echo '<div class="searchField lastname">'.htmlentities($row["'skllern_users'.'lastname'"]).'</div>';
        echo '<div class="searchField classperiod">'.htmlentities($row["'skllern_users'.'classperiod'"]).'</div>';
        echo '<div class="searchField usertype">'.usertypeToString($row["'skllern_users'.'usertype'"]).'</div>';
    echo '</div>';//end search result div.
    echo "\n";
}
}else{
    echo 'Your search resulted in 0 results. Please try fewer or incomplete terms as you might be spelling it wrong.';
}
?>