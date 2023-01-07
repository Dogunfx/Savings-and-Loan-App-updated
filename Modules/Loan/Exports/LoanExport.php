<?php

namespace Modules\Loan\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LoanExport implements FromView
{
    protected $view;

    /**
     * AccountingExport constructor.
     * @param $view
     */
    public function __construct($view)
    {
        $this->view = $view;
    }

    public function view(): View
    {
        return $this->view;
    }
}
