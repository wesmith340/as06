<?php

/*
  filename 	: covidData.php
  author   	: Weston Smith
  course   	: cis355 (winter2020)
  description	: print fomatted output from JSON object 
                  returned by Covid-19 API
  input    	: https://api.covid19api.com/summary
 */

main();

#-----------------------------------------------------------------------------
# FUNCTIONS
#-----------------------------------------------------------------------------

function main() {

    // echo html head section
    echo '<html>';
    echo '<head>';
    echo '<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">';
    echo '<script type="text/javascript" src="../bootstrap/js/bootstrap.min.js"></script>';
    echo '<style>
            .center{text-align : center; margin : 0px;}
            .right{text-align : right; margin : 0px;}
         </style>';
    echo '</head>';

    // open html body section
    echo '<body>';
    echo '<h1 align="center">Covid-19 Cases</h1>';
    echo '<h3 align="center">Highest populations infected with Covid-19</h3>';
    
    printTopTen();

    // close html body section
    echo '</body>';
    echo '</html>';
}
#-----------------------------------------------------------------------------

// print top ten countries with highest number of covid-19 cases
function printTopTen() {
    
    // get JSON object
    $apiCall = "https://api.covid19api.com/summary";

    $json = curl_get_contents($apiCall);
    $obj = json_decode($json);
    $topTen = array();
    
    // create empty array with dummy variables
    for ($i = 0; $i<10; $i++){
        $topTen[$i] = (object) array('Country' => 'N/A','TotalConfirmed' => '-1');
    }
    // if JSON object exists
    if (!($obj->Countries == null)) {
        // collect top ten countries
        foreach ($obj->Countries as $country) {
            $i = 0;
            $smaller = true;
            while ($i < 10 && $smaller){
                if ($country->TotalConfirmed > $topTen[$i]->TotalConfirmed){
                    $topTen[$i] = $country;
                    $smaller = false;
                }
                $i++;
            }
        }
        
        $odd = true;
        // create table
        echo '<div class="container">';
        echo '<table class = "table table-bordered">';
        echo '<tr>
                <th><p class="center">Country</p></th>
                <th><p class="center">Total Cases</p></th>
                <th><p class="center">Total Deaths</p></th>
                <th><p class="center">Total Recovered</p></th>
              <tr>';
        foreach ($topTen as $country){
            // even/odd coloring
            if ($country->Country != 'N/A'){
                if ($odd){
                    $printline = '<tr bgcolor="white">'; 
                    $odd = false;
                }else{
                    $printline = '<tr bgcolor="#c0c0c0">'; 
                    $odd = true;
                }
                $printline .= '<td width="20%">' . $country->Country . '</td>';
                $printline .= '<td width="20%"><p class="right">' . number_format($country->TotalConfirmed) . ' people infected</p></td>';
                $printline .= '<td width="20%"><p class="right">' . number_format($country->TotalDeaths) . ' people dead</p></td>';
                $printline .= '<td width="20%"><p class="right">' . number_format($country->TotalRecovered) . ' people recovered</p></td>';
                $printline .= '</tr>';
                echo $printline;
            }
        }
        echo '</table>';
        echo '</div>';
        
    }

}
#-----------------------------------------------------------------------------

// read file into a string
function curl_get_contents($url) {

    // alternative to file_get_contents

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}
