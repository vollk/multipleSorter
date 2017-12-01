<?php
/**
 * User: vollk
 * Date: 22.11.2017
 * Time: 23:44
 */

require_once '../src/multipleSorter.php';
require_once 'Product.php';

use function multipleSorter\createComparator;
use function multipleSorter\aggregate;
use const \multipleSorter\NULLS_LAST;
use const \multipleSorter\NULLS_FIRST;

use const \multipleSorter\SORT_ASC;
use const \multipleSorter\SORT_DESC;


$getId = function (Product $p) {
    return $p->getId();
};
$getName = function (Product $p) {
    return $p->getName();
};

$comparatorByName = createComparator($getName, NULLS_LAST | SORT_DESC);
$comparatorById = createComparator($getId, SORT_DESC);

$sortAggregate = aggregate($comparatorByName, $comparatorById);


/**
 * @var @Product
 */
$data = [
    new Product(7, 'p1'),
    new Product(3, 'p1'),
    new Product(5, 'p2'),
    new Product(8, 'p1'),
    new Product(9, null),
];


usort($data, $sortAggregate);

foreach ($data as $product)
{
    printf("name: %s , id: %d <br>",$product->getName(), $product->getId());
}