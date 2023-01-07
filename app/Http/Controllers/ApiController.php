<?php
/**
 * Created by PhpStorm.
 * User: Tj
 * Date: 28/1/2019
 * Time: 19:35
 */

namespace App\Http\Controllers;


use App\Models\Borrower;
use App\Models\Branch;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function formcraft(Request $request)
    {

        $loan_amount = $request->Choisissez_un_m;
        $frequency_of_payment = $request->Fréquence_de_paiement;
        $first_name = $request->Prénom;
        $last_name = $request->Nom_de_famille;
        $dob = $request->Date_de_naissance;
        $phone = $request->Téléphone;
        $mobile = $request->Cellulaire;
        $email = $request->Courriel;
        $gender = $request->Genre;
        $address = $request->Addresse;
        $city = $request->Ville;
        $province = $request->Province;
        $zip = $request->Code_postal;

        $borrower = new Borrower();
        $borrower->first_name = $first_name;
        $borrower->last_name = $last_name;
        if ($gender == "Homme") {
            $borrower->gender = "Male";
        }
        if ($gender == "Femme") {
            $borrower->gender = "Female";
        }
        if (!empty($dob)) {
            $temp = explode("/", $dob);
            $dob = $temp[2] . '-' . $temp[1] . '-' . $temp[0];
            $borrower->dob = $dob;
        }
        $borrower->branch_id = Branch::where('default_branch', 1)->first()->id;
        $borrower->mobile = $mobile;
        $borrower->email = $email;
        $borrower->address = $address;
        $borrower->city = $city;
        $borrower->state = $province;
        $borrower->zip = $zip;
        $borrower->phone = $phone;
        $borrower->loan_officers = serialize([]);
        $date = explode('-', date("Y-m-d"));
        $borrower->year = $date[0];
        $borrower->month = $date[1];
        $borrower->save();

        //try to create loan application
    }
}