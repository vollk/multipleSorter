<?php
/**
 * User: vollk
 * Date: 22.11.2017
 * Time: 23:44
 */

require_once 'multipleSorter.php';

use function multipleSorter\createSorter;
use function multipleSorter\aggregate;

class Product
{
    private $id;
    private $name;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }
}

$getId = function (Product $p) {
    return $p->getId();
};
$getName = function (Product $p) {
    return $p->getName();
};

$sorterByName = createSorter($getName);
$sorterById = createSorter($getId);

$sortAggregate = aggregate($sorterByName, $sorterById);


$data = [
    new Product(7, 'p1'),
    new Product(3, 'p1'),
    new Product(5, 'p2'),
    new Product(8, 'p1'),
];


usort($data, $sortAggregate);

var_dump($data);