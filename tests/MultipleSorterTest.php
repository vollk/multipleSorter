<?php
/**
 * Date: 23.11.2017
 * Time: 16:34
 */

use function multipleSorter\createComparator;
use function multipleSorter\aggregate;
use const multipleSorter\NULLS_LAST;
use const multipleSorter\NULLS_FIRST;
use const multipleSorter\SORT_ASC;
use const multipleSorter\SORT_DESC;



class MultipleSorterTest extends \PHPUnit\Framework\TestCase
{
    private $data;
    private $sortedWithoutFlags;
    private $sortedNameDescIdDescNullsLast;

    private $getId;
    private $getName;

    public function setUp()
    {
        $this->createTestData();

        $this->getId = function (Product $p) {
            return $p->getId();
        };
        $this->getName = function (Product $p) {
            return $p->getName();
        };

        parent::setUp();
    }

    public function testWithouFlags()
    {
        $comparatorByName = createComparator($this->getName);
        $comparatorById = createComparator($this->getId);

        $comparatorAggregate = aggregate($comparatorByName, $comparatorById);

        usort($this->data, $comparatorAggregate);

        $ids = array_map($this->getId,$this->data);

        $this->assertTrue($this->sortedWithoutFlags === $ids);
    }

    public function testNameDescIdDescNullsLast()
    {
        $comparatorByName = createComparator($this->getName, SORT_DESC | NULLS_LAST);
        $comparatorById = createComparator($this->getId, SORT_DESC);

        $sortAggregate = aggregate($comparatorByName, $comparatorById);

        usort($this->data, $sortAggregate);

        $ids = array_map($this->getId,$this->data);

        $this->assertTrue($this->sortedNameDescIdDescNullsLast === $ids);
    }

    public function testOneComparator()
    {
        $comparatorById = createComparator($this->getId);
        usort($this->data, $comparatorById);

        $ids = array_map($this->getId,$this->data);

        $this->assertTrue([1,2,3,4,5,6] === $ids);
    }

    public function testOneComparatorDesc()
    {
        $comparatorById = createComparator($this->getId, SORT_DESC);
        usort($this->data, $comparatorById);

        $ids = array_map($this->getId,$this->data);

        $this->assertTrue([6,5,4,3,2,1] === $ids);
    }

    /**
     * @return @Product
     */
    public function createTestData()
    {
        $this->data = [
            new Product(4, 'p1'),
            new Product(2, 'p1'),
            new Product(3, 'p2'),
            new Product(5, 'p1'),
            new Product(6, null),
            new Product(1, 'p2'),
        ];

        $this->sortedWithoutFlags = [6,2,4,5,1,3];
        $this->sortedNameDescIdDescNullsLast = [3,1,5,4,2,6];
    }

}