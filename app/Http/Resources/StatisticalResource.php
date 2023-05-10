<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StatisticalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'sales_money_in_month' => $this['salesMoneyInMonth'], //tiền lãi trong tháng
            'sales_money_in_yesterday' => $this['salesMoneyInYesterday'], //tiền lãi trong ngày hôm qua 
            'sales_money_in_now' => $this['salesMoneyInNow'], //tiền lãi trong ngày 
            'sales_money_in_day_of_week' => $this['salesMoneyInDayOfWeek'], //tiền lãi trong tuần

            'funds' => $this['funds'], // quỹ vốn nhập hàng
            'product_totail' => $this['productTotail'], // tổng sản phẩm
            'best_selling_products' => $this['bestSellingProducts'], // sản phẩm bán chạy nhất
            'most_profitable_products' => $this['mostProfitableProducts'], // sản phẩm có lợi nhuận cao nhất

            
        ];
    }
}
