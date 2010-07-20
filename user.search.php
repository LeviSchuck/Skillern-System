<?php
require("include/base.php");
needrights(7);//sorry but no students 
//this page is the page that will literally preform the search and return the content. The result is to be put in like live AJAX.
$search = preg_split('/\s*"([^"]+)"\s*|\s+/i', trim($_POST['searchTerms']), null, 2);// will allow search for "haa haah" for its own term instead of like "haa and haah"
//for debug purposes uncomment the following line to test for a user named "Kelly"
//$search = array('lydia');
//now we need to clear out any bad entries, like just symbols, and we need to make sure that 
//now to prepare the SQL
$sql = "SELECT 'skllern_users'.'ID', 'skllern_users'.'firstname', 'skllern_users'.'lastname', 'skllern_users'.'classperiod', 'skllern_users'.'usertype' FROM 'skllern_users' WHERE ";
//ok so we now have  the base but we need more.

//okay, so, we want to have a special mode so we can search for period:1 etc. So, we should go and add an acceptable (*): \1 array of good keywords lower case.
$special = array('fname','lname', 'period');
//and something to match the special array.
$special2 = array("'skllern_users'.'firstname'","'skllern_users'.'lastname'","'skllern_users'.'classperiod'");
$anded = false;//This means have we used AND yet, or rather, is it needed?
$searchColumns = array('username', 'firstname','lastname','email');
foreach($search as $term){
    $preterm = strtolower(preg_replace('/[^\s\w":]+/i', '',$term));
    //now! we need to detect... if we are special.
    $notspecial = true;
    if(stringContains($preterm,':')){
        $specialexp = explode(':',$preterm,2);
        $notspecial = false;//we 'should' be special at this point, but we might not be.
        if(in_array($specialexp[0],$special)){
            if($anded){
                //insert the AND
                $sql .= ' AND ';
            }else{
                $anded = true;//so next time we have the AND inserted
            }
            $sql .=  $special2[array_search($specialexp[0], $special)] .' LIKE \'%'.$specialexp[1].'%\' ';
            
        }else{
            $notspecial = true;
        }
    }
    if($notspecial){
        $term = sqlite_escape_string($preterm);
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
}
$sql .= " ORDER BY 'skllern_users'.'classperiod' ASC, 'skllern_users'.'lastname' ASC, 'skllern_users'.'firstname'";
//SQL is now prepared, time to do it and prepare the feedback.
$result = sqlite_query($sdb,$sql);
if(sqlite_num_rows($result) > 0){
    echo '<div class="searchResult">';
        echo '<div class="searchField firstname">First Name</div>';
        echo '<div class="searchField lastname">Last Name</div>';
        echo '<div class="searchField classperiod">Class Period</div>';
        echo '<div class="searchField usertype">User Type</div>';
    echo '</div>';//end search result div.
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