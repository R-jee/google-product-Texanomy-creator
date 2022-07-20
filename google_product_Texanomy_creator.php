<?php
header('Access-Control-Allow-Origin: *');
require_once("inc/database.php");
function googlecats()
{

    $json = true;
    $pool = $mitigate = $output = array();
    $pool = file('http://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $version = array_shift($pool);
    $mitigate_copy = [];
    foreach ($pool as $row) {
        list($id, $cat) = explode(' - ', $row);
        array_push($mitigate_copy,  array("g_cid" => $id, "g_cat" => $cat));
    }
    return ($mitigate_copy);
    unset($pool);
    unset($mitigate_copy);
}

$Google_taxonomy_ARRAY = googlecats();
$Primary_Category_Array = [];
$_1st_Sub_Category_Array = [];
$_2nd_Sub_Category_Array = [];
$_3rd_Sub_Category_Array = [];
$_4th_Sub_Category_Array = [];
$_5th_Sub_Category_Array = [];
$_6th_Sub_Category_Array = [];

foreach ($Google_taxonomy_ARRAY as $key => $Google_taxonomy_ARRAY_value) {

    if (count(explode(">", $Google_taxonomy_ARRAY_value['g_cat']))  == 1) {
        array_push($Primary_Category_Array, array('g_cid' => $Google_taxonomy_ARRAY_value['g_cid'], 'g_cat' => $Google_taxonomy_ARRAY_value['g_cat']));
    }
    if (count(explode(">", $Google_taxonomy_ARRAY_value['g_cat']))  == 2) {
        array_push($_1st_Sub_Category_Array, array('g_cid' => $Google_taxonomy_ARRAY_value['g_cid'], 'g_cat' => $Google_taxonomy_ARRAY_value['g_cat']));
    }
    if (count(explode(">", $Google_taxonomy_ARRAY_value['g_cat']))  == 3) {
        array_push($_2nd_Sub_Category_Array, array('g_cid' => $Google_taxonomy_ARRAY_value['g_cid'], 'g_cat' => $Google_taxonomy_ARRAY_value['g_cat']));
    }
    if (count(explode(">", $Google_taxonomy_ARRAY_value['g_cat']))  == 4) {
        array_push($_3rd_Sub_Category_Array, array('g_cid' => $Google_taxonomy_ARRAY_value['g_cid'], 'g_cat' => $Google_taxonomy_ARRAY_value['g_cat']));
    }
    if (count(explode(">", $Google_taxonomy_ARRAY_value['g_cat']))  == 5) {
        array_push($_4th_Sub_Category_Array, array('g_cid' => $Google_taxonomy_ARRAY_value['g_cid'], 'g_cat' => $Google_taxonomy_ARRAY_value['g_cat']));
    }
    if (count(explode(">", $Google_taxonomy_ARRAY_value['g_cat']))  == 6) {
        array_push($_5th_Sub_Category_Array, array('g_cid' => $Google_taxonomy_ARRAY_value['g_cid'], 'g_cat' => $Google_taxonomy_ARRAY_value['g_cat']));
    }
    if (count(explode(">", $Google_taxonomy_ARRAY_value['g_cat']))  == 7) {
        array_push($_6th_Sub_Category_Array, array('g_cid' => $Google_taxonomy_ARRAY_value['g_cid'], 'g_cat' => $Google_taxonomy_ARRAY_value['g_cat']));
    }
}
$Main_GOOGLE_ARRAY = array($Primary_Category_Array, $_1st_Sub_Category_Array, $_2nd_Sub_Category_Array, $_3rd_Sub_Category_Array, $_4th_Sub_Category_Array, $_5th_Sub_Category_Array, $_6th_Sub_Category_Array);
unset($Primary_Category_Array);
unset($_1st_Sub_Category_Array);
unset($_2nd_Sub_Category_Array);
unset($_3rd_Sub_Category_Array);
unset($_4th_Sub_Category_Array);
unset($_5th_Sub_Category_Array);
unset($_6th_Sub_Category_Array);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function GET_CATEGORYS__($Main_GOOGLE_ARRAY)
{
    $Final_table_array = [];
    $count = count($Main_GOOGLE_ARRAY);
    $first = 0;
    $second = 1;
    $first_array = $Main_GOOGLE_ARRAY[$first];
    $second_array = $Main_GOOGLE_ARRAY[$second];
    foreach ($first_array as  $first_array_value) {
        array_push($Final_table_array, ['c_id' => $first_array_value['g_cid'], 'p_id' => 0, 'name' => $first_array_value['g_cat'], 'texanomy' => $first_array_value['g_cat']]);
    }
    for ($c = 0; $c < $count - 1; $c++) {
        // $first = $first + $c; //    count 0
        // $second = $second + $c; //  count 1
        $first = $c; //    count 0
        $second = $c + 1; //  count 1
        $first_array = $Main_GOOGLE_ARRAY[$first];  //  array (0)
        $second_array = $Main_GOOGLE_ARRAY[$second];    //  array (1)
        for ($i = 0; $i < (count($first_array)); $i++) {
            $str = $first_array[$i]['g_cat'];
            $p_cid = $first_array[$i]['g_cid'];
            $parent_g_cid = 0;
            if ($c >= 1) {
                array_push($Final_table_array, ['c_id' => $p_cid, 'p_id' => $p_cid, 'name' => (explode(" > ", $str))[(count(explode(" > ", $str)) - 1)], 'texanomy' => $str]);
            }
            for ($j = 0; $j < (count($second_array)); $j++) {
                $p_str = $second_array[$j]['g_cat'];
                $child_cid = $second_array[$j]['g_cid'];
                $child_name =  (explode(" > ", $p_str))[(count(explode(" > ", $p_str)) - 1)];
                $child_name_texanomy =  $p_str;
                if (strpos($p_str, ($str . " > "))  !== false) {
                    array_push($Final_table_array, ['c_id' => $child_cid, 'p_id' => $p_cid, 'name' => $child_name, 'texanomy' => $child_name_texanomy]);
                }
            }
        }
    }
    return $Final_table_array;
}
$Final_table_array_temp =  GET_CATEGORYS__($Main_GOOGLE_ARRAY);
$LAST_final_executed_array = [];
foreach ($Final_table_array_temp  as $value) {  //   ----   start search foreach 

    if ($value['c_id'] == 0 || $value['c_id'] != $value['p_id']) {
        array_push($LAST_final_executed_array, ['c_id' => $value['c_id'], 'p_id' => $value['p_id'], 'name' => $value['name'], 'texanomy' => $value['texanomy']]);
    }
    if ($value['c_id'] == $value['p_id']) {
        $temp_sttr = "";
        $temp_p_str = "";
        $str = $value['texanomy'];
        for ($t = 0; $t < (count(explode(" > ", $value['texanomy'])) - 1); $t++) {
            if ($t == 0) {
                $temp_p_str .= (explode(" > ", $value['texanomy']))[$t];
            } else {
                $temp_p_str .=  (' > ' .   (explode(" > ", $value['texanomy']))[$t]);
            }
        }
        foreach ($Final_table_array_temp as $search_temp_value) {
            if ($search_temp_value['c_id'] != $search_temp_value['p_id']) {
                if (($temp_p_str) == ($search_temp_value['texanomy'])) {
                    $temp_p_cid = $search_temp_value['c_id'];
                    array_push($LAST_final_executed_array, ['c_id' => $value['c_id'], 'p_id' => $temp_p_cid, 'name' => $value['name'], 'texanomy' => $value['texanomy']]);
                }
            }
        }
    }
} //   ----   end search foreach 

foreach ($LAST_final_executed_array as $key =>  $value___) {
    $C_ID = $value___['c_id'];
    $P_ID = $value___['p_id'];
    $NAME =  str_replace("'", '`', $value___['name']);
    $Texanomy = str_replace("'", '`', $value___['texanomy']);
    $rows_count = $pdo->prepare("SELECT count(`id`) as count FROM `g_cat_table` where `texanomy` = '" . $Texanomy . "' ");
    $rows_count->execute();
    $rows_count_GOT = $rows_count->fetch();
    if ($rows_count_GOT['count'] == 0) {
        echo  $value___['c_id'] . ' --- ' . $value___['p_id'] . ' --- ' .  $value___['texanomy'] . "<br>\n";


        $statement = $pdo->prepare("INSERT INTO `g_cat_table` ( `g_cid`, `p_cid`, `name`, `texanomy`) VALUES ( '" . $C_ID . "' , '" . $P_ID . "' , '" . $NAME . "' , '" . $Texanomy . "' ) ");
        $statement->execute();
    }
}
