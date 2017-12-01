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

function aggregate(callable ...$comparators){

    return function ($obj1, $obj2) use ($comparators)
    {
        foreach ($comparators as $comparator)
        {
            $compare_result = $comparator($obj1, $obj2);

            if($compare_result === 0)
                continue;
            else
                return $compare_result;
        }
        return 0;
    };
}

function createComparatorByExtractor($directionMultiplier)
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

function createComparator(callable $extractor, $flags = null)
{
    $nulls_flag = $flags & 0b11;
    $dir_flag = $flags & 0b100;

    if(SORT_ASC === $dir_flag)
        $comparatorByExtractor = createComparatorByExtractor(1);
    elseif (SORT_DESC === $dir_flag)
        $comparatorByExtractor = createComparatorByExtractor(-1);
    else
        $comparatorByExtractor = createComparatorByExtractor(1);

    if(NULLS_LAST === $nulls_flag)
        $comparatorByExtractor = handleNulls($comparatorByExtractor, 1);
    elseif (NULLS_FIRST === $nulls_flag)
        $comparatorByExtractor = handleNulls($comparatorByExtractor, -1);

    return partial_left($comparatorByExtractor, $extractor);
}

function handleNulls(callable $comparatorByExtractor, $directionMultiplier)
{
    return function($extractor, $obj1, $obj2) use ($comparatorByExtractor, $directionMultiplier)
    {
        $is_null1 = is_null($extractor($obj1));
        $is_null2 = is_null($extractor($obj2));
        if($is_null1 && !$is_null2)
            return 1 * $directionMultiplier;
        elseif(!$is_null1 && $is_null2)
            return -1 * $directionMultiplier;
        else
            return $comparatorByExtractor($extractor, $obj1, $obj2);
    };
}
