<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function str_binary_search($elem, $array) 
{
   $top = sizeof($array) -1;
   $bot = 0;
   while($top >= $bot) 
   {
      $p = floor(($top + $bot) / 2);
      if ("abc" . $array[$p] < "abc" . $elem)
      {
          $bot = $p + 1;
      }
      elseif ("abc" . $array[$p] > "abc" . $elem)
      { $top = $p - 1; }
      else { return TRUE; }
   }
   return FALSE;
}