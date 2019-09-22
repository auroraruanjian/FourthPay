<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientCreateRequest extends FormRequest
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
            'account'=> 'required|alpha_num',
            'status' => 'required|boolean',
        ];
    }

    /**
     * 获取已定义验证规则的错误消息。
     * @return array
     */
    public function messages()
    {
        return [
            'account.required'  => '商户名称不能为空！',
            'account.integer'   => '商户名称必须为数字！',
            'status.required'   => '状态不能为空！',
            'status.boolean'    => '状态不正确！',
        ];
    }
}
