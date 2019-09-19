<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'page'  => 'required|integer',
            'limit' => 'required|integer',
        ];
    }

    /**
     * 获取已定义验证规则的错误消息。
     * @return array
     */
    public function messages()
    {
        return [
            'page.required'    => '页码不能为空！',
            'page.integer'     => '页码必须为数字！',
            'limit.required'   => '单页条数不能为空！',
            'limit.integer'    => '单页条数必须为数字！',
        ];
    }
}
