<?php

namespace Modules\Loan\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Loan\Entities\LoanNote;

class LoanNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:loan.loans.notes.index'])->only(['index','show']);
        $this->middleware(['permission:loan.loans.notes.create'])->only(['create','store']);
        $this->middleware(['permission:loan.loans.notes.edit'])->only(['edit','update']);
        $this->middleware(['permission:loan.loans.notes.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return theme_view('loan::loan_note.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create($id)
    {
        return theme_view('loan::loan_note.create', compact('id'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'description' => ['required'],
        ]);
        $loan_note = new LoanNote();
        $loan_note->created_by_id = Auth::id();
        $loan_note->loan_id = $id;
        $loan_note->description = $request->description;
        $loan_note->save();
        activity()->on($loan_note)
            ->withProperties(['id' => $loan_note->id])
            ->log('Create Loan Note');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $id . '/show');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $loan_note = LoanNote::find($id);
        return theme_view('loan::loan_note.show',compact('loan_note'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $loan_note = LoanNote::find($id);
        return theme_view('loan::loan_note.edit',compact('loan_note'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => ['required'],
        ]);
        $loan_note = LoanNote::find($id);
        $loan_note->description = $request->description;
        $loan_note->save();
        activity()->on($loan_note)
            ->withProperties(['id' => $loan_note->id])
            ->log('Update Loan Note');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan_note->loan_id . '/show');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $loan_note = LoanNote::find($id);
        $loan_note->delete();
        activity()->on($loan_note)
            ->withProperties(['id' => $loan_note->id])
            ->log('Delete Loan Note');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
