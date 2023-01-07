<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Helpers\RouteSms;
use App\Helpers\Infobip;
use App\Models\Borrower;
use App\Models\Email;
use App\Models\Setting;
use App\Models\Sms;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Clickatell\Rest;
use Illuminate\Http\Request;
use Aloha\Twilio\Twilio;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Yajra\DataTables\Facades\DataTables;

class CommunicationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['sentinel', 'branch']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexEmail()
    {
        if (!Sentinel::hasAccess('communication')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Email::where('branch_id', session('branch_id'))->get();
        return view('communication.email', compact('data'));
    }

    public function get_emails(Request $request)
    {
        if (!Sentinel::hasAccess('communication')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $query = DB::table("emails")->leftJoin("users", "users.id", "emails.user_id")->selectRaw(DB::raw('emails.*, users.first_name first_name,users.last_name last_name'));
        return DataTables::of($query)->editColumn('user', function ($data) {
            return $data->first_name . ' ' . $data->last_name;
        })->editColumn('action', function ($data) {
            $action = '<ul class="icons-list"><li class="dropdown">  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu9"></i></a> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Sentinel::hasAccess('communication.delete')) {
                $action .= '<li><a href="' . url('communication/email/' . $data->id . '/delete') . '" class="delete">' . trans_choice('general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></ul>";
            return $action;
        })->rawColumns(['id', 'user', 'action'])->make(true);
    }

    public function indexSms()
    {
        if (!Sentinel::hasAccess('communication')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Sms::where('branch_id', session('branch_id'))->get();
        return view('communication.sms', compact('data'));
    }

    public function get_sms(Request $request)
    {
        if (!Sentinel::hasAccess('communication')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $query = DB::table("sms")->leftJoin("users", "users.id", "sms.user_id")->selectRaw(DB::raw('sms.*, users.first_name first_name,users.last_name last_name'));
        return DataTables::of($query)->editColumn('user', function ($data) {
            return $data->first_name . ' ' . $data->last_name;
        })->editColumn('action', function ($data) {
            $action = '<ul class="icons-list"><li class="dropdown">  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu9"></i></a> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Sentinel::hasAccess('communication.delete')) {
                $action .= '<li><a href="' . url('communication/email/' . $data->id . '/delete') . '" class="delete">' . trans_choice('general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></ul>";
            return $action;
        })->rawColumns(['id', 'user', 'action'])->make(true);
    }

    public function createEmail(Request $request)
    {
        if (!Sentinel::hasAccess('communication.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $borrowers = array();
        $borrowers["0"] = "All Borrowers";
        foreach (Borrower::all() as $key) {
            $borrowers[$key->id] = $key->first_name . ' ' . $key->last_name . ' (' . $key->unique_number . ')';
        }
        if (isset($request->borrower_id)) {
            $selected = $request->borrower_id;
        } else {
            $selected = '';
        }
        return view('communication.create_email', compact('borrowers', 'selected'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeEmail(Request $request)
    {
        if (!Sentinel::hasAccess('communication.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $body = "";
        $recipients = 1;
        if ($request->send_to == 0) {
            foreach (Borrower::all() as $borrower) {
                $body = $request->message;
//lets build and replace available tags
                $body = str_replace('{borrowerTitle}', $borrower->title, $body);
                $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
                $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
                $body = str_replace('{borrowerAddress}', $borrower->address, $body);
                $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
                $body = str_replace('{borrowerEmail}', $borrower->email, $body);
                $body = str_replace('{borrowerTotalLoansDue}',
                    round(GeneralHelper::borrower_loans_total_due($borrower->id), 2), $body);
                $body = str_replace('{borrowerTotalLoansBalance}',
                    round((GeneralHelper::borrower_loans_total_due($borrower->id) - GeneralHelper::borrower_loans_total_paid($borrower->id)),
                        2), $body);
                $body = str_replace('{borrowerTotalLoansPaid}', GeneralHelper::borrower_loans_total_paid($borrower->id),
                    $body);
                $email = $borrower->email;
                if (!empty($email)) {
                    Mail::send([], [], function ($message) use ($body,$request, $email) {
                        $message->to($email)
                            ->subject($request->subject)
                            ->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                                Setting::where('setting_key', 'company_name')->first()->setting_value)
                            ->setBody($body, 'text/html');
                    });

                }
                $recipients = $recipients + 1;
            }
            $mail = new Email();
            $mail->user_id = Sentinel::getUser()->id;
            $mail->message = $body;
            $mail->subject = $request->subject;
            $mail->branch_id = session('branch_id');;
            $mail->recipients = $recipients;
            $mail->send_to = 'All Borrowers';
            $mail->save();
            GeneralHelper::audit_trail("Send  email to all borrowers");
            Flash::success("Email successfully sent");
            return redirect('communication/email');
        } else {
            $body = $request->message;
            $borrower = Borrower::find($request->send_to);
            //lets build and replace available tags
            $body = str_replace('{borrowerTitle}', $borrower->title, $body);
            $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
            $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
            $body = str_replace('{borrowerAddress}', $borrower->address, $body);
            $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
            $body = str_replace('{borrowerEmail}', $borrower->email, $body);
            $body = str_replace('{borrowerTotalLoansDue}',
                round(GeneralHelper::borrower_loans_total_due($borrower->id), 2), $body);
            $body = str_replace('{borrowerTotalLoansBalance}',
                round((GeneralHelper::borrower_loans_total_due($borrower->id) - GeneralHelper::borrower_loans_total_paid($borrower->id)),
                    2), $body);
            $body = str_replace('{borrowerTotalLoansPaid}', GeneralHelper::borrower_loans_total_paid($borrower->id),
                $body);
            $email = $borrower->email;
            if (!empty($email)) {
                Mail::send([], [], function ($message) use ($body,$request, $email) {
                    $message->to($email)
                        ->subject($request->subject)
                        ->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                            Setting::where('setting_key', 'company_name')->first()->setting_value)
                        ->setBody($body, 'text/html');
                });
                $mail = new Email();
                $mail->user_id = Sentinel::getUser()->id;
                $mail->message = $body;
                $mail->subject = $request->subject;
                $mail->branch_id = session('branch_id');;
                $mail->recipients = $recipients;
                $mail->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
                $mail->save();
                GeneralHelper::audit_trail("Sent email to borrower ");
                Flash::success("Email successfully sent");
                return redirect('communication/email');
            }

        }
        Flash::success("Email successfully sent");
        return redirect('communication/email');
    }


    public function deleteEmail($id)
    {
        if (!Sentinel::hasAccess('communication.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        Email::destroy($id);
        GeneralHelper::audit_trail("Deleted email record with id:" . $id);
        Flash::success("Email successfully deleted");
        return redirect('communication/email');
    }

    public function createSms(Request $request)
    {
        if (!Sentinel::hasAccess('communication.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $borrowers = array();
        $borrowers["0"] = "All Borrowers";
        foreach (Borrower::all() as $key) {
            $borrowers[$key->id] = $key->first_name . ' ' . $key->last_name . ' (' . $key->unique_number . ')';
        }
        if (isset($request->borrower_id)) {
            $selected = $request->borrower_id;
        } else {
            $selected = '';
        }
        return view('communication.create_sms', compact('borrowers', 'selected'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeSms(Request $request)
    {
        if (!Sentinel::hasAccess('communication.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $body = "";
        $recipients = 1;
        if (Setting::where('setting_key', 'sms_enabled')->first()->setting_value == 1) {
            if ($request->send_to == 0) {

                $active_sms = Setting::where('setting_key', 'active_sms')->first()->setting_value;
                foreach (Borrower::all() as $borrower) {
                    $body = $request->message;
//lets build and replace available tags
                    $body = str_replace('{borrowerTitle}', $borrower->title, $body);
                    $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
                    $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
                    $body = str_replace('{borrowerAddress}', $borrower->address, $body);
                    $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
                    $body = str_replace('{borrowerEmail}', $borrower->email, $body);
                    $body = str_replace('{borrowerTotalLoansDue}',
                        round(GeneralHelper::borrower_loans_total_due($borrower->id), 2), $body);
                    $body = str_replace('{borrowerTotalLoansBalance}',
                        round((GeneralHelper::borrower_loans_total_due($borrower->id) - GeneralHelper::borrower_loans_total_paid($borrower->id)),
                            2), $body);
                    $body = str_replace('{borrowerTotalLoansPaid}',
                        GeneralHelper::borrower_loans_total_paid($borrower->id),
                        $body);
                    $email = $borrower->email;
                    $body = trim(strip_tags($body));
                    if (!empty($borrower->mobile)) {
                        GeneralHelper::send_sms($borrower->mobile, $body);
                    }
                    $recipients = $recipients + 1;
                }
                $sms = new Sms();
                $sms->user_id = Sentinel::getUser()->id;
                $sms->message = $body;
                $sms->gateway = $active_sms;
                $sms->branch_id = session('branch_id');;
                $sms->recipients = $recipients;
                $sms->send_to = 'All borrowers';
                $sms->save();
                GeneralHelper::audit_trail("Sent SMS   to all borrower");
                Flash::success("SMS successfully sent");
                return redirect('communication/sms');
            } else {
                $body = $request->message;
                $borrower = Borrower::find($request->send_to);
                //lets build and replace available tags
                $body = str_replace('{borrowerTitle}', $borrower->title, $body);
                $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
                $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
                $body = str_replace('{borrowerAddress}', $borrower->address, $body);
                $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
                $body = str_replace('{borrowerEmail}', $borrower->email, $body);
                $body = str_replace('{borrowerTotalLoansDue}',
                    round(GeneralHelper::borrower_loans_total_due($borrower->id), 2), $body);
                $body = str_replace('{borrowerTotalLoansBalance}',
                    round((GeneralHelper::borrower_loans_total_due($borrower->id) - GeneralHelper::borrower_loans_total_paid($borrower->id)),
                        2), $body);
                $body = str_replace('{borrowerTotalLoansPaid}', GeneralHelper::borrower_loans_total_paid($borrower->id),
                    $body);
                $body = trim(strip_tags($body));
                if (!empty($borrower->mobile)) {
                    $active_sms = Setting::where('setting_key', 'active_sms')->first()->setting_value;
                    GeneralHelper::send_sms($borrower->mobile, $body);
                    $sms = new Sms();
                    $sms->user_id = Sentinel::getUser()->id;
                    $sms->message = $body;
                    $sms->gateway = $active_sms;
                    $sms->recipients = $recipients;
                    $sms->branch_id = session('branch_id');;
                    $sms->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
                    $sms->save();
                    Flash::success("SMS successfully sent");
                    return redirect('communication/sms');
                }

            }
            GeneralHelper::audit_trail("Sent SMS   to borrower");
            Flash::success("Sms successfully sent");
            return redirect('communication/sms');
        } else {
            Flash::warning('SMS service is disabled, please go to settings and enable it');
            return redirect('setting/data')->with(array('error' => 'SMS is disabled, please enable it.'));
        }
    }


    public function deleteSms($id)
    {
        if (!Sentinel::hasAccess('communication.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        Sms::destroy($id);
        GeneralHelper::audit_trail("Deleted sms record with id:" . $id);
        Flash::success("SMS successfully deleted");
        return redirect('communication/sms');
    }

}
