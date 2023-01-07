<?php

namespace Modules\Savings\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SavingsExport implements FromView
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
