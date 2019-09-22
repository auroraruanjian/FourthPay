<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminCreateRequest extends FormRequest
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
            'username'  => 'required|alpha_num|between:3,20',
            'nickname'  => 'required|alpha_num|between:2,20',
            'password'  => ($this->path()!='admin/edit'?'required|':'').'alpha_dash|between:8,40',
            'is_locked' => 'required|boolean',
            'role'      => 'array'
        ];
    }

    /**
     * 获取已定义验证规则的错误消息。
     * @return array
     */
    public function messages()
    {
        $messages = [
            'username.required'     => '用户名不能为空！',
            'username.alpha_num'    => '用户名为格式为数字字母组合',
            'username.between'      => '用户名长度在3-20位',
            'nickname.required'     => '昵称不能为空',
            'nickname.alpha_num'    => '昵称为字母数字组合',
            'nickname.between'      => '昵称长度在2-20位',
            'password.alpha_dash'   => '密码为字母、数字、-、_ 组合',
            'password.between'      => '密码长度在8-20位',
            'is_locked.required'    => '状态不能为空',
            'is_locked.boolean'     => '状态只能为true或false',
            'role.array'            => '角色格式错误！',
        ];

        if( $this->path() != 'admin/edit' ){
            $messages['password.required']  = '密码不能为空';
        }

        return $messages;
    }
}
