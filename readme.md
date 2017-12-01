# Multiple Sorter


This small library allows yot to sort an array of your compound objects or arrays in declarative functional manner.     
This is a common task for example for rows from database or some other array of objects.


## Why?

It is boring to write comparators from scratch, and probably all of them looks similar
```
// don`t forget about null values
if($a->getName() > $b->getName()) 
    return 1;
elseif($a->getName() < $b->getName())
    return -1;
else 
{
    if($a->getId() > $b->getId()) 
        return 1;
    elseif($a->getId() < $b->getId())
        return -1;
    else        
        return 0;
}
   
```
This code is not reusable. You need to write it again if you need to sort data by description and then by id for example.    

With this tool yot should define extractors  - functions, that map item from your sorted array to some value. This value
should be comparable using < and > operators. (string, number, DateTime object, etc...)  
Then you should crete comparator from your extractor functions and combine them using aggregate method

## Installation

..in progresss

## Usage

Assumed we have an array of Product class and our client wants to see them sorted by Name  and then by id on one page.  
And sorted by description and then by id in reverse order on page2. Consider that description can be null;  

```
class Product
{
  ....  

   public function getId() {return $this->id;}
   public function getName() {return $this->name;}
   public function getDesc() {return $this->desc;}
}
```
       
A we write:
```
  /**
  
  $getId = function (Product $p) {
      return $p->getId();
  };
  $getName = function (Product $p) {
      return $p->getName();
  };
  
  $getDesc = function (Product $p) {
        return $p->getDesc();
    };
    
  function sortProductsOnPage1(array & $products)
  {
    $comparatorByName = createComparator($getName);
    $comparatorById = createComparator($getId);
    
    $sortAggregate = aggregate($comparatorByName, $comparatorById);
    
    usort($products, $sortAggregate);
  }
  
  function sortProductsOnPage2(array & $products)
    {
      $comparatorByDesc = createComparator($getDesc, NULLS_LAST);
      $comparatorById = createComparator($getId, SORT_DESC);
      
      $sortAggregate = aggregate($comparatorByDesc, $comparatorById);
      usort($products, $sortAggregate);
    }
```


## License

For *this* project, I choose MIT license.
