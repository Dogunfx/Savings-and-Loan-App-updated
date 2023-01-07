<?php
/**
 * Created by PhpStorm.
 * User: Tj
 * Date: 30/11/2018
 * Time: 09:43
 */

namespace App\Http\Controllers;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index()
    {
        foreach (DB::table("savings_charges")->join('charges', 'charges.id', 'savings_charges.charge_id')->join("savings", 'savings.id', 'savings_charges.savings_id')->leftJoin("savings_products", 'savings_products.id', 'savings.savings_product_id')->selectRaw("savings_charges.amount,savings.*,charges.charge_option,charges.charge_type,savings_products.chart_reference_id,savings_products.accounting_rule,savings_products.chart_expense_interest_id")->whereIn("charges.charge_type", ['annual_fee', 'monthly_fee'])->get() as $savings) {

        }
        echo 'r';
    }
}