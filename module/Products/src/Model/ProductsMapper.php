<?php

namespace Products\Model;

class ProductsMapper
{
    private const FILTER_OPERATION = [
        "category" => "==",
        "priceLessThan" => "<="
    ];

    private const FILTER_FIELD = [
        "category" => "category",
        "priceLessThan" => "price"
    ];
    private const FILTER_TYPE = [
        "category" => "string",
        "priceLessThan" => "int"
    ];
    private const DISCOUNT = [
        "category" => [["discount" => 30, "matchitem" => "boots"]],
        "sku" => [["discount" => 15, "matchitem" => 000003]],
    ];

    public function __construct($config)
    {
        $this->products = new Products($config);
    }

    public function getProducts($filter)
    {
        $productData = $this->products->loadData();
        $productData = $productData["products"];
        $filteredData = $productData;
        if (count($filter) > 0) {
            $filteredData = $this->filter($productData, $filter);
        }
        $filteredData = $this->addPriceContent($filteredData);
        $filteredData = $this->calcDiscount($filteredData);
        if (empty($filteredData)) {
            throw new \Exception("No matching products found");
        }
        return array_slice($filteredData, 0, 5);
    }

    protected function calcDiscount($filteredData)
    {
        foreach (self::DISCOUNT as $fieldKey => $fieldDiscounts) {
            foreach ($fieldDiscounts as $fieldDiscount) {
                $productsToDiscounts = array_keys(array_column($filteredData, $fieldKey), $fieldDiscount['matchitem']);
                foreach ($productsToDiscounts as $productsToDiscounts) {
                    $discountInt = !empty($filteredData[$productsToDiscounts]['price']['discount_percentage'])
                        ? str_replace("%", '', $filteredData[$productsToDiscounts]['price']['discount_percentage'])
                        : $fieldDiscount['discount'];
                    $discountInt = $fieldDiscount['discount'] > $discountInt ? $fieldDiscount['discount'] : $discountInt;
                    $filteredData[$productsToDiscounts]['price']['final'] -= $filteredData[$productsToDiscounts]['price']['original'] * ($discountInt / 100);
                    $filteredData[$productsToDiscounts]['price']['discount_percentage'] = sprintf("%s%%", $discountInt);
                }
            }
        }

        return $filteredData;
    }

    protected function addPriceContent($filteredData)
    {
        for ($i = 0; $i < count($filteredData); $i++) {
            $price = str_replace(".", '', $filteredData[$i]['price']);
            $filteredData[$i]['price'] = [
                'original' => $price,
                'final' => $price,
                'discount_percentage' => null,
                'currency' => "EUR"
            ];
        }

        return $filteredData;
    }

    protected function filter($data, $filter)
    {
        $filtered = [];
        $field = self::FILTER_FIELD;
        $operation = self::FILTER_OPERATION;
        $type = self::FILTER_TYPE;

        foreach ($filter as $filterKey => $filterValue) {
            if (in_array($filterKey, array_keys($field))) {
                $filtered = array_filter(
                    $data,
                    function ($value) use ($filterKey, $filterValue, $field, $operation, $type) {
                        $keyInner = $field[$filterKey];
                        $val1 = $value[$keyInner];
                        $val2 = $filterValue;
                        if ($type[$filterKey] == 'string') {
                            $val1 = sprintf('"%s"', $value[$keyInner]);
                            $val2 = sprintf('"%s"', $filterValue);
                        }

                        return eval("return $val1 $operation[$filterKey] $val2;");
                    }
                );
            }
            $data = array_values($filtered);
        }

        return $data;
    }
}
