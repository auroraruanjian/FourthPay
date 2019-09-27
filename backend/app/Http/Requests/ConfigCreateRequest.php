<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigCreateRequest extends FormRequest
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
            //|alpha_num|between:3,20
            'parent_id'     => ($this->path()=='config/edit'?'required|':'').'|integer',
            'title'         => 'required|alpha_dash|between:1-100',
            'key'           => 'required|alpha_dash|between:4-100',
            'value'         => 'alpha_dash',
            'type'          => 'required|integer',
            'extra'         => 'array',
            'description'   => 'string',
            'is_disabled'   => 'required|boolean',
        ];
    }

    /**
     * 获取已定义验证规则的错误消息。
     * @return array
     */
    public function messages()
    {
        $message = [
            'parent_id.integer'     => '上级ID为数字！',
            'title.required'        => '标题不能为空！',
            'title.alpha_dash'      => '标题只能为数字字母组合',
            'title.between'         => '标题长度为1-100个字符',
            'key.required'          => '配置名称不能为空！',
            'key.alpha_dash'        => '配置名称为数字字母组合！',
            'key.between'           => '配置名称长度为4-100个字符',
            'value.alpha_dash'      => '配置值不能为空',
            'type.required'         => '配置类型不能为空',
            'type.integer'          => '配置类型为数字',
            'extra.array'           => '扩展配置不正确',
            'description.string'    => '描述只能为字符',
            'is_disabled.required'  => '状态不能为空',
            'is_disabled.boolean'   => '状态不正确',
        ];

        if( $this->path() == 'config/create' ){
            $message['parent_id.required'] = '上级ID不能为空！';
        }

        return $message;
    }
}
