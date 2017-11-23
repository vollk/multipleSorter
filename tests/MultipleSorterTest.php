<?php
/**
 * Date: 23.11.2017
 * Time: 16:34
 */

use function multipleSorter\createSorter;
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
        $sorterByName = createSorter($this->getName);
        $sorterById = createSorter($this->getId);

        $sortAggregate = aggregate($sorterByName, $sorterById);

        usort($this->data, $sortAggregate);

        $ids = array_map($this->getId,$this->data);

        $this->assertTrue($this->sortedWithoutFlags === $ids);
    }

    public function testNameDescIdDescNullsLast()
    {
        $sorterByName = createSorter($this->getName, SORT_DESC, NULLS_LAST);
        $sorterById = createSorter($this->getId, SORT_DESC);

        $sortAggregate = aggregate($sorterByName, $sorterById);

        usort($this->data, $sortAggregate);

        $ids = array_map($this->getId,$this->data);

        $this->assertTrue($this->sortedNameDescIdDescNullsLast === $ids);
    }

    public function testOneSorter()
    {
        $sorterById = createSorter($this->getId);
        usort($this->data, $sorterById);

        $ids = array_map($this->getId,$this->data);

        $this->assertTrue([1,2,3,4,5,6] === $ids);
    }

    public function testOneSorterDesc()
    {
        $sorterById = createSorter($this->getId, SORT_DESC);
        usort($this->data, $sorterById);

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