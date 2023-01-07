<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 9/18/19
 * Time: 11:22 AM
 */

use \Modules\CustomField\Entities\CustomField;
use \Modules\CustomField\Entities\CustomFieldMeta;

if (!function_exists('custom_field_build_form_field')) {
    function custom_field_build_form_field($value, $id = '')
    {
        $current = '';
        if ($id) {
            $meta = CustomFieldMeta::where('category', $value->category)->where('parent_id', $id)->where('custom_field_id', $value->id)->first();
            if (!empty($meta)) {
                $current = $meta->value;
            }
        }

        $data = [];
        $data["current"] = $current;
        if ($value->label) {
            $data["label"] = $value->label;
        } else {
            $data["name"] = $value->name;
        }
        $html = '';
        $html .= "";
        if ($value->required == 1) {
            $required = " required";
        } else {
            $required = "";
        }
        if ($value->type == 'textfield') {
            $html .= "<input type='text' name='field_$value->id' id='field_$value->id' class='form-control $value->class' value='$current' $required/>";
        }
        if ($value->type == 'text') {
            $html .= "<input type='text' name='field_$value->id' id='field_$value->id' class='form-control $value->class' value='$current' $required/>";
        }
        if ($value->type == 'number') {
            $html .= "<input type='number' name='field_$value->id' id='field_$value->id' class='form-control numeric $value->class' value='$current' $required/>";
        }
        if ($value->type == 'info') {
            $html .= "<input type='text' name='field_$value->id' id='field_$value->id' class='form-control $value->class' value='$current' disabled/>";
        }
        if ($value->type == 'textarea') {
            $html .= "<textarea name='field_$value->id' id='field_$value->id' class='form-control $value->class' $required>$current</textarea>";
        }
        if ($value->type == 'date') {
            $html .= "<input type='text' name='field_$value->id' id='field_$value->id' class='form-control date-picker $value->class' value='$current' $required/>";
        }
        if ($value->type == 'file') {
            $html .= "<input type='file' name='field_$value->id' id='field_$value->id' class='form-control $value->class' data-allowed='$value->options' data-path='$current' $required/>";
        }
        if ($value->type == 'radio') {
            foreach (explode(',', $value->options) as $key) {
                if ($current == $key) {
                    $selected = " checked";
                } else {
                    $selected = "";
                }
                $html .= "<label><input type='radio' name='field_$value->id' value='$key' class='$value->class' $selected />" . $key . " </label>";
            }
        }
        if ($value->type == 'checkbox') {
            foreach (explode(',', $value->options) as $key) {
                if ($current && in_array($key, explode(',', $current))) {
                    $selected = " checked";
                } else {
                    $selected = "";
                }
                $html .= "<label><input type='checkbox' name='field_$value->id[]' value='$key' class='$value->class' $selected/>" . $key . " </label>";
            }
        }
        if ($value->type == 'select' || $value->type == 'select_multiple') {
            $html .= "<select name='field_$value->id' id='field_$value->id' class='form-control $value->class' $required>";
            $html .= "<option value=''></option>";
            foreach (explode(',', $value->options) as $key) {
                if ($value->type == 'select') {
                    if ($current == $key) {
                        $selected = " selected";
                    } else {
                        $selected = "";
                    }
                }
                if ($value->type == 'select_multiple') {
                    if ($current && in_array($key, explode(',', $current))) {
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
            $html .= "<select name='field_$value->id' id='field_$value->id' class='form-control $value->class' $required>";
            $html .= "<option value=''></option>";
            $records = \Illuminate\Support\Facades\DB::table($value->options)->get();
            foreach ($records as $key) {
                list($value_field, $display_field) = explode(',', $value->db_columns);
                if ($value->type == 'select_db') {
                    if ($current == $key->$value_field) {
                        $selected = " selected";
                    } else {
                        $selected = "";
                    }
                }
                if ($value->type == 'select_db_multiple') {
                    if ($current && in_array($key->$value_field, explode(',', $current))) {
                        $selected = " selected";
                    } else {
                        $selected = '';
                    }
                }
                $html .= "<option value='" . $key->$value_field . "' $selected>" . $key->$display_field . "</option>";
            }
            $html .= "</select>";
        }
        $data['html'] = $html;
        return $data;
    }
}
if (!function_exists('custom_fields_save_form')) {
    /**
     * @param $category String
     * @param $request \Illuminate\Http\Request
     * @param $id
     */
    function custom_fields_save_form($category, $request, $id)
    {
        foreach (CustomField::where('category', $category)->where('active', 1)->get() as $custom_field) {
            $name = 'field_' . $custom_field->id;
            if (is_array($request->$name)) {
                $value = implode(',', $request->$name);
            } else {
                $value = $request->$name;
            }
            $field = CustomFieldMeta::updateOrCreate(
                ['category' => $category, 'parent_id' => $id, 'custom_field_id' => $custom_field->id],
                [
                    'category' => $category,
                    'parent_id' => $id,
                    'custom_field_id' => $custom_field->id,
                    'value' => $value
                ]
            )->save();
        }
    }
}
if (!function_exists('custom_fields_build_data_for_json')) {
    /**
     * @param $category String
     * @param $request \Illuminate\Http\Request
     * @param $id
     */
    function custom_fields_build_data_for_json($custom_fields, $data)
    {
        foreach ($custom_fields as &$custom_field) {
            $meta = CustomFieldMeta::where('category', $custom_field->category)->where('parent_id', $data->id)->where('custom_field_id', $custom_field->id)->first();
            if (!empty($meta)) {
                $custom_field->value = $meta->value;
            } else {
                $custom_field->value = '';
            }
        }
        return $custom_fields;
    }
}