<?php

namespace Modules\Client\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ClientExport implements FromView
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
