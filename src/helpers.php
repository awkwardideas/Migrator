<?php

//String helpers
function indent($count=1){
    $indent = "    ";
    $indents = $indent;
    if ($count <= 1) {
        return $indents;
    } else {
        for ($i = 1; $i < $count; $i++) {
            $indents .= $indent;
        }
        return $indents;
    }
}

function after($needle, $haystack){
    if (!is_bool(strpos($haystack, $needle)))
        return substr($haystack, strpos($haystack, $needle) + strlen($needle));
}

function after_last($needle, $haystack){
    if (!is_bool(strrevpos($haystack, $needle)))
        return substr($haystack, strrevpos($haystack, $needle) + strlen($needle));
}

function before($needle, $haystack){
    if(strpos($haystack, $needle)>-1){
        return substr($haystack, 0, strpos($haystack, $needle));
    }else{
        return $haystack;
    }
}

function before_last($needle, $haystack){
    return substr($haystack, 0, strrevpos($haystack, $needle));
}

function between($needleStart, $needleEnd, $haystack){
    return before($needleEnd, after($needleStart, $haystack));
}

function between_last($needleStart, $needleEnd, $haystack){
    return after_last($needleStart, before_last($needleEnd, $haystack));
}

function strrevpos($instr, $needle){
    $rev_pos = strpos(strrev($instr), strrev($needle));
    if ($rev_pos === false)
        return false;
    else
        return strlen($instr) - $rev_pos - strlen($needle);
}