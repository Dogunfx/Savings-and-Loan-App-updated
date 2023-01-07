<?php

use Illuminate\Contracts\View\Factory;

/**
 * Created by PhpStorm.
 * User: tj
 * Date: 7/6/19
 * Time: 4:28 PM
 */
function build_html_form_field($value)
{
    if (empty($value->id)) {
        $field_name = 'field_' . uniqid();
    } else {
        $field_name = 'field_' . $value->id;
    }
    $html = '';
    $html .= "<label class='control-label' for='$field_name'>$value->name</label>";
    if ($value->required == 1) {
        $required = " required";
    } else {
        $required = "";
    }

    if ($value->type == 'text') {
        $html .= "<input type='text' name='$field_name' id='$field_name' class='form-control $value->class' value='$value->setting_value' $required/>";
    }
    if ($value->type == 'number') {
        $html .= "<input type='number' name='$field_name' id='$field_name' class='form-control $value->class' value='$value->setting_value' $required/>";
    }
    if ($value->type == 'info') {
        $html .= "<input type='text' name='$field_name' id='$field_name' class='form-control $value->class' value='$value->setting_value' disabled/>";
    }
    if ($value->type == 'textarea') {
        $html .= "<textarea name='$field_name' id='$field_name' class='form-control $value->class' $required>$value->setting_value</textarea>";
    }
    if ($value->type == 'date') {
        $html .= "<input type='text' name='$field_name' id='$field_name' class='form-control date-picker $value->class' value='$value->setting_value' $required/>";
    }
    if ($value->type == 'file') {
        $html .= "<input type='file' name='$field_name' id='$field_name' class='form-control $value->class' data-allowed='$value->options' data-path='$value->setting_value' $required/>";
    }
    if ($value->type == 'select' || $value->type == 'select_multiple') {
        $html .= "<select name='$field_name' id='$field_name' class='form-control $value->class' $required>";
        $html .= "<option value=''></option>";
        foreach (explode(',', $value->options) as $key) {
            if ($value->type == 'select') {
                if ($value->setting_value == $key) {
                    $selected = " selected";
                } else {
                    $selected = "";
                }
            }
            if ($value->type == 'select_multiple') {
                if (in_array(explode(',', $value->setting_value), $key)) {
                    $selected = " selected";
                } else {
                    $selected = "";
                }
            }
            $html .= "<option value='$key' $selected>$key</option>";
        }
        $html .= "</select>";
    }
    if ($value->type == 'select_db' || $value->type == 'select_db_multiple') {
        $html .= "<select name='$field_name' id='$field_name' class='form-control $value->class' $required>";
        $html .= "<option value=''></option>";
        $records = \Illuminate\Support\Facades\DB::table($value->options)->get();
        foreach ($records as $key) {
            list($value_field, $display_field) = explode(',', $value->db_columns);
            if ($value->type == 'select_db') {
                if ($value->setting_value == $key->$value_field) {
                    $selected = " selected";
                } else {
                    $selected = "";
                }
            }
            if ($value->type == 'select_db_multiple') {
                if (in_array(explode(',', $value->setting_value), $key->$value_field)) {
                    $selected = " selected";
                } else {
                    $selected = '';
                }
            }
            $html .= "<option value='" . $key->$value_field . "' $selected>" . $key->$display_field . "</option>";
        }
        $html .= "</select>";
    }

    return $html;
}

if (!function_exists('theme_view')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param string|null $view
     * @param \Illuminate\Contracts\Support\Arrayable|array $data
     * @param array $mergeData
     * @return \Illuminate\Contracts\View\View|Factory
     */
    function theme_view($view = null, $data = [], $mergeData = [])
    {
        $factory = app(Factory::class);

        if (func_num_args() === 0) {
            return $factory;
        }
        $active_theme = strtolower(config('active_theme'));
        if ($factory->exists(str_replace("::", "::themes.$active_theme.", $view))) {
            $view = str_replace("::", "::themes.$active_theme.", $view);
        }
        return $factory->make($view, $data, $mergeData);
    }
}
if (!function_exists('theme_view_file')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param string|null $view
     * @param \Illuminate\Contracts\Support\Arrayable|array $data
     * @param array $mergeData
     * @return \Illuminate\Contracts\View\View|Factory
     */
    function theme_view_file($view = null)
    {
        $factory = app(Factory::class);
        $active_theme = strtolower(config('active_theme'));
        if ($factory->exists(str_replace("::", "::themes.$active_theme.", $view))) {
            $view = str_replace("::", "::themes.$active_theme.", $view);
        }
        return $view;
    }
}
if (!function_exists('table_order_link')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param string $column
     * @return string
     */
    function table_order_link($column)
    {
        $link = request()->fullUrlWithQuery(['order_by' => $column, 'order_by_dir' => (request('order_by_dir') === 'asc') ? 'desc' : 'asc']);
        return $link;
    }
}
if (!function_exists('generate_reference')) {
    function generate_reference($setting_key = '', $id = '')
    {
        $prefix = '';
        if ($setting_key) {
            if ($setting = Setting::where('setting_key', $setting_key)->first()) {
                $prefix = $setting->setting_value;
            }
        }
        if (strlen($id) < 2) {
            $sequence_number = '00' . $id;
        } elseif (strlen($id) < 3) {
            $sequence_number = '0' . $id;
        } else {
            $sequence_number = $id;
        }
        $random_number = uniqid();
        $reference_format = Setting::where('setting_key', 'core.reference_format')->first()->setting_value;
        if ($reference_format == 'Sequence Number') {
            return $prefix . $sequence_number;
        } elseif ($reference_format == 'Random Number') {
            return $prefix . $random_number;
        } elseif ($reference_format == 'YEAR/Sequence Number (SL/2014/001)') {
            return $prefix . date("Y") . '/' . $sequence_number;
        } elseif ($reference_format == 'YEAR/MONTH/Sequence Number (SL/2014/08/001)') {
            return $prefix . date("Y") . '/' . date("m") . '/' . $sequence_number;
        } else {
            return $id;
        }
    }
}