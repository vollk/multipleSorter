<?php

/**
 * User: vollk
 * Date: 22.11.2017
 * Time: 22:59
 */

namespace multipleSorter;

const NULLS_UNDEF = 0b00;
const NULLS_FIRST = 0b01;
const NULLS_LAST = 0b10;

const SORT_ASC = 0b000;
const SORT_DESC = 0b100;

function partial_left(callable $callback, ...$arguments)
{
    return function (...$innerArguments) use ($callback, $arguments) {
        return $callback(...array_merge($arguments, $innerArguments));
    };
}

function aggregate(callable ...$sorters){

    return function ($obj1, $obj2) use ($sorters)
    {
        foreach ($sorters as $sorter)
        {
            $compare_result = $sorter($obj1, $obj2);

            if($compare_result === 0)
                continue;
            else
                return $compare_result;
        }
        return 0;
    };
}

function getSortByExtractor($directionMultiplier)
{
    return function (callable $extractor, $obj1, $obj2) use ($directionMultiplier)
    {
        if($extractor($obj1) > $extractor($obj2))
            return 1*$directionMultiplier;

        if($extractor($obj1) < $extractor($obj2))
            return -1 * $directionMultiplier;
        else
            return 0;
    };
}

function createSorter(callable $extractor, $flags = null)
{
    $nulls_flag = $flags & 0b11;
    $dir_flag = $flags & 0b100;

    if(SORT_ASC === $dir_flag)
        $sortByExtractor = getSortByExtractor(1);
    elseif (SORT_DESC === $dir_flag)
        $sortByExtractor = getSortByExtractor(-1);
    else
        $sortByExtractor = getSortByExtractor(1);

    if(NULLS_LAST === $nulls_flag)
        $sortByExtractor = handleNulls($sortByExtractor, 1);
    elseif (NULLS_FIRST === $nulls_flag)
        $sortByExtractor = handleNulls($sortByExtractor, -1);

    return partial_left($sortByExtractor, $extractor);
}

function handleNulls(callable $sortByExtractor, $directionMultiplier)
{
    return function($extractor, $obj1, $obj2) use ($sortByExtractor, $directionMultiplier)
    {
        $is_null1 = is_null($extractor($obj1));
        $is_null2 = is_null($extractor($obj2));
        if($is_null1 && !$is_null2)
            return 1 * $directionMultiplier;
        elseif(!$is_null1 && $is_null2)
            return -1 * $directionMultiplier;
        else
            return $sortByExtractor($extractor, $obj1, $obj2);
    };
}
