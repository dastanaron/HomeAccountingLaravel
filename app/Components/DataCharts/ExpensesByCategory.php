<?php

namespace App\Components\DataCharts;

use \App\Funds;
use \App\revCategories;

class ExpensesByCategory
{

    /**
     * @var string
     */
    public $userId;

    /**
     * @var string
     */
    public $dateStart;

    /**
     * @var string
     */
    public $dateEnd;

    /**
     * @var int
     */
    public $fundsRev;

    /**
     * ExpensesByCategory constructor.
     * @param $userId
     * @param $dateStart
     * @param $dateEnd
     */
    public function __construct($userId, $dateStart, $dateEnd, $fundsRev=2)
    {
        $this->userId = $userId;
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->fundsRev = $fundsRev;
    }

    /**
     * @param $userId
     * @param $dateStart
     * @param $dateEnd
     * @return ExpensesByCategory
     */
    public static function init($userId, $dateStart, $dateEnd)
    {
        return new self($userId, $dateStart, $dateEnd);
    }

    /**
     * @return Funds|\Illuminate\Database\Query\Builder
     */
    public function queryFunds()
    {
       return Funds::select('funds.user_id', 'funds.sum', 'funds.date', 'funds.cause', 'rev_categories.id as category_id', 'rev_categories.name as category_name')
            ->leftJoin('rev_categories', 'funds.category_id', 'rev_categories.id')
            ->where('funds.user_id', '=', $this->userId)
            ->where('funds.rev', '=', $this->fundsRev)
            ->whereBetween('funds.date', [$this->dateStart, $this->dateEnd]);
    }

    public function queryCategories()
    {
        return revCategories::select('id', 'user_id', 'name')->where('user_id', '=', $this->userId);
    }

    /**
     * @return Funds[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getGroupedByDate()
    {
        return $this->queryFunds()->get()->groupBy('date')->toArray();
    }

    /**
     * @return array
     */
    public function groupByCategory()
    {
        $data = $this->getGroupedByDate();

        $array = array();

        foreach($data as $date => $element) {

            foreach($element as $item) {
                $array[$date][$item['category_id']][] = $item;
            }
        }

        return $array;
    }

    /**
     * @return array
     */
    public function categorySum()
    {
        $fundsData = $this->groupByCategory();

        $newArray = array();


        foreach($fundsData as $date => $itemsCategory) {

            foreach($itemsCategory as $categoryID=>$items) {

                $newArray[$date][$categoryID]['sum'] = 0;
                $newArray[$date][$categoryID]['name'] = $items[0]['category_name'];

                foreach($items as $item) {

                    $newArray[$date][$categoryID]['sum'] += $item['sum'];

                }

            }

        }

        return $newArray;

    }

    public function addSumToOtherCategories()
    {
        $categories = $this->queryCategories()->get()->toArray();
        $data = $this->categorySum();

        $newArray = array();
        $tmpCategories = array();

        foreach($categories as $category) {

            $tmpCategories[$category['id']]['sum'] = 0;
            $tmpCategories[$category['id']]['name'] = $category['name'];
        }

        foreach($data as $date=>$categorySum) {

            $newArray[$date] = array_replace($tmpCategories, $categorySum);

        }

        return $newArray;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->addSumToOtherCategories();
    }

    /**
     * @return string
     */
    public function getJson()
    {
        return json_encode($this->getData());
    }

    public function getJsonByChart()
    {
        $data = $this->getData();

        $xArray = array();

        $string = 'x, ';

        foreach($data as $date => $items) {
            $xArray['x'][] = $date;
        }

        $string .= implode(', ', $xArray['x']);

        $array = array();

        $x = explode(', ', $string);

        $categoryData = $this->buildRowsToChart();

        //$array = array_merge($array, $x,  $categoryData);

        $array[] = $x;

        foreach ($categoryData as $item) {
            $array[] = $item;
        }

        return json_encode($array);

    }

    public function buildRowsToChartData()
    {
        $data = $this->getData();

        $categories = $this->queryCategories()->get()->toArray();


        $array = array();

        $i = 0;

        foreach($categories as $category) {

            $array[$i][$category['name']] = array();

            foreach($data as $date => $items) {

                foreach($items as $categoryId => $item) {
                    if($categoryId === $category['id']) {
                        $array[$i][$category['name']][] = $item['sum'];
                    }
                }

            }

            $i++;
        }

        return $array;
    }

    public function buildRowsToChart()
    {
        $data = $this->buildRowsToChartData();

        $array = array();

        $string = '';

        foreach($data as $elements) {

            foreach($elements as $key => $value) {

                $string = $key . ', ' . implode(', ', $value);

            }

            $array[] = $string;

        }

        $newArray = array();

        foreach($array as $item) {

            $newArray[] = explode(', ', $item);

        }

        return $newArray;

    }


}