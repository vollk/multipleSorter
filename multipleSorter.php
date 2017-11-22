<?php

/**
 * User: vollk
 * Date: 22.11.2017
 * Time: 22:59
 */

namespace multipleSorter;

function partial_left(callable $callback, ...$arguments)
{
    return function (...$innerArguments) use ($callback, $arguments) {
        return $callback(...array_merge($arguments, $innerArguments));
    };
}

function aggregate(...$sorters){

    return function ($obj1, $obj2) use ($sorters)
    {
        foreach ($sorters as $extractor)
        {
            $compare_result = $extractor($obj1, $obj2);

            if($compare_result === 0)
                continue;
            else
                return $compare_result;
        }
        return 0;
    };
}

function getSortByExtractor()
{
    return function (callable $extractor, $obj1, $obj2)
    {
        if($extractor($obj1) > $extractor($obj2))
            return 1;

        if($extractor($obj1) < $extractor($obj2))
            return -1;
        else
            return 0;
    };
}

function createSorter(callable $extractor)
{
    $sortByExtractor = getSortByExtractor();
    return partial_left($sortByExtractor, $extractor);
}